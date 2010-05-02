<?php
/**
 * The demo app router file of ZenTaoPHP.
 *
 * All request should be routed by this router.
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
 * @copyright   Copyright 2009-2010 青岛易软天创网络科技有限公司(www.cnezsoft.com)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 * @link        http://www.zentaoms.com
 */
/* 记录最开始的时间。*/
$timeStart = _getTime();

/* 包含必须的类文件。*/
include '../../../framework/router.class.php';
include '../../../framework/control.class.php';
include '../../../framework/model.class.php';
include '../../../framework/helper.class.php';

/* 如果zentao框架是通过pear方式安装的，可以将上面的注释掉，打开下面的四行语句。*/
//include 'zentao/framework/router.class.php';
//include 'zentao/framework/control.class.php';
//include 'zentao/framework/model.class.php';
//include 'zentao/framework/helper.class.php';

/* 实例化路由对象，并加载配置，连接到数据库，加载common模块。*/
$app    = router::createApp('demo', dirname(dirname(__FILE__)));
$config = $app->loadConfig('common');
$dbh    = $app->connectDB();
$common = $app->loadCommon();

/* 设置客户端所使用的语言、风格。*/
$app->setClientLang();
$app->setClientTheme();
$lang = $app->loadLang('common');

/* 处理请求，加载相应的模块。*/
$app->parseRequest();
$app->loadModule();

/* Debug信息，监控页面的执行时间和内存占用。*/
$timeUsed = round(_getTime() - $timeStart, 4) * 1000;
$memory   = round(memory_get_usage() / 1024, 1);

echo <<<EOT
<div style='text-align:center'>
  Powered By <a href='http://www.zentaoms.com' target='_blank'>ZenTaoPHP</a>.
EOT;
if($config->debug)
{
    echo " Time:$timeUsed ms; Mem</strong>:$memory KB ";
}
echo '</div>';

/* 获取系统时间，微秒为单位。*/
function _getTime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
