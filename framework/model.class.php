<?php
/**
 * The model class file of ZenTaoPHP.
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
 * 模型基类。
 * 
 * @package ZenTaoPHP
 */
class model
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
     * 此model所属模块的名字。
     * 
     * @var string
     * @access protected
     */
    protected $moduleName;

    /**
     * 此model所属模块所在的路径。
     * 
     * @var string
     * @access protected
     */
    protected $modulePath;

    /**
     * 模块对应的配置文件。
     * 
     * @var string
     * @access protected
     */
    protected $moduleConfig;

    /**
     * 模块的语言文件。
     * 
     * @var string
     * @access protected
     */
    protected $moduleLang;

    /**
     * 消息变量，用来记录某一个方法的返回消息。
     * 
     * @var string
     * @access protected
     */
    protected $message;

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
     * 构造函数：
     *
     * 1. 引用全局变量，使之可以通过成员属性访问。
     * 2. 设置当前模块的路径、配置、语言等信息，并加载相应的文件。
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        global $app, $config, $lang, $dbh;
        $this->app    = $app;
        $this->config = $config;
        $this->lang   = $lang;
        $this->dbh    = $dbh;

        $this->setModuleName();
        $this->setModulePath();
        $this->setModuleConfig();
        $this->loadModuleConfig();
        $this->setModuleLang();
        $this->loadModuleLang();

        if(isset($config->db->dao)   and $config->db->dao)   $this->loadDAO();
        if(isset($config->super2OBJ) and $config->super2OBJ) $this->setSuperVars();
    }

    /**
     * 设置模块名：将类名中的model替换掉即为模块名。
     * 没有使用$app->getModule()方法，因为它返回的是当前调用的模块。
     * 而在一次请求中，当前模块的control文件很有可能会调用其他模块的model。
     * 
     * @access protected
     * @return void
     */
    protected function setModuleName()
    {
        $parentClass = get_parent_class($this);
        $selfClass   = get_class($this);
        $className   = $parentClass == 'model' ? $selfClass : $parentClass;
        $this->moduleName = strtolower(str_ireplace('Model', '', $className));
    }

    /**
     * 设置模块所处的路径。
     * 
     * @access protected
     * @return void
     */
    protected function setModulePath()
    {
        $this->modulePath = $this->app->getModuleRoot() . $this->moduleName . $this->app->getPathFix();
    }

    /**
     * 设置模块的配置文件。
     * 
     * @access protected
     * @return void
     */
    protected function setModuleConfig()
    {
        $this->moduleConfig = $this->modulePath. 'config.php';
    }

    /**
     * 加载模块的配置文件。
     * 
     * @access protected
     * @return void
     */
    protected function loadModuleConfig()
    {
        if(file_exists($this->moduleConfig)) $this->app->loadConfig($this->moduleName);
    }

    /**
     * 设置模块的语言文件。
     * 
     * @access protected
     * @return void
     */
    protected function setModuleLang()
    {
        $this->moduleLang = $this->modulePath. 'lang' . $this->app->getPathFix() . $this->app->getClientLang() . '.php';
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

    /**
     * 加载某一个模块的model文件。
     * 
     * @param   string  $moduleName     模块名字，如果为空，则取当前的模块名作为model名。
     * @access  public
     * @return  void
     */
    public function loadModel($moduleName)
    {
        if(empty($moduleName)) return false;
        $modelFile = $this->setModelFile($moduleName);
        if(!file_exists($modelFile)) return false;

        $modelClass = $moduleName. 'Model';
        helper::import($modelFile);
        
        if(!class_exists($modelClass)) $this->app->error(" The model $modelClass not found", __FILE__, __LINE__, $exit = true);
        $this->$moduleName = new $modelClass();
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
        $modelFile = $this->app->getModuleRoot() . strtolower(trim($moduleName)) . $this->app->getPathFix() . 'model.php';
        return $modelFile;
    }

    /**
     * 加载模块的语言文件。
     * 
     * @access protected
     * @return void
     */
    protected function loadModuleLang()
    {
        if(file_exists($this->moduleLang)) $this->app->loadLang($this->moduleName);
    }

    /**
     * 获取最新的消息记录。
     * 
     * @access public
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 设置消息记录。
     * 
     * @param string $message 
     * @access protected
     * @return void
     */
    protected function setMessage($message)
    {
        $this->message = $message;
    }
    
    /**
     * 追加消息记录。
     * 
     * @param string $message 
     * @access protected
     * @return void
     */
    protected function appendMessage($message)
    {
        $this->message .= $message;
    }

    //-------------------- 数据库操作相应的方法。--------------------//

    /**
     * 加载DAO类，并返回对象。
     * 
     * @access private
     * @return void
     */
    private function loadDAO()
    {
        $this->dao = $this->app->loadClass('dao');
    }

    /**
     * 返回key=>value形式的数组。
     * 
     * @param string $sql           要执行的sql语句。 
     * @param string $keyField      key字段名。
     * @param string $valueField    value字段名。
     * @access protected
     * @return void
     */
    public function fetchPairs($sql, $keyField = '', $valueField = '')
    {
        $pairs = array();
        $stmt  = $this->dbh->query($sql, PDO::FETCH_ASSOC);
        $ready = false;
        while($row = $stmt->fetch())
        {
            if(!$ready)
            {
                if(empty($keyField)) $keyField = key($row);
                if(empty($valueField)) 
                {
                    end($row);
                    $valueField = key($row);
                }
                $ready = true;
            }
            $pairs[$row[$keyField]] = $row[$valueField];
        }
        return $pairs;
    }
}    
