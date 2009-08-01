<?php 
echo <<<EOT
<?php
/**
 * The router file of {$claim[appName]}.
 *
{$claim[license]}  
 *
 * @copyright   {$claim[copyright]}
 * @author      {$claim[author]}
 * @package     {$claim[appName]}
 * @version     \$Id\$
 * @link        {$claim[website]}
 */

/* 记录最开始的时间。*/
\$timeStart = _getTime();

/* 包含必须的类文件。*/
include '../../../framework/router.class.php';
include '../../../framework/control.class.php';
include '../../../framework/model.class.php';
include '../../../framework/helper.class.php';

/* 如果zentao框架是通过pear方式安装的，可以将上面的注释掉，打开下面的四行语句。
//include 'zentao/framework/router.class.php';
//include 'zentao/framework/control.class.php';
//include 'zentao/framework/model.class.php';
//include 'zentao/framework/helper.class.php';

/* 实例化路由对象，并加载配置，连接到数据库。*/
\$app    = router::createApp('{$claim[appName]}', dirname(dirname(__FILE__)));
\$config = \$app->loadConfig('common');
\$dbh    = \$app->connectDB();

/* 设置客户端所使用的语言、风格。*/
\$app->setClientLang();
\$app->setClientTheme();
\$lang = \$app->loadLang('common');

/* 处理请求，加载相应的模块。*/
\$app->parseRequest();
\$app->loadModule();


/* Debug信息，监控页面的执行时间和内存占用。*/
\$timeUsed = round(_getTime() - \$timeStart, 4) * 1000;
\$memory   = round(memory_get_usage() / 1024, 1);

echo <<<EOD
<div style='text-align:center'>
  Powered By <a href='http://www.zentao.cn' target='_blank'>ZenTaoPHP</a>.
EOD;
if(\$config->debug)
{
    echo " Time:\$timeUsed ms; Mem</strong>:\$memory KB \n";
}
echo '</div>';
EOD;

/* 获取系统时间，微秒为单位。*/
function _getTime()
{
    list(\$usec, \$sec) = explode(" ", microtime());
    return ((float)\$usec + (float)\$sec);
}
EOT;
