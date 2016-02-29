<?php
include 'router.class.php';
class myrouter extends router
{
    public function __construct($appName = 'demo', $appRoot = '')
    {
        parent::__construct($appName, $appRoot);
        $this->setTplRoot();
        $this->fixRequestURI();
        $this->sendHeader();
        $this->fixLangConfig();

        if(RUN_MODE == 'admin' and helper::isAjaxRequest()) $this->config->debug = 1;
    }
    public function sendHeader()
    {
        $type = 'html';
        if((strpos($_SERVER['REQUEST_URI'], '.xml') !== false) or (isset($_GET['t']) and $_GET['t'] == 'xml')) $type = 'xml'; 

        header("Content-Type: text/{$type}; charset={$this->config->encoding}");
        header("Cache-control: private");
    }
    public function setTplRoot()
    {
        $this->tplRoot = $this->wwwRoot . 'template' . DS;
    }
    public function getTplRoot()
    {
        return $this->tplRoot;
    }
    public function setClientLang($lang = '')
    {
        if(RUN_MODE != 'install' and RUN_MODE != 'upgrade' and RUN_MODE != 'shell'  and $this->config->installed)
        {
            $result = $this->dbh->query("select value from " . TABLE_CONFIG . " where owner = 'system' and module = 'common' and section = 'site' and `key` = 'defaultLang'")->fetch();
            $defaultLang = !empty($result->value) ? $result->value : $this->config->default->lang;

            $result = $this->dbh->query("select value from " . TABLE_CONFIG . " where owner = 'system' and module = 'common' and section = 'site' and `key` = 'lang'")->fetch();
            $enabledLangs = isset($result->value) ? $result->value : '';

            $result = $this->dbh->query("select value from " . TABLE_CONFIG . " where owner = 'system' and module = 'common' and section = 'site' and `key` = 'cn2tw'")->fetch();
            $this->config->cn2tw = isset($result->value) ? $result->value : '';

            if(empty($enabledLangs))
            {
                $enabledLangs = array_keys($this->config->langs);
            }
            else
            {
                $enabledLangs = explode(',', $enabledLangs);
            }
            if(!in_array($defaultLang, $enabledLangs)) $defaultLang = current($enabledLangs);

            /* Set default lang. */
            $this->config->default->lang = $defaultLang;
        }
        else
        {
            $defaultLang  = $this->config->default->lang;
            $enabledLangs = array_keys($this->config->langs);
        }
            
        $langCookieVar = RUN_MODE . 'Lang';

        if(!empty($lang))
        {
            $this->clientLang = $lang;
        }
        elseif(RUN_MODE == 'front')
        {
            if(strpos($this->server->http_referer, 'm=visual') !== false and !empty($_COOKIE['adminLang'])) 
            {
                $this->clientLang = $_COOKIE['adminLang'];
            }
            else
            {
                $flipedLangs = array_flip($this->config->langsShortcuts);
                if($this->config->requestType == 'GET' and isset($_GET[$this->config->langVar])) $this->clientLang = $flipedLangs[$_GET[$this->config->langVar]];
                if($this->config->requestType != 'GET')
                {
                    $pathInfo = $this->getPathInfo();
                    foreach($this->config->langsShortcuts as $language => $code)
                    {
                        if(strpos(trim($pathInfo, '/'), $code) === 0) $this->clientLang = $language;
                    }
                }
            }
        }
        elseif(isset($_COOKIE[$langCookieVar]))
        {
            $this->clientLang = $_COOKIE[$langCookieVar];
        }
        elseif(RUN_MODE == 'admin' and isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            $this->clientLang = strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ',') === false ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ','));
        }

        if(!empty($this->clientLang) and in_array($this->clientLang, $enabledLangs)) 
        {
            $this->clientLang = strtolower($this->clientLang);
        }
        else
        {
            $this->clientLang = $defaultLang;
        }
        setcookie('lang', $this->clientLang, $this->config->cookieLife, $this->config->cookiePath);

        if(in_array($this->clientLang, $enabledLangs)) return $this->clientLang;
    }

    public function fixLangConfig()
    {
        $langCode = $this->clientLang == $this->config->default->lang ? '' : $this->config->langsShortcuts[$this->clientLang];
        $this->config->langCode = $langCode;
        $this->config->homeRoot = getHomeRoot();
    }
    public function fixRequestURI()
    {
        if($this->config->requestType == 'GET') return true;

        if(isset($_SERVER['HTTP_X_REWRITE_URL']))
        {
            $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        elseif(isset($_SERVER['HTTP_REQUEST_URI']))
        {
            $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
        }
    }
    public function parseRequest()
    {
        if(isGetUrl())
        {
            if($this->config->requestType == 'PATH_INFO2') define('FIX_PATH_INFO2', true);
            $this->config->requestType = 'GET';
        }

        if($this->config->requestType == 'PATH_INFO' or $this->config->requestType == 'PATH_INFO2')
        {
            $this->parsePathInfo();

            $langCode = $this->config->langsShortcuts[$this->clientLang];
            if(strpos($this->URI, $langCode) === 0) $this->URI = substr($this->URI, strlen($langCode) + 1);

            $this->URI = seo::parseURI($this->URI);

            $this->setRouteByPathInfo();
        }
        elseif($this->config->requestType == 'GET')
        {
            $this->parseGET();
            $this->setRouteByGET();
        }
        else
        {
            $this->triggerError("The request type {$this->config->requestType} not supported", __FILE__, __LINE__, $exit = true);
        }
    }
    public function setControlFile($exitIfNone = true)
    {
        $modulePath = $this->getModulePath();
        $this->controlFile = $modulePath . DS . 'ext' . DS . '_' . $this->siteCode . DS . 'control' . DS . $this->methodName . '.php';
        if(is_file($this->controlFile)) return true;

        $this->controlFile = $modulePath . DS . 'ext' . DS . 'control' . DS . $this->methodName . '.php';
        if(is_file($this->controlFile)) return true;

        $this->controlFile = $modulePath . DS . 'control.php';

        if(!is_file($this->controlFile) && $this->getModuleName() != 'error') 
        {
            if($this->server->request_uri == '/favicon.ico') die();
            $this->setModuleName('error');
            $this->setMethodName('index');
            return $this->setControlFile();
        }
        return true;
    }
    public function getModulePath($appName = '', $moduleName = '')
    {
        $modulePath = parent::getModulePath($appName, $moduleName);
        if(!file_exists($modulePath)) $modulePath = $this->getModuleRoot() . 'ext' . DS . '_' . $this->siteCode . DS . $moduleName . DS;
        return $modulePath;
    }
    public function setRouteByGET()
    {
        $moduleName = isset($_GET[$this->config->moduleVar]) ? strtolower($_GET[$this->config->moduleVar]) : $this->config->default->module;
        $methodName = isset($_GET[$this->config->methodVar]) ? strtolower($_GET[$this->config->methodVar]) : $this->config->default->method;
        $this->setModuleName($moduleName);
        $this->setMethodName($methodName);

        if(strpos($this->URI, '/index.php/user-oauthCallback-qq.html') !== false)
        {    
            $this->setModuleName('user');
            $this->setMethodName('oauthCallback');
            array_unshift($_GET, 'qq');
        }    

        $this->setControlFile();
    }
    public function loadModule()
    {
        if($this->config->requestType == 'GET') unset($_GET[$this->config->langVar]);
        return parent::loadModule();
    }
    public function setParamsByPathInfo($defaultParams = array())
    {
        /* Spit the URI. */
        $items     = explode('-', $this->URI);
        $itemCount = count($items);

        /* The first two item is moduleName and methodName. So the params should begin at 2.*/
        $params = array();
        for($i = 2; $i < $itemCount; $i ++)
        {
            $key = key($defaultParams);     // Get key from the $defaultParams.
            $params[$key] = str_replace('.', '-', $items[$i]);
            next($defaultParams);
        }
        $this->params = $this->mergeParams($defaultParams, $params);
    }
    public function loadLang($moduleName, $appName = '')
    {
        global $app;
        $langFiles = array();

        $modulePath   = $this->getModulePath($appName, $moduleName);
        $mainLangFile = $modulePath . 'lang' . DS . $this->clientLang . '.php';

        if(file_exists($mainLangFile)) $langFiles[] = $mainLangFile;

        if(is_object($app))
        {
            $device = helper::getDevice();
            $langPath = $this->getTplRoot() . $this->config->template->{$device}->name . DS . 'lang' . DS . $moduleName . DS; 
            $templateLangFile = $langPath . $this->clientLang . '.php';

            if(file_exists($templateLangFile)) $langFiles[] = $templateLangFile;
        }

        /* get ext lang files. */
        $extLangPath        = $this->getModuleExtPath($appName, $moduleName, 'lang');
        $commonExtLangFiles = helper::ls($extLangPath['common'] . $this->clientLang, '.php');
        $siteExtLangFiles   = helper::ls($extLangPath['site'] . $this->clientLang, '.php');
        $extLangFiles       = array_merge($commonExtLangFiles, $siteExtLangFiles);

        $langFiles = array_merge($langFiles, $extLangFiles);

        /* Set the files to includ. */
        if(empty($langFiles)) return false;

        global $lang;
        if(!is_object($lang)) $lang = new language();
        if(!isset($lang->$moduleName)) $lang->$moduleName = new stdclass();

        static $loadedLangs = array();
        foreach($langFiles as $langFile)
        {
            if(in_array($langFile, $loadedLangs)) continue;
            include $langFile;
            $loadedLangs[] = $langFile;
        }

        $this->lang = $lang;
        return $lang;
    }
    public function headError()
    {
        $this->setModuleName('error');
        $this->setMethodName('index');
        $this->setControlFile();
        $this->loadModule();
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
        elseif(isset($_SERVER['REQUEST_URI']))
        {
            $value = $_SERVER['REQUEST_URI'];
        }
        else
        {
            $value = @getenv('PATH_INFO');
            if(empty($value)) $value = @getenv('ORIG_PATH_INFO');
            if(empty($value)) $value = @getenv('REQUEST_URI');
            if(strpos($value, $_SERVER['SCRIPT_NAME']) !== false) $value = str_replace($_SERVER['SCRIPT_NAME'], '', $value);
        }

        if(strpos($value, '?') === false) return trim($value, '/');
        $value = parse_url($value);
        return trim($value['path'], '/');
    }
}
