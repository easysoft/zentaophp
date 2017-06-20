<?php
/**
 * The control file of file module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     file
 * @version     $Id: control.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
class file extends control
{
    /**
     * AJAX: get upload request from the web editor.
     * 
     * @access public
     * @return void
     */
    public function ajaxUpload($uid = '')
    {
        $file = $this->file->getUpload('imgFile');
        $file = $file[0];
        if($file)
        {
            if($file['size'] == 0) die(json_encode(array('error' => 1, 'message' => $this->lang->file->errorFileUpload)));
            if(@move_uploaded_file($file['tmpname'], $this->file->savePath . $file['pathname']))
            {
                /* Compress image for jpg and bmp. */
                $file = $this->file->compressImage($file);

                $file['addedBy']    = $this->app->user->account;
                $file['addedDate']  = helper::today();
                unset($file['tmpname']);
                $this->dao->insert(TABLE_FILE)->data($file)->exec();

                $fileID = $this->dao->lastInsertID();
                $url    = $this->createLink('file', 'read', "fileID=$fileID", $file['extension']);
                if($uid) $_SESSION['album'][$uid][] = $fileID;
                die(json_encode(array('error' => 0, 'url' => $url)));
            }
            else
            {
                $error = strip_tags(sprintf($this->lang->file->errorCanNotWrite, $this->file->savePath, $this->file->savePath));
                die(json_encode(array('error' => 1, 'message' => $error)));
            }
        }
    }

    /**
     * Paste image in kindeditor at firefox and chrome. 
     * 
     * @access public
     * @return void
     */
    public function ajaxPasteImage($uid = '')
    {
        if($_POST)
        {
            echo $this->file->pasteImage($this->post->editor, $uid);
        }
    }

    /**
     * Read file. 
     * 
     * @param  int    $fileID 
     * @access public
     * @return void
     */
    public function read($fileID)
    {
        $file = $this->file->getById($fileID);
        if(empty($file) or !file_exists($file->realPath)) return false;

        $mime = in_array($file->extension, $this->config->file->imageExtensions) ? "image/{$file->extension}" : $this->config->file->mimes['default'];
        header("Content-type: $mime");

        $handle = fopen($file->realPath, "r");
        if($handle)
        {
            while(!feof($handle)) echo fgets($handle);
            fclose($handle);
        }
    }
}
