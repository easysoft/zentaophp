<?php
include 'router.class.php';
class myrouter extends router
{
    public $staticRoot; 

    public function __construct($appName = '', $appRoot = '')
    {
        $this->setAppName($appName);
        parent::__construct($appName, $appRoot);
        $this->setStaticRoot();
        $this->setWwwRoot();
        $this->setDataRoot();
        $this->setThemeRoot();

    }

    public function setModuleRoot()
    {
        $this->moduleRoot = $this->appRoot;
    }

    public function setStaticRoot()
    {
        $this->staticRoot = $this->basePath . 'www' . DS;
    }

    public function setWwwRoot()
    {
        $this->wwwRoot = rtrim(dirname(dirname($_SERVER['SCRIPT_FILENAME'])), DS) . DS;
    }

    public function setDataRoot()
    {
        $this->dataRoot = $this->staticRoot . 'data' . DS;
    }

    public function setThemeRoot()
    {
        $this->themeRoot = $this->staticRoot . 'theme' . DS;
    }

    public function getAppName()
    {
        return $this->appName;
    }

    public function getStaticRoot()
    {
        return $this->staticRoot;
    }

    public function getURI($full = false)
    {
        if($full and $this->config->requestType != 'GET')
        {
            if($this->URI) return $this->config->webRoot . $this->appName . '/' . $this->URI . '.' . $this->viewType;
            return $this->config->webRoot . $this->appName . '/';
        }
        return $this->URI;
    }

    public function loadCommon()
    {
        $this->setModuleName('common');
        $commonModelFile = helper::setModelFile('common');
        if(!file_exists($commonModelFile)) $commonModelFile = helper::setModelFile('common', 'sys');

        helper::import($commonModelFile);
        if(class_exists('extcommonModel'))
        {
            return new extcommonModel();
        }
        elseif(class_exists('commonModel'))
        {
            return new commonModel();
        }
        else
        {
            return false;
        }
    }

    public function setControlFile($exitIfNone = true)
    {
        $this->controlFile = $this->moduleRoot . $this->moduleName . DS . 'control.php';
        if(!is_file($this->controlFile)) $this->controlFile = $this->basePath . 'app' . DS . 'sys' . DS . $this->moduleName . DS . 'control.php';
        if(!is_file($this->controlFile))
        {
            $this->triggerError("the control file $this->controlFile not found.", __FILE__, __LINE__, $exitIfNone);
            return false;
        }
        return true;
    }
    public function getModulePath($appName = '', $moduleName = '')
    {
        $modulePath = parent::getModulePath($appName, $moduleName);
        if(!is_dir($modulePath) and $appName != 'sys') $modulePath = parent::getModulePath('sys', $moduleName);
        return $modulePath;
    }

    public function getModuleExtPath($appName, $moduleName, $ext)
    {
        $paths = parent::getModuleExtPath($appName, $moduleName, $ext);
        if((!is_dir($paths['common']) or ($paths['site'] and !is_dir($paths['site']))) and $appName != 'sys')
        {
            $sysPaths = parent::getModuleExtPath('sys', $moduleName, $ext);
            if(!is_dir($paths['common'])) $paths['common'] = $sysPaths['common'];
            if($paths['site'] and !is_dir($paths['site'])) $paths['site'] = $sysPaths['common'];
        }
        return $paths;
    }
    public function setActionExtFile()
    {
        $result = parent::setActionExtFile();
        if(!$result and $this->appName != 'sys')
        {
            $oldAppName = $this->appName;
            $this->appName = 'sys';
            $result = parent::setActionExtFile();
            $this->appName = $oldAppName;
        }
        return $result;
    }

    public function loadLang($moduleName, $appName = '')
    {
        if($moduleName == 'common' and $appName == '') $this->loadLang('common', 'sys');

        $modulePath   = $this->getModulePath($appName, $moduleName);
        $mainLangFile = $modulePath . 'lang' . DS . $this->clientLang . '.php';
        $extLangPath        = $this->getModuleExtPath($appName, $moduleName, 'lang');
        $commonExtLangFiles = helper::ls($extLangPath['common'] . $this->clientLang, '.php');
        $siteExtLangFiles   = helper::ls($extLangPath['site'] . $this->clientLang, '.php');
        $extLangFiles       = array_merge($commonExtLangFiles, $siteExtLangFiles);

        /* Set the files to includ. */
        if(!is_file($mainLangFile))
        {
            if(empty($extLangFiles)) return false;  // also no extension file.
            $langFiles = $extLangFiles;
        }
        else
        {
            $langFiles = array_merge(array($mainLangFile), $extLangFiles);
        }

        global $lang;
        if(!is_object($lang)) $lang = new language();

        static $loadedLangs = array();
        foreach($langFiles as $langFile)
        {
            if(in_array($langFile, $loadedLangs)) continue;
            include $langFile;
            $loadedLangs[] = $langFile;
        }

        /* Merge from the db lang. */
        if(empty($appName)) $appName = $this->appName;
        if(isset($lang->db->custom[$appName][$moduleName]))
        {
            foreach($lang->db->custom[$appName][$moduleName] as $section => $fields)
            {
                foreach($fields as $key => $value)
                {
                    if($moduleName == 'common')
                    {
                        unset($lang->{$section}[$key]);
                        $lang->{$section}[$key] = $value;
                    }
                    else
                    {
                        unset($lang->{$moduleName}->{$section}[$key]);
                        $lang->{$moduleName}->{$section}[$key] = $value;
                    }
                }
            }
        }

        $this->lang = $lang;
        return $lang;
    }

    public function getPathInfo()
    {
        if(isset($_SERVER['PATH_INFO']))
        {
            $value = $_SERVER['PATH_INFO'];
        }
        elseif(isset($_SERVER['ORIG_PATH_INFO']))
        {
            $value = $_SERVER['ORIG_PATH_INFO'];
        }
        else
        {
            $value = @getenv('PATH_INFO');
            if(empty($value)) $value = @getenv('ORIG_PATH_INFO');
            if(strpos($value, $_SERVER['SCRIPT_NAME']) !== false) $value = str_replace($_SERVER['SCRIPT_NAME'], '', $value);
        }

        if(strpos($value, '?') === false) return trim($value, '/');
        $value = parse_url($value);
        return trim($value['path'], '/');
    }
}
