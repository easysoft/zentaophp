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
 * @copyright   Copyright 2009, Chunsheng Wang
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
     * 记录赋值到view的所有变量。
     * 
     * @var array
     * @access private
     */
    private $vars = array();

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
    public function __construct()
    {
        /* 引用全局对象，并赋值。*/
        global $app, $config, $lang, $dbh;
        $this->app        = $app;
        $this->config     = $config;
        $this->lang       = $lang;
        $this->dbh        = $dbh;
        $this->pathFix    = $this->app->getPathFix();
        $this->moduleName = $this->app->getModuleName();
        $this->modulePath = $this->app->getModuleRoot() . $this->moduleName . $this->pathFix;

        /* 自动加载当前模块的model文件。*/
        $this->loadModel();

        /* 自动将$config和$lang赋值到模板中。*/
        $this->assign('lang',   $lang);
        $this->assign('config', $config);
    }

    //-------------------- model相关的方法。--------------------//

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
        if(!class_exists($modelClass)) $this->app->error(" The model $modelClass not found", __FILE__, __LINE__, $exit = true);
        $this->$moduleName = new $modelClass();
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
        $viewFile = $this->app->getModuleRoot() . $moduleName . $this->pathFix . 'view' . $this->pathFix . $methodName . '.' . $this->app->getViewType() . '.php';
        if(!file_exists($viewFile)) $this->app->error("the view file $viewFile not found", __FILE__, __LINE__, $exit = true);
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
        $this->vars[$name] = $value;
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
        if(empty($methodName)) $methodName = $this->app->getMethodName();
        $viewFile = $this->setViewFile($moduleName, $methodName);

        /* 切换到视图文件所在的目录，以保证视图文件中的包含路径有效。*/
        $currentPWD = getcwd();
        chdir(dirname($viewFile));

        extract($this->vars);
        ob_start();
        include $viewFile;
        $this->output .= ob_get_contents();
        ob_end_clean();

        /* 最后还要切换到原来的目录。*/
        chdir($currentPWD);
    }

    /**
     * 获取视图内容。
     * 
     * 可以将某一个视图文件的内容作为字符串返回。 
     *
     * @param   string  $moduleName    模块名。
     * @param   string  $methodName    方法名。
     * @param   bool    $clear         是否清除原来的视图内容。
     * @access  public
     * @return  string
     */
    public function fetch($moduleName = '', $methodName = '', $clear = false)
    {
        if($clear) $this->clear();
        if(empty($this->output)) $this->parse($moduleName, $methodName);
        return $this->output;
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
    }

    /**
     * 生成某一个模块某个方法的链接。
     * 
     * @param   string  $moduleName    模块名。
     * @param   string  $methodName    方法名。
     * @param   mixed   $vars          要传递的参数，可以是数组，array('var1'=>'value1')。也可以是var1=value1&var2=value2的形式。
     * @access  public
     * @return  string
     */
    public function createLink($moduleName, $methodName = 'index', $vars = array())
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        return helper::createLink($moduleName, $methodName, $vars);
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
