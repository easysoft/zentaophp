    public function setViewPrefix()
    {
        global $app, $config;
        $this->viewPrefix = '';
        if(RUN_MODE == 'front')
        {
            /* Detect mobile. */
            $mobile = $app->loadClass('mobile');
            if($mobile->isMobile() and !isset($config->template->mobile)) $this->viewPrefix = 'm.';
        }
    }
    public function setViewFile($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));

        $modulePath  = $this->app->getModulePath($this->appName, $moduleName);
        $viewExtPath = $this->app->getModuleExtPath($this->appName, $moduleName, 'view');

        $viewType = $this->viewType == 'mhtml' ? 'html' : $this->viewType;
        if((RUN_MODE != 'front') or (strpos($modulePath, 'module' . DS . 'ext' . DS) !== false))
        {
            /* If not in front mode or is ext module, view file is in modeule path. */
            $mainViewFile = $modulePath . 'view' . DS . $this->viewPrefix . $methodName . '.' . $viewType . '.php';
        }
        else
        {
            /* If in front mode, view file is in www/template path. */
            $mainViewFile = TPL_ROOT . $moduleName . DS . $this->viewPrefix . "{$methodName}.{$viewType}.php";
            if($this->viewPrefix == 'm.' and !is_file($mainViewFile))
            {
                $this->viewPrefix = '';
                $mainViewFile = TPL_ROOT . $moduleName . DS . $this->viewPrefix . "{$methodName}.{$viewType}.php";
            }
        }

        /* Extension view file. */
        $commonExtViewFile = $viewExtPath['common'] . $this->viewPrefix . $methodName . ".{$viewType}.php";
        $siteExtViewFile   = $viewExtPath['site'] . $this->viewPrefix . $methodName . ".{$viewType}.php";
        $viewFile = file_exists($commonExtViewFile) ? $commonExtViewFile : $mainViewFile;
        $viewFile = file_exists($siteExtViewFile) ? $siteExtViewFile : $viewFile;
        if(!is_file($viewFile)) $this->app->triggerError("the view file $viewFile not found", __FILE__, __LINE__, $exit = true);

        /* Extension hook file. */
        $commonExtHookFiles = glob($viewExtPath['common'] . $this->viewPrefix . $methodName . ".*.{$viewType}.hook.php");
        $siteExtHookFiles   = glob($viewExtPath['site'] . $this->viewPrefix . $methodName . ".*.{$viewType}.hook.php");
        $extHookFiles       = array_merge((array) $commonExtHookFiles, (array) $siteExtHookFiles);
        if(!empty($extHookFiles)) return array('viewFile' => $viewFile, 'hookFiles' => $extHookFiles);

        return $viewFile;
    }
    public function getExtViewFile($viewFile)
    {
        if($this->config->site->code)
        {
            $extPath     = dirname(realpath($viewFile)) . "/ext/_{$this->config->site->code}/";
            $extViewFile = $extPath . basename($viewFile);

            if(file_exists($extViewFile))
            {
                helper::cd($extPath);
                return $extViewFile;
            }
        }

        $extPath = RUN_MODE == 'front' ? dirname(realpath($viewFile)) . '/ext/' : dirname(dirname(realpath($viewFile))) . '/ext/view/';
        $extViewFile = $extPath . basename($viewFile);

        if(file_exists($extViewFile))
        {
            helper::cd($extPath);
            return $extViewFile;
        }

        return false;
    }
    private function getCSS($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));

        $modulePath = $this->app->getModulePath($this->appName, $moduleName);

        $cssExtPath = $this->app->getModuleExtPath($this->appName, $moduleName, 'css') ;

        $css = '';
        if((RUN_MODE != 'front') or (strpos($modulePath, 'module' . DS . 'ext') !== false))
        {
            $mainCssFile   = $modulePath . 'css' . DS . $this->viewPrefix . 'common.css';
            $methodCssFile = $modulePath . 'css' . DS . $this->viewPrefix . $methodName . '.css';

            if(file_exists($mainCssFile))   $css .= file_get_contents($mainCssFile);
            if(file_exists($methodCssFile)) $css .= file_get_contents($methodCssFile);
        }
        else
        {
            $defaultMainCssFile   = TPL_ROOT . $moduleName . DS . 'css' . DS . $this->viewPrefix . "common.css";
            $defaultMethodCssFile = TPL_ROOT . $moduleName . DS . 'css' . DS . $this->viewPrefix . "{$methodName}.css";
            $themeMainCssFile     = TPL_ROOT . $moduleName . DS . 'css' . DS . $this->viewPrefix . "common.{$this->config->site->theme}.css";
            $themeMethodCssFile   = TPL_ROOT . $moduleName . DS . 'css' . DS . $this->viewPrefix . "{$methodName}.{$this->config->site->theme}.css";

            if(file_exists($defaultMainCssFile))   $css .= file_get_contents($defaultMainCssFile);
            if(file_exists($defaultMethodCssFile)) $css .= file_get_contents($defaultMethodCssFile);
            if(file_exists($themeMainCssFile))     $css .= file_get_contents($themeMainCssFile);
            if(file_exists($themeMethodCssFile))   $css .= file_get_contents($themeMethodCssFile);
        }

        $commonExtCssFiles = glob($cssExtPath['common'] . $methodName . DS . $this->viewPrefix . '*.css');
        if(!empty($commonExtCssFiles)) foreach($commonExtCssFiles as $cssFile) $css .= file_get_contents($cssFile);

        $methodExtCssFiles = glob($cssExtPath['site'] . $methodName . DS . $this->viewPrefix . '*.css');
        if(!empty($methodExtCssFiles)) foreach($methodExtCssFiles as $cssFile) $css .= file_get_contents($cssFile);

        return $css;
    }
    private function getJS($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));
        
        $modulePath = $this->app->getModulePath($this->appName, $moduleName);
        $jsExtPath  = $this->app->getModuleExtPath($this->appName, $moduleName, 'js');

        $js = '';
        if((RUN_MODE !== 'front') or (strpos($modulePath, 'module' . DS . 'ext') !== false))
        {
            $mainJsFile   = $modulePath . 'js' . DS . $this->viewPrefix . 'common.js';
            $methodJsFile = $modulePath . 'js' . DS . $this->viewPrefix . $methodName . '.js';

            if(file_exists($mainJsFile))   $js .= file_get_contents($mainJsFile);
            if(file_exists($methodJsFile)) $js .= file_get_contents($methodJsFile);
        }
        else
        {
            $defaultMainJsFile   = TPL_ROOT . $moduleName . DS . 'js' . DS . $this->viewPrefix . "common.js";
            $defaultMethodJsFile = TPL_ROOT . $moduleName . DS . 'js' . DS . $this->viewPrefix . "{$methodName}.js";
            $themeMainJsFile     = TPL_ROOT . $moduleName . DS . 'js' . DS . $this->viewPrefix . "common.{$this->config->site->theme}.js";
            $themeMethodJsFile   = TPL_ROOT . $moduleName . DS . 'js' . DS . $this->viewPrefix . "{$methodName}.{$this->config->site->theme}.js";

            if(file_exists($defaultMainJsFile))   $js .= file_get_contents($defaultMainJsFile);
            if(file_exists($defaultMethodJsFile)) $js .= file_get_contents($defaultMethodJsFile);
            if(file_exists($themeMainJsFile))     $js .= file_get_contents($themeMainJsFile);
            if(file_exists($themeMethodJsFile))   $js .= file_get_contents($themeMethodJsFile);
        }

        $commonExtJsFiles = glob($jsExtPath['common'] . $methodName . DS . $this->viewPrefix . '*.js');
        if(!empty($commonExtJsFiles))
        {
            foreach($commonExtJsFiles as $jsFile) $js .= file_get_contents($jsFile);
        }

        $methodExtJsFiles = glob($jsExtPath['site'] . $methodName . DS  . $this->viewPrefix . '*.js');
        if(!empty($methodExtJsFiles))
        {
            foreach($methodExtJsFiles as $jsFile) $js .= file_get_contents($jsFile);
        }

        return $js;
    }
    public function parse($moduleName = '', $methodName = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(empty($methodName)) $methodName = $this->methodName;

        if($this->viewType == 'json') return $this->parseJSON($moduleName, $methodName);

        /* If the parser is default or run mode is admin, install, upgrade, call default parser.  */
        if(RUN_MODE != 'front' or $this->config->template->parser == 'default')
        {
            $this->parseDefault($moduleName, $methodName);
            return $this->output;
        }

        /* Call the extened parser. */
        $parserClassName = $this->config->template->parser . 'Parser';
        $parserClassFile = 'parser.' . $this->config->template->parser . '.class.php';
        $parserClassFile = dirname(__FILE__) . DS . $parserClassFile;
        if(!is_file($parserClassFile)) $this->app->triggerError(" The parser file  $parserClassFile not found", __FILE__, __LINE__, $exit = true);

        helper::import($parserClassFile);
        if(!class_exists($parserClassName)) $this->app->triggerError(" Can not find class : $parserClassName not found in $parserClassFile <br/>", __FILE__, __LINE__, $exit = true);

        $parser = new $parserClassName($this);
        return $parser->parse($moduleName, $methodName);
    }
    private function parseJSON($moduleName, $methodName)
    {
        die('View type error.');
        unset($this->view->app);
        unset($this->view->config);
        unset($this->view->lang);
        unset($this->view->pager);
        unset($this->view->header);
        unset($this->view->position);
        unset($this->view->moduleTree);
        unset($this->view->common);

        $output['status'] = is_object($this->view) ? 'success' : 'fail';
        $output['data']   = json_encode($this->view);
        $output['md5']    = md5(json_encode($this->view));
        $this->output     = json_encode($output);
    }
    private function parseDefault($moduleName, $methodName)
    {
        /* Set the view file. */
        $results  = $this->setViewFile($moduleName, $methodName);
        $viewFile = $results;
        if(is_array($results)) extract($results);

        /* Get css and js. */
        $css = $this->getCSS($moduleName, $methodName);
        $js  = $this->getJS($moduleName, $methodName);

        if(RUN_MODE == 'front')
        {
            $template    = $this->config->template->{$this->device}->name;
            $theme       = $this->config->template->{$this->device}->theme;
            $customParam = $this->loadModel('ui')->getCustomParams($template, $theme);
            $themeHooks  = $this->loadThemeHooks();
            $importedCSS = array();
            $importedJS  = array();

            if(!empty($themeHooks))
            {
                $jsFun  = "get{$theme}JS";
                $cssFun = "get{$theme}CSS";

                if(function_exists($jsFun))  $importedJS = $jsFun();
                if(function_exists($cssFun)) $importedCSS = $cssFun();
            }

            $js .= zget($importedJS, "{$template}_{$theme}_all", '');
            $js .= zget($this->config->js, "{$template}_{$theme}_all", '');
            $js .= zget($importedJS, "{$template}_{$theme}_{$moduleName}_{$methodName}", '');
            $js .= zget($this->config->js,"{$template}_{$theme}_{$moduleName}_{$methodName}", '');

            $allPageCSS  = zget($importedCSS, "{$template}_{$theme}_all", '');
            $allPageCSS .= zget($this->config->css, "{$template}_{$theme}_all", '');

            $currentPageCSS  = zget($importedCSS, "{$template}_{$theme}_{$moduleName}_{$methodName}", '');
            $currentPageCSS .= zget($this->config->css, "{$template}_{$theme}_{$moduleName}_{$methodName}", '');
            $css .= $this->ui->compileCSS($customParam, $allPageCSS . $currentPageCSS);
        }

        if($css) $this->view->pageCSS = $css;
        if($js)  $this->view->pageJS  = $js;
        
        /* Change the dir to the view file to keep the relative pathes work. */
        $currentPWD = getcwd();
        chdir(dirname($viewFile));

        extract((array)$this->view);

        ob_start();
        include $viewFile;
        if(isset($hookFiles)) foreach($hookFiles as $hookFile) if(file_exists($hookFile)) include $hookFile;
        $this->output .= ob_get_contents();
        ob_end_clean();

        /* At the end, chang the dir to the previous. */
        chdir($currentPWD);
    }
    public function display($moduleName = '', $methodName = '')
    {
        if(empty($this->output)) $this->parse($moduleName, $methodName);
        if(isset($this->config->cn2tw) and $this->config->cn2tw and $this->app->getClientLang() == 'zh-tw')
        {
            $this->app->loadClass('cn2tw', true);
            $this->output = cn2tw::translate($this->output);
        }

        if(RUN_MODE == 'front') 
        {
            $this->mergeCSS();
            $this->mergeJS();
        }

        //if(isset($this->config->site->cdn))
        //{
        //    $cdn = rtrim($this->config->site->cdn, '/');
        //    $this->output = str_replace('src="/data/upload', 'src="' . $cdn . '/data/upload', $this->output);
        //    $this->output = str_replace("src='/data/upload", "src='" . $cdn . "/data/upload", $this->output);
        //    $this->output = str_replace("url(/data/upload", "url(" . $cdn . "/data/upload", $this->output);
        //}
        
        echo $this->output;
    }
    public function createLink($moduleName, $methodName = 'index', $vars = array(), $alias = array(), $viewType = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        return helper::createLink($moduleName, $methodName, $vars, $alias, $viewType);
    }
    public function inlink($methodName = 'index', $vars = array(), $alias = array(), $viewType = '')
    {
        return helper::createLink($this->moduleName, $methodName, $vars, $alias, $viewType);
    }

    /**
     * Set TPL_ROOT used in template files.
     * 
     * @access public
     * @return void
     */
    public function setTplRoot()
    {
        if(!defined('TPL_ROOT')) define('TPL_ROOT', $this->app->getTplRoot() . $this->config->template->{$this->device}->name . DS . 'view' . DS);
    }

    /**
     * Load theme hooks.
     * 
     * @access public
     * @return array
     */
    public function loadThemeHooks()
    {
        $theme    = $this->config->template->{$this->device}->theme;
        $hookPath = dirname(TPL_ROOT) . DS . 'theme' . DS . $theme . DS;
        $hookFiles = glob("{$hookPath}*.php");

        foreach($hookFiles as $file) include $file;
        return $hookFiles;
    }

    /**
     * Merge all css codes of one page. 
     * 
     * @access public
     * @return void
     */
    public function mergeCSS()
    {
        $pageCSS = '';
        preg_match_all('/<style>([\s\S]*?)<\/style>/', $this->output, $styles);
        if(!empty($styles[1])) $pageCSS = join('', $styles[1]);
        if(!empty($pageCSS))
        {
            $this->output = preg_replace('/<style>([\s\S]*?)<\/style>/', '', $this->output);
            if(strpos($this->output, '</head>') != false) $this->output = str_replace('</head>', "<style>{$pageCSS}</style></head>", $this->output);
            if(strpos($this->output, '</head>') == false) $this->output = "<style>{$pageCSS}</style>" . $this->output;
        }
    }

    /**
     * Merge all js codes of one page, 
     * 
     * @access public
     * @return void
     */
    public function mergeJS()
    {
        $pageJS = '';
        preg_match_all('/<script>([\s\S]*?)<\/script>/', $this->output, $scripts);
        if(empty($scripts[1][1])) return true;
        $configCode = $scripts[1][0] . $scripts[1][1];
        unset($scripts[1][1]);
        unset($scripts[1][0]);
        
        if(!empty($scripts[1])) $pageJS = join('', $scripts[1]);
        if(!empty($pageJS))
        {
            $this->output = preg_replace('/<script>([\s\S]*?)<\/script>/', '', $this->output);
            if(strpos($this->output, '</body>') != false) $this->output = str_replace('</body>', "<script>{$pageJS}</script></body>", $this->output);
            if(strpos($this->output, '</body>') == false) $this->output .= "<script>$pageJS</script>";
        }
        $pos = strpos($this->output, '<script src=');
        $this->output = substr_replace($this->output, '<script>' . $configCode . '</script>', $pos) . substr($this->output, $pos);
        return true;
    }
