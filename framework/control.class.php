<?php
/**
 * The control class file of ZenTaoPHP.
 *
 * ZenTaoPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * ZenTaoPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with ZenTaoPHP.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright 2009-2010, Chunsheng Wang
 * @author      Chunsheng Wang <wwccss@gmail.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 * @link        http://www.zentao.cn
 */

/**
 * 控制器基类。
 * 
 * @package ZenTaoPHP
 */
class control
{
    /**
     * 全局的$app对象。
     * 
     * @var object
     * @access protected
     */
    protected $app;

    /**
     * 全局的$config对象。 
     * 
     * @var object
     * @access protected
     */
    protected $config;

    /**
     * 全局的$lang对象。
     * 
     * @var object
     * @access protected
     */
    protected $lang;

    /**
     * 全局的$dbh（数据库访问句柄）对象。
     * 
     * @var object
     * @access protected
     */
    protected $dbh;

    /**
     * dao对象。
     * 
     * @var object
     * @access protected
     */
    public $dao;

    /**
     * POST对象。
     * 
     * @var ojbect
     * @access public
     */
    public $post;

    /**
     * session对象。
     * 
     * @var ojbect
     * @access public
     */
    public $session;

    /**
     * server对象。
     * 
     * @var ojbect
     * @access public
     */
    public $server;

    /**
     * global对象。
     * 
     * @var ojbect
     * @access public
     */
    public $global;

    /**
     * 所属模块的名字。
     * 
     * @var string
     * @access protected
     */
    protected $moduleName;

    /**
     * 模块所处的路径。
     * 
     * @var string
     * @access protected
     */
    protected $modulePath;

    /**
     * 要加载的model文件。
     * 
     * @var string
     * @access private
     */
    private $modelFile;

    /**
     * 派生的model文件。
     * 
     * @var string
     * @access private
     */
    private $myModelFile;

    /**
     * 记录赋值到view的所有变量。
     * 
     * @var object
     * @access public
     */
    public $view; 

    /**
     * 视图类型
     * 
     * @var string
     * @access private
     */
    private $viewType;

    /**
     * 要加载的view文件。
     * 
     * @var string
     * @access private
     */
    private $viewFile;

    /**
     * 要输出的内容。
     * 
     * @var string
     * @access private
     */
    private $output;

    /**
     * 路径分隔符。
     * 
     * @var string
     * @access protected
     */
    protected $pathFix;

    /**
     * 构造函数：
     *
     * 1. 引用全局对象，使之可以通过成员变量访问。
     * 2. 设置模块相应的路径信息，并加载对应的model文件。
     * 3. 自动将$lang和$config赋值到模板。
     * 
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        /* 引用全局对象，并赋值。*/
        global $app, $config, $lang, $dbh;
        $this->app        = $app;
        $this->config     = $config;
        $this->lang       = $lang;
        $this->dbh        = $dbh;
        $this->pathFix    = $this->app->getPathFix();
        $this->viewType   = $this->app->getViewType();

        $this->setModuleName($moduleName);
        $this->setModulePath();
        $this->setMethodName($methodName);

        /* 自动加载当前模块的model文件。*/
        $this->loadModel();

        /* 自动将$app, $config和$lang赋值到模板中。*/
        $this->assign('app',    $app);
        $this->assign('lang',   $lang);
        $this->assign('config', $config);

        if(isset($config->super2OBJ) and $config->super2OBJ) $this->setSuperVars();
    }

    //-------------------- model相关的方法。--------------------//
    //
    /* 设置模块名。*/
    private function setModuleName($moduleName = '')
    {
        $this->moduleName = $moduleName ? strtolower($moduleName) : $this->app->getModuleName();
    }

    /* 设置模块路径。*/
    private function setModulePath()
    {
        $this->modulePath = $this->app->getModuleRoot() . $this->moduleName . $this->pathFix;
    }

    /* 设置方法名。*/
    private function setMethodName($methodName = '')
    {
        $this->methodName = $methodName ? strtolower($methodName) : $this->app->getMethodName();
    }

    /**
     * 设置model文件。
     * 
     * @param   string      $moduleName     模块名字。
     * @access  private
     * @return void
     */
    private function setModelFile($moduleName)
    {
        $this->modelFile = $this->app->getModuleRoot() . strtolower(trim($moduleName)) . $this->pathFix . 'model.php';
        return file_exists($this->modelFile);
    }

    /**
     * 设置派生的model文件。
     * 
     * @param   string      $moduleName     模块名字。
     * @access  private
     * @return void
     */
    private function setMyModelFile()
    {
        $this->myModelFile = str_replace('model.php', 'mymodel.php', $this->modelFile);
        return file_exists($this->myModelFile);
    }

    /**
     * 加载某一个模块的model文件。
     * 
     * @param   string  $moduleName     模块名字，如果为空，则取当前的模块名作为model名。
     * @access  public
     * @return  void
     */
    public function loadModel($moduleName = '')
    {
        /* 如果没有指定module名，则取当前加载的模块的名作为model名。*/
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(!$this->setModelFile($moduleName)) return false;

        $modelClass = $moduleName. 'Model';
        helper::import($this->modelFile);
        
        /* 存在派生的model文件，则加载。*/
        if($this->setMyModelFile())
        {
            helper::import($this->myModelFile);
            $modelClass = 'my' . $modelClass;
        }

        if(!class_exists($modelClass)) $this->app->error(" The model $modelClass not found", __FILE__, __LINE__, $exit = true);
        $this->$moduleName = new $modelClass();
        if(isset($this->config->db->dao) and $this->config->db->dao)
        {
            $this->dao = $this->$moduleName->dao;
        }
        return $this->$moduleName;
    }

    /**
     * 设置超全局变量。
     * 
     * @access protected
     * @return void
     */
    protected function setSuperVars()
    {
        $this->post    = $this->app->post;
        $this->server  = $this->app->server;
        $this->session = $this->app->session;
        $this->global  = $this->app->global;
    }

    //-------------------- 加载view相关的方法。--------------------//
    /**
     * 设置视图文件。
     * 
     * 某一个module的控制器可以加载另外一个module的视图文件。
     *
     * @param string $moduleName    模块名。
     * @param string $methodName    方法名。
     * @access private
     * @return string               对应的视图文件。
     */
    private function setViewFile($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));

        $modulePath   = $this->app->getModuleRoot() . strtolower($moduleName) . $this->pathFix;
        $viewExtPath  = $modulePath . "opt{$this->pathFix}view{$this->pathFix}";

        /* 主视图文件，扩展视图文件和扩展钩子文件。*/
        $mainViewFile = $modulePath . 'view' . $this->pathFix . $methodName . '.' . $this->viewType . '.php';
        $extViewFile  = $viewExtPath . $methodName . ".{$this->viewType}.php";
        $extHookFile  = $viewExtPath . $methodName . ".{$this->viewType}.hook.php";

        $viewFile = file_exists($extViewFile) ? $extViewFile : $mainViewFile;
        if(!file_exists($viewFile)) $this->app->error("the view file $viewFile not found", __FILE__, __LINE__, $exit = true);
        if(file_exists($extHookFile)) return array('viewFile' => $viewFile, 'hookFile' => $extHookFile);
        return $viewFile;
    }

    /**
     * 赋值一个变量到view视图。
     * 
     * @param   string  $name       赋值到视图文件中的变量名。
     * @param   mixed   $value      所对应的值。
     * @access  public
     * @return  void
     */
    public function assign($name, $value)
    {
        $this->view->$name = $value;
    }

    /**
     * 重置output内容。
     * 
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->output = '';
    }

    /**
     * 解析视图文件。
     *
     * 如果没有指定模块名和方法名，则取当前模块的当前方法。
     *
     * @param string $moduleName    模块名。
     * @param string $methodName    方法名。
     * @access public
     * @return void
     */
    public function parse($moduleName = '', $methodName = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(empty($methodName)) $methodName = $this->methodName;

        if($this->viewType == 'json') $this->parseJSON($moduleName, $methodName);
        if($this->viewType == 'html') $this->parseHtml($moduleName, $methodName);
        return $this->output;
    }

    /* 解析JSON格式的输出。*/
    private function parseJSON($moduleName, $methodName)
    {
        unset($this->view->app);
        unset($this->view->config);
        unset($this->view->lang);
        unset($this->view->pager);
        unset($this->view->header);
        unset($this->view->position);
        $this->output = json_encode($this->view);
    }

    /* HTML格式。*/
    private function parseHtml($moduleName, $methodName)
    {
        /* 设置视图文件。*/
        $viewFile = $this->setViewFile($moduleName, $methodName);
        if(is_array($viewFile)) extract($viewFile);

        /* 切换到视图文件所在的目录，以保证视图文件中的包含路径有效。*/
        $currentPWD = getcwd();
        chdir(dirname($viewFile));

        extract((array)$this->view);
        ob_start();
        include $viewFile;
        if(isset($hookFile)) include $hookFile;
        $this->output .= ob_get_contents();
        ob_end_clean();

        /* 最后还要切换到原来的目录。*/
        chdir($currentPWD);
    }

    /**
     * 获取某一个模块的某一个方法的内容。
     * 
     * 如果没有指定模块名，则取当前模块当前方法的视图。如果指定了模块和方法，则调用对应的模块方法的视图内容。
     *
     * @param   string  $moduleName    模块名。
     * @param   string  $methodName    方法名。
     * @param   array   $params        方法参数。
     * @access  public
     * @return  string
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

        /* 设置被调用的模块的路径及相应的文件。*/
        $modulePath        = $this->app->getModuleRoot() . strtolower($moduleName) . $this->pathFix;
        $moduleControlFile = $modulePath . 'control.php';
        $actionExtFile     = $modulePath . "opt{$this->pathFix}control{$this->pathFix}" . strtolower($methodName) . '.php';
        $file2Included     = file_exists($actionExtFile) ? $actionExtFile : $moduleControlFile;

        /* 加载控制文件。*/
        if(!file_exists($file2Included)) $this->app->error("The control file $file2Included not found", __FILE__, __LINE__, $exit = true);
        chdir(dirname($file2Included));
        if($moduleName != $this->moduleName) helper::import($file2Included);
        
        /* 设置要调用的类的名称。*/
        $className = class_exists("my$moduleName") ? "my$moduleName" : $moduleName;
        if(!class_exists($className)) $this->app->error(" The class $className not found", __FILE__, __LINE__, $exit = true);

        if(!is_array($params)) parse_str($params, $params);
        $module = new $className($moduleName, $methodName);

        ob_start();
        call_user_func_array(array($module, $methodName), $params);
        $output = ob_get_contents();
        ob_end_clean();
        unset($module);
        return $output;
    }

    /**
     * 显示视图内容。 
     * 
     * @param   string  $moduleName    模块名。
     * @param   string  $methodName    方法名。
     * @access  public
     * @return  void
     */
    public function display($moduleName = '', $methodName = '')
    {
        if(empty($this->output)) $this->parse($moduleName, $methodName);
        echo $this->output;
        if($this->viewType == 'json') die();
    }

    /**
     * 生成某一个模块某个方法的链接。
     * 
     * @param   string  $moduleName    模块名。
     * @param   string  $methodName    方法名。
     * @param   mixed   $vars          要传递的参数，可以是数组，array('var1'=>'value1')。也可以是var1=value1&var2=value2的形式。
     * @param   string  $viewType      视图格式。
     * @access  public
     * @return  string
     */
    public function createLink($moduleName, $methodName = 'index', $vars = array(), $viewType = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        return helper::createLink($moduleName, $methodName, $vars, $viewType);
    }

    /**
     * 生成对本模块某个方法的链接。
     * 
     * @param   string  $methodName    方法名。
     * @param   mixed   $vars          要传递的参数，可以是数组，array('var1'=>'value1')。也可以是var1=value1&var2=value2的形式。
     * @param   string  $viewType      视图格式。
     * @access  public
     * @return  string
     */
    public function inlink($methodName = 'index', $vars = array(), $viewType = '')
    {
        return helper::createLink($this->moduleName, $methodName, $vars, $viewType);
    }

    /**
     * 跳转到另外一个页面。
     * 
     * @param   string   $url   要跳转的url地址。
     * @access  public
     * @return  void
     */
    public function locate($url)
    {
        header("location: $url");
        exit;
    }
}
