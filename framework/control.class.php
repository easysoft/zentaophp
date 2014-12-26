<?php
/**
 * The control class file of ZenTaoPHP framework.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/**
 * The base class of control.
 *
 * @package framework
 */
class control
{
    /**
     * The global $app object.
     * 全局对象 $app
     * 
     * @var object
     * @access protected
     */
    protected $app;

    /**
     * The global $config object.
     * 全局对象 $config
     * 
     * @var object
     * @access protected
     */
    protected $config;

    /**
     * The global $lang object.
     * 全局对象 $lang
     * 
     * @var object
     * @access protected
     */
    protected $lang;

    /**
     * The global $dbh object, the database connection handler.
     * 全局对象 $dbh，数据库连接句柄
     * 
     * @var object
     * @access protected
     */
    protected $dbh;

    /**
     * The $dao object, used to access or update database.
     * $dao对象，封装SQL语句，方便数据库访问和更新
     * 
     * @var object
     * @access protected
     */
    public $dao;

    /**
     * The $post object, used to access the $_POST var.
     * $post对象，将$_POST数组改为对象，方便调用
     * 
     * @var ojbect
     * @access public
     */
    public $post;

    /**
     * The $get object, used to access the $_GET var.
     * $get对象，将$_GET数组改为对象，方便调用
     * 
     * @var ojbect
     * @access public
     */
    public $get;

    /**
     * The $session object, used to access the $_SESSION var.
     * $session对象，将$_SESSION数组改为对象，方便调用
     * 
     * @var ojbect
     * @access public
     */
    public $session;

    /**
     * The $server object, used to access the $_SERVER var.
     * $server对象，将$_SERVER数组改为对象，方便调用
     * 
     * @var ojbect
     * @access public
     */
    public $server;

    /**
     * The $cookie object, used to access the $_COOKIE var.
     * $cookie对象，将$_COOKIE数组改为对象，方便调用
     * 
     * @var ojbect
     * @access public
     */
    public $cookie;

    /**
     * The $global object, used to access the $_GLOBAL var.
     * $global对象，将$_COOKIE数组改为对象，方便调用
     * 
     * @var ojbect
     * @access public
     */
    public $global;

    /**
     * The name of current module.
     * 当前模块的名称
     * 
     * @var string
     * @access protected
     */
    protected $moduleName;

    /**
     * The vars assigned to the view page.
     * $view用于存放从control传到view视图的数据
     * 
     * @var object
     * @access public
     */
    public $view; 

    /**
     * The type of the view, such html, json.
     * 视图的类型，比如html json 
     * 
     * @var string
     * @access private
     */
    private $viewType;

    /**
     * The content to display.
     * 输出到浏览器的内容 
     * 
     * @var string
     * @access private
     */
    private $output;

    /**
     * The directory seperator.
     * 目录分隔符(Unix系统为'/'，Windows系统为'\')
     * 
     * @var string
     * @access protected
     */
    protected $pathFix;

    /**
     * The construct function.
     *
     * 1. global the global vars, refer them by the class member such as $this->app.
     * 2. set the pathes of current module, and load it's model class.
     * 3. auto assign the $lang and $config to the view.
     * 
     * 构造方法 
     * 
     * 1. 将全局变量设为control类的成员变量，方便control的派生类调用 
     * 2. 设置当前模块，读取该模块的model类
     * 3. 初始化$view视图类 
     *
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        /* Global the globals, and refer them to the class member. */
        /* 将全局变量设为control类的成员变量，方便control的派生类调用 */
        global $app, $config, $lang, $dbh;
        $this->app        = $app;
        $this->config     = $config;
        $this->lang       = $lang;
        $this->dbh        = $dbh;
        $this->pathFix    = $this->app->getPathFix();
        $this->viewType   = $this->app->getViewType();

        /* Load the model file auto. */
        /* 设置当前模块，读取该模块的model类 */
        $this->setModuleName($moduleName);
        $this->setMethodName($methodName);
        $this->loadModel();

        /* Init the view vars.  */
        /* 初始化$view视图类 */
        $this->view = new stdclass();
        $this->view->app    = $app;
        $this->view->lang   = $lang;
        $this->view->config = $config;
        $this->view->title  = '';

        /* Set super vars. */
        /* 设置超级变量，从$app引用过来 */
        $this->setSuperVars();
    }

    //-------------------- Model related methods --------------------//
    //-------------------- Model相关方法 --------------------//

    /* Set the module name. 
    /* 设置模块名 
     * 
     * @param   string  $moduleName   The module name, if empty, get it from $app.   模块名，如果为空，则从$app中获取
     * @access  private
     * @return  void
     */
    private function setModuleName($moduleName = '')
    {
        $this->moduleName = $moduleName ? strtolower($moduleName) : $this->app->getModuleName();
    }

    /* Set the method name.
     * 设置方法名
     * 
     * @param   string  $methodName    The method name, if empty, get it from $app.   方法名，如果为空，则从$app中获取
     * @access  private
     * @return  void
     */
    private function setMethodName($methodName = '')
    {
        $this->methodName = $methodName ? strtolower($methodName) : $this->app->getMethodName();
    }

    /**
     * Load the model file of one module.
     * 加载所属模块的model文件
     * 
     * @param   string      $methodName    The method name, if empty, use current module's name.
     * @access  public
     * @return  object|bool If no model file, return false. Else return the model object.
     */
    public function loadModel($moduleName = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        $modelFile = helper::setModelFile($moduleName);

        /* If no model file, try load config. */
        if(!helper::import($modelFile)) 
        {
            $this->app->loadConfig($moduleName, false);
            $this->app->loadLang($moduleName);
            $this->dao = new dao();
            return false;
        }

        $modelClass = class_exists('ext' . $moduleName. 'model') ? 'ext' . $moduleName . 'model' : $moduleName . 'model';
        if(!class_exists($modelClass)) $this->app->triggerError(" The model $modelClass not found", __FILE__, __LINE__, $exit = true);

        $this->$moduleName = new $modelClass();
        $this->dao = $this->$moduleName->dao;
        return $this->$moduleName;
    }

    /**
     * Set the super vars.
     * 设置超级全局变量，$app已经设置过了，直接引用
     * 
     * @access protected
     * @return void
     */
    protected function setSuperVars()
    {
        $this->post    = $this->app->post;
        $this->get     = $this->app->get;
        $this->server  = $this->app->server;
        $this->session = $this->app->session;
        $this->cookie  = $this->app->cookie;
        $this->global  = $this->app->global;
    }

    //-------------------- View related methods --------------------//
    
    /**
     * Set the view file, thus can use fetch other module's page.
     * 设置视图文件，可以获取其他模块的视图文件
     * 
     * @param  string   $moduleName    module name
     * @param  string   $methodName    method name
     * @access private
     * @return string  the view file
     */
    private function setViewFile($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));

        $modulePath  = $this->app->getModulePath($moduleName);
        $viewExtPath = $this->app->getModuleExtPath($moduleName, 'view');

        /* The main view file, extension view file and hook file. */
        $mainViewFile = $modulePath . 'view' . $this->pathFix . $methodName . '.' . $this->viewType . '.php';
        $extViewFile  = $viewExtPath . $methodName . ".{$this->viewType}.php";
        $extHookFiles = helper::ls($viewExtPath, '.hook.php');

        $viewFile = file_exists($extViewFile) ? $extViewFile : $mainViewFile;
        if(!is_file($viewFile)) $this->app->triggerError("the view file $viewFile not found", __FILE__, __LINE__, $exit = true);
        if(!empty($extHookFiles)) return array('viewFile' => $viewFile, 'hookFiles' => $extHookFiles);
        return $viewFile;
    }

    /**
     * Get the extension file of an view.
     * 获取视图的扩展文件，在ext/view/目录下
     * 
     * @param  string $viewFile 
     * @access public
     * @return string|bool  If extension view file exists, return the path. Else return fasle.
     */
    public function getExtViewFile($viewFile)
    {
        $extPath     = dirname(dirname(realpath($viewFile))) . '/ext/view/';
        $extViewFile = $extPath . basename($viewFile);
        if(file_exists($extViewFile))
        {
            helper::cd($extPath);
            return $extViewFile;
        }
        return false;
    }

    /**
     * Get css code for a method. 
     * 获取方法的css内容，common.css + 该方法的css 
     * 
     * @param  string    $moduleName 
     * @param  string    $methodName 
     * @access private
     * @return string
     */
    private function getCSS($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));
        $modulePath = $this->app->getModulePath($moduleName);

        $css = '';
        $mainCssFile   = $modulePath . 'css' . $this->pathFix . 'common.css';
        $methodCssFile = $modulePath . 'css' . $this->pathFix . $methodName . '.css';
        if(file_exists($mainCssFile))   $css .= file_get_contents($mainCssFile);
        if(is_file($methodCssFile))     $css .= file_get_contents($methodCssFile);

        return $css;
    }

    /**
     * Get js code for a method. 
     * 获取方法的js，common.js + 该方法的js
     * 
     * @param  string    $moduleName 
     * @param  string    $methodName 
     * @access private
     * @return string
     */
    private function getJS($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));
        $modulePath = $this->app->getModulePath($moduleName);

        $js = '';
        $mainJsFile   = $modulePath . 'js' . $this->pathFix . 'common.js';
        $methodJsFile = $modulePath . 'js' . $this->pathFix . $methodName . '.js';
        if(file_exists($mainJsFile))   $js .= file_get_contents($mainJsFile);
        if(is_file($methodJsFile))     $js .= file_get_contents($methodJsFile);

        return $js;
    }

    /**
     * Assign one var to the view vars.
     * 向$view传递一个变量
     * 
     * @param   string  $name       the name.
     * @param   mixed   $value      the value.
     * @access  public
     * @return  void
     */
    public function assign($name, $value)
    {
        $this->view->$name = $value;
    }

    /**
     * Clear the output.
     * 将之前打算输出的内容清空
     *
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->output = '';
    }

    /**
     * Parse view file. 
     * 根据请求的视图类型，生成输出内容
     *
     * @param  string $moduleName    module name, if empty, use current module.
     * @param  string $methodName    method name, if empty, use current method.
     * @access public
     * @return string the parsed result.
     */
    public function parse($moduleName = '', $methodName = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(empty($methodName)) $methodName = $this->methodName;

        if($this->viewType == 'json')
        {
            $this->parseJSON($moduleName, $methodName);
        }
        else
        {
            $this->parseDefault($moduleName, $methodName);
        }
        return $this->output;
    }

    /**
     * Parse json format.
     * 请求为json格式的处理逻辑 
     *
     * @param string $moduleName    module name
     * @param string $methodName    method name
     * @access private
     * @return void
     */
    private function parseJSON($moduleName, $methodName)
    {
        unset($this->view->app);
        unset($this->view->config);
        unset($this->view->lang);
        unset($this->view->header);
        unset($this->view->position);
        unset($this->view->moduleTree);

        $output['status'] = is_object($this->view) ? 'success' : 'fail';
        $output['data']   = json_encode($this->view);
        $output['md5']    = md5(json_encode($this->view));
        $this->output     = json_encode($output);
    }

    /**
     * Parse default html format.
     * 其他请求格式的处理逻辑，输出视图文件的内容
     *
     * @param string $moduleName    module name
     * @param string $methodName    method name
     * @access private
     * @return void
     */
    private function parseDefault($moduleName, $methodName)
    {
        /* Set the view file. */
        $viewFile = $this->setViewFile($moduleName, $methodName);
        if(is_array($viewFile)) extract($viewFile);

        /* Get css and js. */
        $css = $this->getCSS($moduleName, $methodName);
        $js  = $this->getJS($moduleName, $methodName);
        if($css) $this->view->pageCss = $css;
        if($js)  $this->view->pageJS  = $js;

        /* Change the dir to the view file to keep the relative pathes work. */
        $currentPWD = getcwd();
        chdir(dirname($viewFile));

        extract((array)$this->view);
        ob_start();
        include $viewFile;
        if(isset($hookFiles)) foreach($hookFiles as $hookFile) include $hookFile;
        $this->output .= ob_get_contents();
        ob_end_clean();

        /* At the end, chang the dir to the previous. */
        chdir($currentPWD);
    }

    /**
     * Get the output of one module's one method as a string, thus in one module's method, can fetch other module's content.
     * If the module name is empty, then use the current module and method. If set, use the user defined module and method.
     *
     * 获取一个方法的输出内容，这样我们可以在一个方法里获取其他模块方法的内容
     *
     * @param   string  $moduleName    module name.
     * @param   string  $methodName    method name.
     * @param   array   $params        params.
     * @access  public
     * @return  string  the parsed html.
     */
    public function fetch($moduleName = '', $methodName = '', $params = array())
    {
        if($moduleName == '') $moduleName = $this->moduleName;
        if($methodName == '') $methodName = $this->methodName;
        if($moduleName == $this->moduleName and $methodName == $this->methodName) 
        {
            $this->parse($moduleName, $methodName);
            return $this->output;
        }

        /* Set the pathes and files to included. */
        $modulePath        = $this->app->getModulePath($moduleName);
        $moduleControlFile = $modulePath . 'control.php';
        $actionExtFile     = $this->app->getModuleExtPath($moduleName, 'control') . strtolower($methodName) . '.php';
        $file2Included     = file_exists($actionExtFile) ? $actionExtFile : $moduleControlFile;

        /* Load the control file. */
        if(!is_file($file2Included)) $this->app->triggerError("The control file $file2Included not found", __FILE__, __LINE__, $exit = true);
        $currentPWD = getcwd();
        chdir(dirname($file2Included));
        if($moduleName != $this->moduleName) helper::import($file2Included);
        
        /* Set the name of the class to be called. */
        $className = class_exists("my$moduleName") ? "my$moduleName" : $moduleName;
        if(!class_exists($className)) $this->app->triggerError(" The class $className not found", __FILE__, __LINE__, $exit = true);

        /* Parse the params, create the $module control object. */
        if(!is_array($params)) parse_str($params, $params);
        $module = new $className($moduleName, $methodName);

        /* Call the method and use ob function to get the output. */
        ob_start();
        call_user_func_array(array($module, $methodName), $params);
        $output = ob_get_contents();
        ob_end_clean();

        /* Return the content. */
        unset($module);
        chdir($currentPWD);
        return $output;
    }

    /**
     * Print the content of the view. 
     * 向浏览器输出内容
     *
     * @param   string  $moduleName    module name
     * @param   string  $methodName    method name
     * @access  public
     * @return  void
     */
    public function display($moduleName = '', $methodName = '')
    {
        if(empty($this->output)) $this->parse($moduleName, $methodName);
        echo $this->output;
    }
    /** 
     * Send data directly, for ajax requests.
     * 直接输出data数据，通常用于ajax请求中
     *
     * @param  misc    $data 
     * @param  string $type 
     * @access public
     * @return void
     */
    public function send($data, $type = 'json')
    {   
        if($type == 'json') echo json_encode($data);
        die(helper::removeUTF8Bom(ob_get_clean()));
    }   

    /**
     * Create a link to one method of one module.
     * 创建一个模块方法的链接
     *
     * @param   string         $moduleName    module name
     * @param   string         $methodName    method name
     * @param   string|array   $vars          the params passed, can be array(key=>value) or key1=value1&key2=value2
     * @param   string         $viewType      the view type
     * @access  public
     * @return  string the link string.
     */
    public function createLink($moduleName, $methodName = 'index', $vars = array(), $viewType = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        return helper::createLink($moduleName, $methodName, $vars, $viewType);
    }

    /**
     * Create a link to the inner method of current module.
     * 创建当前模块的一个方法链接
     * 
     * @param   string         $methodName    method name
     * @param   string|array   $vars          the params passed, can be array(key=>value) or key1=value1&key2=value2
     * @param   string         $viewType      the view type
     * @access  public
     * @return  string  the link string.
     */
    public function inlink($methodName = 'index', $vars = array(), $viewType = '')
    {
        return helper::createLink($this->moduleName, $methodName, $vars, $viewType);
    }

    /**
     * Location to another page.
     * 重定向到另一个页面
     * 
     * @param   string   $url   the target url.
     * @access  public
     * @return  void
     */
    public function locate($url)
    {
        header("location: $url");
        exit;
    }
}
