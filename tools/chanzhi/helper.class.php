    static public function createLink($moduleName, $methodName = 'index', $vars = '', $alias = array(), $viewType = '')
    {
        global $app, $config;
        $requestType = $config->requestType;
        if(defined('FIX_PATH_INFO2') and FIX_PATH_INFO2) 
        {
            $config->requestType = 'PATH_INFO2';
        }

        $clientLang = $app->getClientLang();
        $lang       = $config->langCode;

        /* Set viewType is mhtml if visit with mobile.*/
        if(!$viewType and RUN_MODE == 'front' and helper::getDevice() == 'mobile' and $methodName != 'oauthCallback') $viewType = 'mhtml';

        /* Set vars and alias. */
        if(!is_array($vars)) parse_str($vars, $vars);
        if(!is_array($alias)) parse_str($alias, $alias);
        foreach($alias as $key => $value) $alias[$key] = urlencode($value);

        /* Seo modules return directly. */
        if(helper::inSeoMode() and method_exists('uri', 'create' . $moduleName . $methodName))
        {
            if($config->requestType == 'PATH_INFO2') $config->webRoot = $_SERVER['SCRIPT_NAME'] . '/';
            $link = call_user_func_array('uri::create' . $moduleName . $methodName, array('param'=> $vars, 'alias'=>$alias, 'viewType'=>$viewType));

            /* Add client lang. */
            if($lang and $link) $link = $config->webRoot .  $lang . '/' . substr($link, strlen($config->webRoot));

            if($config->requestType == 'PATH_INFO2') $config->webRoot = getWebRoot();
            $config->requestType = $requestType;
            if($link) return $link;
        }
        
        /* Set the view type. */
        if(empty($viewType)) $viewType = $app->getViewType();
        if($config->requestType == 'PATH_INFO')  $link = $config->webRoot;
        if($config->requestType == 'PATH_INFO2') $link = $_SERVER['SCRIPT_NAME'] . '/';
        if($config->requestType == 'GET') $link = $_SERVER['SCRIPT_NAME'];
        if($config->requestType != 'GET' and $lang) $link .= "$lang/";

        /* Common method. */
        if(helper::inSeoMode())
        {
            /* If the method equal the default method defined in the config file and the vars is empty, convert the link. */
            if($methodName == $config->default->method and empty($vars))
            {
                /* If the module also equal the default module, change index-index to index.html. */
                if($moduleName == $config->default->module)
                {
                    $link .= 'index.' . $viewType;
                }
                elseif($viewType == $app->getViewType())
                {
                    $link .= $moduleName . '/';
                }
                else
                {
                    $link .= $moduleName . '.' . $viewType;
                }
            }
            else
            {
                $link .= "$moduleName{$config->requestFix}$methodName";
                foreach($vars as $value) $link .= "{$config->requestFix}$value";
                $link .= '.' . $viewType;
            }
        }
        else
        {
            $link .= "?{$config->moduleVar}=$moduleName&{$config->methodVar}=$methodName";
            if($viewType != 'html') $link .= "&{$config->viewVar}=" . $viewType;
            foreach($vars as $key => $value) $link .= "&$key=$value";
            if($lang and RUN_MODE != 'admin') $link .= "&l=$lang";
        }
        $config->requestType = $requestType;
        return $link;
    }
    public static function getDevice()
    {
        global $app, $config;

        $viewType = $app->getViewType();
        if($viewType == 'mhtml') return 'mobile';

        if(RUN_MODE == 'admin')
        {
            if($app->session->device) return $app->session->device;
            return 'desktop';
        }
        elseif(RUN_MODE == 'front')
        {
            if(isset($_COOKIE['visualDevice'])) return $_COOKIE['visualDevice'];

            /* Detect mobile. */
            $mobile = $app->loadClass('mobile');
            if($mobile->isMobile())
            {
                if(!isset($config->template->mobile)) return 'desktop';
                if(isset($config->site->mobileTemplate) and $config->site->mobileTemplate == 'close') return 'desktop';
                return 'mobile';
            }
        }
        return 'desktop';
    }
function inLink($methodName = 'index', $vars = '', $alias = '', $viewType = '')
{
    global $app;
    return helper::createLink($app->getModuleName(), $methodName, $vars, $alias, $viewType);
}
function getWebRoot($full = false)
{
    $path = $_SERVER['SCRIPT_NAME'];
    if(RUN_MODE == 'shell')
    {
        $url  = parse_url($_SERVER['argv'][1]);
        $path = empty($url['path']) ? '/' : rtrim($url['path'], '/');
        $path = empty($path) ? '/' : $path;
    }

    if($full)
    {
        $http = (isset($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS']) != 'off') ? 'https://' : 'http://';
        return $http . $_SERVER['HTTP_HOST'] . substr($path, 0, (strrpos($path, '/') + 1));
    }

    return substr($path, 0, (strrpos($path, '/') + 1));
}
/**
 * Get home root.
 * 
 * @param  string $langCode 
 * @access public
 * @return string
 */
function getHomeRoot($langCode = '')
{
    global $config;

    $langCode = $langCode == '' ? $config->langCode : $langCode;
    $defaultLang = isset($config->site->defaultLang) ?  $config->site->defaultLang : $config->default->lang;
    if($langCode == $config->langsShortcuts[$defaultLang]) return $config->webRoot;
    $homeRoot = $config->webRoot;

    if($langCode and $config->requestType == 'PATH_INFO') $homeRoot = $config->webRoot . $langCode; 
    if($langCode and $config->requestType == 'PATH_INFO2') $homeRoot = $config->webRoot . 'index.php/' . "$langCode";
    if($langCode and $config->requestType == 'GET') $homeRoot = $config->webRoot . 'index.php?l=' . "$langCode";
    if($config->requestType != 'GET') $homeRoot = rtrim($homeRoot, '/') . '/';
    return $homeRoot;
}

/**
 * Check admin entry. 
 * 
 * @access public
 * @return string
 */
function checkAdminEntry()
{
    if(strpos($_SERVER['PHP_SELF'], '/admin.php') === false) return true; 

    $path  = dirname($_SERVER['SCRIPT_FILENAME']);
    $files = scandir($path);
    $defaultFiles = array('admin.php', 'index.php', 'install.php', 'loader.php', 'upgrade.php');
    foreach($files as $file)
    {
        if(strpos($file, '.php') !== false and !in_array($file, $defaultFiles))
        {
            $contents = file_get_contents($path . '/' . $file);
            $webRoot  = getWebRoot();
            if(strpos($contents, "'RUN_MODE', 'admin'") && strpos($_SERVER['PHP_SELF'], '/admin.php') !== false) die(header("location: $webRoot"));
        }
    }
}

/**
 * Get admin entry.
 * 
 * @access public
 * @return string
 */
function getAdminEntry()
{
    $path  = dirname($_SERVER['SCRIPT_FILENAME']);
    $files = scandir($path);
    $defaultFiles = array('admin.php', 'index.php', 'install.php', 'loader.php', 'upgrade.php');
    foreach($files as $file)
    {
        if(strpos($file, '.php') !== false and !in_array($file, $defaultFiles))
        {
            $contents = file_get_contents($path . '/' . $file);

            if(strpos($contents, "'RUN_MODE', 'admin'") !== false) return $file;
        }
    }
    return 'admin.php';
}

/**
 * Key for chanzhi.
 * 
 * @access public
 * @return string
 */
function k()
{
    global $config, $lang;

    $siteCode = $config->site->code;
    $codeLen  = strlen($siteCode);
    $keywords = explode(';', $lang->k);
    $count    = count($keywords);

    $sum = 0;
    for($i = 0; $i < $codeLen; $i++) $sum += ord($siteCode{$i});

    $key = $sum % $count;
    return $keywords[$key];
}
