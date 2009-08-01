<?php if(!defined('IN_SHELL')) exit;?>
<?php
/**
 * The control file of generator module of ZenTaoPHP.
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
 * @copyright   Copyright: 2009 Chunsheng Wang
 * @author      Chunsheng Wang <wwccss@263.net>
 * @package     ZenTaoPHP
 * @version     $Id: control.php 1210 2009-06-20 00:02:49Z wwccss $
 * @link        http://www.zentao.cn
 */
class generator extends control
{
    var $appRouter2C = '';  // 要创建的应用的路由对象。
    var $appClaim2C  = '';  // 要创建的应用的声明文件，用来定义授权、版权、作者等信息。
    var $module2C    = '';  // 要创建的模块，通过下面的赋值会一个对象。

    /* 构造函数。*/
    public function __construct()
    {
        parent::__construct();
    }
    
    /* 创建某一个应用的入口函数。*/
    public function createApp($appName)
    {
        $this->setApp($appName);
        $this->checkAppClaim();
        $this->loadAppClaim();
        $this->initAppDir();
        $this->saveConfig();
        $this->saveRouter();
        printf($this->lang->afterAppNote, $this->appRouter2C->getAppRoot(), $this->appRouter2C->getConfigRoot() . 'config.php');
    }

    /* 设置要创建的应用。*/
    private function setApp($appName)
    {
        /* 先尝试从环境变量中取appRoot的路径，没有设置则取框架所在的目录为基准，计算应用程序的目录。*/
        $appRoot = getenv('ZT_APP_ROOT');
        if(!empty($appRoot))
        {
            $appRoot = realpath($appRoot);
        }
        else
        {
            $appRoot = $this->app->getBasePath() . 'app' . $this->pathFix . $appName . $this->pathFix;
        }

        if(!is_dir($appRoot)) mkdir($appRoot, 0755, $recursieve = true);
        $this->appRouter2C = router::createApp($appName, $appRoot);
        $this->appClaim2C  = $appRoot . $this->pathFix . 'claim.php';
    }
    
    /* 检查应用所需要的配置文件是否存在。*/
    private function checkAppClaim()
    {
        if(!file_exists($this->appClaim2C)) 
        {
            $this->saveClaim();
            printf($this->lang->fillClaim, $this->appClaim2C);
            exit;
        }
    }

    /* 加载应用的声明。*/
    private function loadAppClaim()
    {
        include $this->appClaim2C;
        $claim['license'] = ' * ' . str_replace("\n", "\n * ", trim($claim['license']));    // 在license的每一行前面添加 *。
        $this->assign('claim', $claim);
    }

    /* 建立某一个应用的目录结构。*/
    private function initAppDir()
    {
        @mkdir($this->appRouter2C->getAppRoot());
        @mkdir($this->appRouter2C->getConfigRoot());
        @mkdir($this->appRouter2C->getModuleRoot());
        @mkdir($this->appRouter2C->getModuleRoot() . 'common');
        @mkdir($this->appRouter2C->getCacheRoot());
        @chmod($this->appRouter2C->getCacheRoot(), 0757);
        @mkdir($this->appRouter2C->getThemeRoot(), 0755, $recursieve = true);

        touch($this->appRouter2C->getModuleRoot() . 'common' . $this->pathFix . 'header.html.php');
        touch($this->appRouter2C->getModuleRoot() . 'common' . $this->pathFix . 'footer.html.php');
    }

    /* 生成应用的声明文件。*/
    private function createClaim()
    {
        return($this->fetch($this->moduleName, __FUNCTION__));
    }
    
    /* 保存应用的声明文件。*/
    private function saveClaim()
    {
        $claimContent = $this->createClaim();
        file_put_contents($this->appClaim2C, $claimContent);
    }

    /* 生成应用的config文件。*/
    private function createConfig()
    {
        return($this->fetch($this->moduleName, __FUNCTION__));
    }

    /* 保存config文件。*/
    private function saveConfig()
    {
        $configContent = $this->createConfig();
        file_put_contents($this->appRouter2C->getConfigRoot() . 'config.php', $configContent);
    }

    /* 生成应用的router文件。*/
    private function createRouter()
    {
        return($this->fetch($this->moduleName, __FUNCTION__, $clear = true));
    }

    /* 保存router文件。*/
    private function saveRouter()
    {
        $content = $this->createRouter();
        file_put_contents($this->appRouter2C->getAppRoot() . 'www' . $this->pathFix . 'index.php', $content);
    }

    /* 生成模块的入口函数。*/
    public function createModule($appName, $moduleName, $tableName)
    {
        $this->setApp($appName);
        $this->checkAppClaim();
        $this->loadAppClaim();
        $this->appRouter2C->loadConfig('common');
        $this->generator->setDBH($this->appRouter2C->connectDB());
        $this->setModuleToCreate($moduleName, $tableName);
        $this->initDir();
        $this->saveLang('zh-cn');
        $this->saveLang('en');
        $this->saveControl();
        $this->saveModel();
        $this->saveIndex();
        $this->saveCreate();
        $this->saveRead();
        $this->saveUpdate();
        $this->saveDelete();
    }

    /* 设置要创建的模块的各种属性。*/
    private function setModuleToCreate($moduleName, $tableName)
    {
        $this->module2C->name    = $moduleName;
        $this->module2C->table   = $tableName;
        $this->module2C->fields  = $this->generator->getFields($tableName);
        $this->module2C->path    = $this->appRouter2C->getModuleRoot() . $this->pathFix . $moduleName . $this->pathFix;
        $this->module2C->lang    = $this->module2C->path . 'lang' . $this->pathFix;
        $this->module2C->view    = $this->module2C->path . 'view' . $this->pathFix;
        $this->module2C->control = $this->module2C->path . 'control.php';
        $this->module2C->model   = $this->module2C->path . 'model.php';
        $this->module2C->index   = $this->module2C->view . 'index.html.php';
        $this->module2C->create  = $this->module2C->view . 'create.html.php';
        $this->module2C->read    = $this->module2C->view . 'read.html.php';
        $this->module2C->update  = $this->module2C->view . 'update.html.php';
        $this->module2C->delete  = $this->module2C->view . 'delete.html.php';
    }

    /* 初始化要创建的模块的目录。*/
    private function initDir()
    {
        if(!file_exists($this->module2C->path)) mkdir($this->module2C->path);
        if(!file_exists($this->module2C->lang)) mkdir($this->module2C->lang);
        if(!file_exists($this->module2C->view)) mkdir($this->module2C->view);
    }

    /* 创建control文件。*/
    private function createControl()
    {
        $this->assign('module2C', $this->module2C->name);
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }
    
    /* 保存control文件。*/
    private function saveControl()
    {
        $controlContent = $this->createControl();
        file_put_contents($this->module2C->control, $controlContent);
    }

    /* 生成model文件。*/
    private function createModel()
    {
        $this->assign('module2C', $this->module2C->name);
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }

    /* 保存model文件。*/
    private function saveModel()
    {
        $modelContent = $this->createModel();
        file_put_contents($this->module2C->model, $modelContent);
    }

    /* 生成语言文件。*/
    private function createLang($langName)
    {
        $this->assign('langName',        $langName);
        $this->assign('module2C',        $this->module2C->name);
        $this->assign('tableName',       $this->module2C->table);
        $this->assign('fields',          $this->module2C->fields);
        $this->assign('fieldsMaxLength', $this->generator->getFieldsMaxLength($this->module2C->fields));
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }

    /* 保存语言文件。*/
    private function saveLang($langName)
    {
        $langFile    = $this->module2C->lang . $langName . '.php';
        $langContent = $this->createLang($langName);
        file_put_contents($langFile, $langContent);
    }

    private function createIndex()
    {
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }

    private function saveIndex()
    {
        file_put_contents($this->module2C->index, $this->createIndex());
    }

    private function createCreate()
    {
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }

    private function saveCreate()
    {
        file_put_contents($this->module2C->create, $this->createCreate());
    }

    private function createRead()
    {
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }

    private function saveRead()
    {
        file_put_contents($this->module2C->read, $this->createRead());
    }

    private function createUpdate()
    {
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }

    private function saveUpdate()
    {
        file_put_contents($this->module2C->update, $this->createUpdate());
    }

    private function createDelete()
    {
        return $this->fetch($this->moduleName, __FUNCTION__, $clear = true);
    }

    private function saveDelete()
    {
        file_put_contents($this->module2C->delete, $this->createDelete());
    }
}
