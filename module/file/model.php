<?php
/**
 * The model file of file module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     file
 * @version     $Id: model.php 4976 2013-07-02 08:15:31Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class fileModel extends model
{
    public $savePath  = '';
    public $webPath   = '';
    public $now       = 0;

    /**
     * Construct function.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->now = time();
        $this->setSavePath();
        $this->setWebPath();
    }

    /**
     * Get info of a file.
     * 
     * @param  int    $fileID 
     * @access public
     * @return object
     */
    public function getById($fileID)
    {
        $file = $this->dao->findById($fileID)->from(TABLE_FILE)->fetch();
        $file->webPath  = $this->webPath . $file->pathname;
        $file->realPath = $this->savePath . $file->pathname;
        return $file;
    }

    /**
     * Save upload.
     * 
     * @param  string $objectType 
     * @param  string $objectID 
     * @param  string $extra 
     * @param  string $filesName
     * @param  string $labelsName
     * @access public
     * @return array
     */
    public function saveUpload($objectType = '', $objectID = '', $extra = '', $filesName = 'files', $labelsName = 'labels')
    {
        $fileTitles = array();
        $now        = helper::today();
        $files      = $this->getUpload($filesName, $labelsName);

        foreach($files as $id => $file)
        {
            if($file['size'] == 0) continue;
            if(!move_uploaded_file($file['tmpname'], $this->savePath . $file['pathname'])) return false;

            $file = $this->compressImage($file);

            $file['objectType'] = $objectType;
            $file['objectID']   = $objectID;
            $file['addedBy']    = $this->app->user->account;
            $file['addedDate']  = $now;
            $file['extra']      = $extra;
            unset($file['tmpname']);
            $this->dao->insert(TABLE_FILE)->data($file)->exec();
            $fileTitles[$this->dao->lastInsertId()] = $file['title'];
        }
        return $fileTitles;
    }

    /**
     * Get counts of uploaded files.
     * 
     * @access public
     * @return int
     */
    public function getCount()
    {
        return count($this->getUpload());
    }

    /**
     * Get info of uploaded files.
     * 
     * @param  string $htmlTagName 
     * @param  string $labelsName
     * @access public
     * @return array
     */
    public function getUpload($htmlTagName = 'files', $labelsName = 'labels')
    {
        $files = array();
        if(!isset($_FILES[$htmlTagName])) return $files;

        $this->app->loadClass('purifier', true);
        $config   = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $purifier = new HTMLPurifier($config);

        /* If the file var name is an array. */
        if(is_array($_FILES[$htmlTagName]['name']))
        {
            extract($_FILES[$htmlTagName]);
            foreach($name as $id => $filename)
            {
                if(empty($filename)) continue;
                if(!validater::checkFileName($filename)) continue;

                $title             = isset($_POST[$labelsName][$id]) ? $_POST[$labelsName][$id] : '';
                $file['extension'] = $this->getExtension($filename);
                $file['pathname']  = $this->setPathName($id, $file['extension']);
                $file['title']     = !empty($title) ? htmlspecialchars($title) : str_replace('.' . $file['extension'], '', $filename);
                $file['title']     = $purifier->purify($file['title']);
                $file['size']      = $size[$id];
                $file['tmpname']   = $tmp_name[$id];
                $files[]           = $file;
            }
        }
        else
        {
            if(empty($_FILES[$htmlTagName]['name'])) return $files;
            extract($_FILES[$htmlTagName]);
            if(!validater::checkFileName($name)) return array();;
            $title             = isset($_POST[$labelsName][0]) ? $_POST[$labelsName][0] : '';
            $file['extension'] = $this->getExtension($name);
            $file['pathname']  = $this->setPathName(0, $file['extension']);
            $file['title']     = !empty($title) ? htmlspecialchars($title) : substr($name, 0, strpos($name, $file['extension']) - 1);
            $file['title']     = $purifier->purify($file['title']);
            $file['size']      = $size;
            $file['tmpname']   = $tmp_name;
            return array($file);
        }
        return $files;
    }

    /**
     * Get extension of a file.
     * 
     * @param  string    $filename 
     * @access public
     * @return string
     */
    public function getExtension($filename)
    {
        $extension = trim(strtolower(pathinfo($filename, PATHINFO_EXTENSION)));
        if(empty($extension) or stripos(",{$this->config->file->dangers},", ",{$extension},") !== false) return 'txt';
        if(empty($extension) or stripos(",{$this->config->file->allowed},", ",{$extension},") === false) return 'txt';
        return $extension;
    }

    /**
     * Set path name of the uploaded file to be saved.
     * 
     * @param  int    $fileID 
     * @param  string $extension 
     * @access public
     * @return string
     */
    public function setPathName($fileID, $extension)
    {
        $sessionID  = session_id();
        $randString = substr($sessionID, mt_rand(0, strlen($sessionID) - 5), 3);
        return date('Ym/dHis', $this->now) . $fileID . mt_rand(0, 10000) . $randString . '.' . $extension;;
    }

    /**
     * Set save path.
     * 
     * @access public
     * @return void
     */
    public function setSavePath()
    {
        $savePath = $this->app->getAppRoot() . "data/upload/" . date('Ym/', $this->now);
        if(!file_exists($savePath))
        {
            @mkdir($savePath, 0777, true);
            touch($savePath . 'index.html');
        }
        $this->savePath = dirname($savePath) . '/';
    }
    
    /**
     * Set the web path of upload files.
     * 
     * @access public
     * @return void
     */
    public function setWebPath()
    {
        $this->webPath = $this->app->getWebRoot() . "data/upload/{$this->app->company->id}/";
    }

    /**
     * Insert the set image size code.
     * 
     * @param  string    $content 
     * @param  int       $maxSize 
     * @access public
     * @return string
     */
    public function setImgSize($content, $maxSize = 0)
    {
        if(empty($content)) return $content;

        $readLinkReg = htmlspecialchars(str_replace(array('%fileID%', '/', '?'), array('[0-9]+', '\/', '\?'), helper::createLink('file', 'read', 'fileID=(%fileID%)')));

        $content = preg_replace('/ src="(' . $readLinkReg . ')" /', ' onload="setImageSize(this,' . $maxSize . ')" src="$1" ', $content);
        $content = preg_replace('/ src="{([0-9]+)}" /', ' onload="setImageSize(this,' . $maxSize . ')" src="' . helper::createLink('file', 'read', "fileID=$1")  . '" ', $content);
        return str_replace(' src="data/upload', ' onload="setImageSize(this,' . $maxSize . ')" src="data/upload', $content);
    }

    /**
     * Paste image in kindeditor at firefox and chrome. 
     * 
     * @param  string    $data 
     * @access public
     * @return string
     */
    public function pasteImage($data, $uid = '')
    {
        if(empty($data)) return '';
        $data = str_replace('\"', '"', $data);

        $dataLength = strlen($data);
        if(ini_get('pcre.backtrack_limit') < $dataLength) ini_set('pcre.backtrack_limit', $dataLength);
        preg_match_all('/<img src="(data:image\/(\S+);base64,(\S+))".*\/>/U', $data, $out);
        foreach($out[3] as $key => $base64Image)
        {
            $extension = strtolower($out[2][$key]);
            if(!in_array($extension, $this->config->file->imageExtensions)) die();
            $imageData = base64_decode($base64Image);

            $file['extension'] = $extension;
            $file['pathname']  = $this->setPathName($key, $file['extension']);
            $file['size']      = strlen($imageData);
            $file['addedBy']   = $this->app->user->account;
            $file['addedDate'] = helper::today();
            $file['title']     = basename($file['pathname']);

            file_put_contents($this->savePath . $file['pathname'], $imageData);
            $this->dao->insert(TABLE_FILE)->data($file)->exec();
            $fileID = $this->dao->lastInsertID();
            if($uid) $_SESSION['album'][$uid][] = $fileID;

            $data = str_replace($out[1][$key], helper::createLink('file', 'read', "fileID=$fileID"), $data);
        }

        return $data;
    }

    /**
     * Process editor.
     * 
     * @param  object    $data 
     * @param  string    $editorList 
     * @access public
     * @return object
     */
    public function processEditor($data, $editorList, $uid = '')
    {
        if(is_string($editorList)) $editorList = explode(',', str_replace(' ', '', $editorList));
        $readLinkReg = htmlspecialchars(str_replace(array('%fileID%', $this->app->getWebRoot(), '/', '?'), array('[0-9]+', '', '\/', '\?'), helper::createLink('file', 'read', 'fileID=(%fileID%)')));
        foreach($editorList as $editorID)
        {
            if(empty($editorID) or empty($data->$editorID)) continue;
            $data->$editorID = $this->pasteImage($data->$editorID, $uid);
            $data->$editorID = preg_replace("/ src=\"$readLinkReg\" /", ' src="{$1}" ', $data->$editorID);
        }
        return $data;
    }

    /**
     * Compress image 
     * 
     * @param  array    $file 
     * @access public
     * @return array
     */
    public function compressImage($file)
    {
        if(!extension_loaded('gd') or !function_exists('imagecreatefromjpeg')) return $file;

        $pathName    = $file['pathname'];
        $fileName    = $this->savePath . $pathName;
        $suffix      = strrchr($fileName, '.');
        $lowerSuffix = strtolower($suffix);

        if(!in_array($lowerSuffix, $this->config->file->image2Compress)) return $file;

        $quality        = 85;
        $newSuffix      = '.jpg';
        $compressedName = str_replace($suffix, $newSuffix, $pathName);

        $res  = $lowerSuffix == '.bmp' ? $this->imagecreatefrombmp($fileName) : imagecreatefromjpeg($fileName);
        imagejpeg($res, $this->savePath . $compressedName, $quality);
        if($fileName != $this->savePath . $compressedName) unlink($fileName);

        $file['pathname']   = $compressedName;
        $file['extension']  = ltrim($newSuffix, '.');
        $file['size']       = filesize($this->savePath . $compressedName);
        return $file;
    }

    /**
     * Read 24bit BMP files
     * Author: de77
     * Licence: MIT
     * Webpage: de77.com
     * Version: 07.02.2010
     * Source : https://github.com/acustodioo/pic/blob/master/imagecreatefrombmp.function.php
     * 
     * @param  string    $filename 
     * @access public
     * @return resource
     */
    public function imagecreatefrombmp($filename) {
        $f = fopen($filename, "rb");

        //read header    
        $header = fread($f, 54);
        $header = unpack('c2identifier/Vfile_size/Vreserved/Vbitmap_data/Vheader_size/'.
            'Vwidth/Vheight/vplanes/vbits_per_pixel/Vcompression/Vdata_size/'.
            'Vh_resolution/Vv_resolution/Vcolors/Vimportant_colors', $header);

        if ($header['identifier1'] != 66 or $header['identifier2'] != 77)
            return false;

        if ($header['bits_per_pixel'] != 24)
            return false;

        $wid2 = ceil((3 * $header['width']) / 4) * 4;

        $wid = $header['width'];
        $hei = $header['height'];

        $img = imagecreatetruecolor($header['width'], $header['height']);

        //read pixels
        for ($y = $hei - 1; $y >= 0; $y--) {
            $row = fread($f, $wid2);
            $pixels = str_split($row, 3);

            for ($x = 0; $x < $wid; $x++) {
                imagesetpixel($img, $x, $y, $this->dwordize($pixels[$x]));
            }
        }
        fclose($f);
        return $img;
    }

    /**
     * Dwordize for imagecreatefrombmp 
     * 
     * @param  streing $str 
     * @access private
     * @return int
     */
    private function dwordize($str)
    {
        $a = ord($str[0]);
        $b = ord($str[1]);
        $c = ord($str[2]);
        return $c * 256 * 256 + $b * 256 + $a;
    }

    /**
     * Update objectID.
     * 
     * @param  int    $uid 
     * @param  int    $objectID 
     * @param  string $objectType 
     * @access public
     * @return bool
     */
    public function updateObjectID($uid, $objectID, $objectType)
    {
        if(empty($uid)) return true;

        $data = new stdclass();
        $data->objectID   = $objectID;
        $data->objectType = $objectType;
        $data->extra      = 'editor';
        if(isset($_SESSION['album'][$uid]) and $_SESSION['album'][$uid])
        {
            $this->dao->update(TABLE_FILE)->data($data)->where('id')->in($_SESSION['album'][$uid])->exec();
            if(dao::isError()) return false;
            unset($_SESSION['album'][$uid]);
            return !dao::isError();
        }
    }

    /**
     * Revert real src. 
     * 
     * @param  object    $data 
     * @param  string    $fields 
     * @access public
     * @return object
     */
    public function revertRealSRC($data, $fields)
    {
        if(is_string($fields)) $fields = explode(',', str_replace(' ', '', $fields));
        foreach($fields as $field)
        {
            if(empty($field) or empty($data->$field)) continue;
            $data->$field = preg_replace('/ src="{([0-9]+)}" /', ' src="' . helper::createLink('file', 'read', "fileID=$1")  . '" ', $data->$field);
        }
        return $data;
    }
}
