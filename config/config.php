<?php
/**
 * ZenTaoPHP的config文件。如果更改配置，不要直接修改该文件，复制到my.php修改相应的值。
 * The config file of zentaophp.  Don't modify this file directly, copy the item to my.php and change it.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/* 保证在命令行环境也能运行。Make sure to run in ztcli env. */
if(!class_exists('config')){class config{}}
if(!function_exists('getWebRoot')){function getWebRoot(){}}

/* 基本设置。Basic settings. */
$config->version     = '3.0';             // ZenTaoPHP的版本。 The version of ZenTaoPHP. Don't change it. 
$config->encoding    = 'UTF-8';           // ZenTaoPHP的编码。 The encoding of ZenTaoPHP.                 
$config->cookieLife  = time() + 2592000;  // Cookie的生存时间。The cookie life time.                      
$config->timezone    = 'Asia/Shanghai';   // 时区设置。        The time zone setting, for more see http://www.php.net/manual/en/timezones.php.
$config->webRoot     = '';                // URL根目录。       The root path of the url.

/* 框架路由相关设置。Routing settings. */
$config->requestType = 'PATH_INFO';       // 请求类型：PATH_INFO|PATHINFO2|GET。    The request type: PATH_INFO|PATH_INFO2|GET.
$config->requestFix  = '-';               // PATH_INFO和PATH_INFO2模式的分隔符。    The divider in the url when PATH_INFO|PATH_INFO2.              
$config->moduleVar   = 'm';               // 请求类型为GET：模块变量名。            requestType=GET: the module var name.               
$config->methodVar   = 'f';               // 请求类型为GET：模块变量名。            requestType=GET: the method var name.               
$config->viewVar     = 't';               // 请求类型为GET：视图变量名。            requestType=GET: the view var name.                 
$config->sessionVar  = 'sid';             // 请求类型为GET：session变量名。         requestType=GET: the session var name.              
$config->views       = ',html,json,';     // 支持的视图类型。                       Supported view formats. 

/* 支持的主题和语言。Supported thems and languages. */
$config->themes['default'] = 'default'; 
$config->langs['zh-cn']    = '简体';
$config->langs['en']       = 'En';

/* 设备类型视图文件前缀。The prefix for view file for different device. */ 
$config->devicePrefix['mhtml'] = 'm.';

/* 默认值设置。Default settings. */
$config->default = new stdclass();
$config->default->view   = 'html';        //默认视图。 Default view.
$config->default->lang   = 'en';          //默认语言。 Default language.
$config->default->theme  = 'default';     //默认主题。 Default theme.
$config->default->module = 'index';       //默认模块。 Default module.
$config->default->method = 'index';       //默认方法。 Default method.

/* 数据库设置。Database settings. */
$config->db = new stdclass();
$config->slaveDB = new stdClass();
$config->db->persistant      = false;     // 是否为持续连接。       Pconnect or not.
$config->db->driver          = 'mysql';   // 目前只支持MySQL数据库。Must be MySQL. Don't support other database server yet.
$config->db->encoding        = 'UTF8';    // 数据库编码。           Encoding of database.
$config->db->strictMode      = false;     // 关闭MySQL的严格模式。  Turn off the strict mode of MySQL.
$config->db->prefix          = '';        // 数据库表名前缀。       The prefix of the table name.
$config->slaveDB->persistant = false;
$config->slaveDB->driver     = 'mysql';
$config->slaveDB->encoding   = 'UTF8';
$config->slaveDB->strictMode = false;

/* 系统框架配置。Framework settings. */
$config->framework = new stdclass();
$config->framework->multiLanguage = true;  // 是否启用多语言功能。              Whether enable multi lanuage or not.
$config->framework->multiTheme    = true;  // 是否启用多风格功能。              Whether enable multi theme or not.
$config->framework->detectDevice  = true;  // 是否启用设备检测功能。            Whether enable device detect or not.
$config->framework->autoConnectDB = true;  // 是否自动连接数据库。              Whether auto connect database or not.
$config->framework->extensionLevel= 0;     // 0=>无扩展,1=>公共扩展,2=>站点扩展 0=>no extension, 1=> common extension, 2=> every site has it's extension.
$config->framework->jsWithPrefix  = true;  // js::set()输出的时候是否增加前缀。 When us js::set(), add prefix or not.
$config->framework->logDays       = 14;    // 日志文件保存的天数。              The days to save log files.
$config->framework->filterBadKeys = true;  // 是否过滤不合要求的键值。          Whether filter bad keys or not.
$config->framework->filterTrojan  = true;  // 是否过滤木马攻击代码。            Whether strip trojan code or not.
$config->framework->filterXSS     = true;  // 是否过滤XSS攻击代码。             Whether strip xss code or not.
$config->framework->purifier      = true;  // 是否对数据做purifier处理。        Whether purifier data or not.

/* 引用自定义的配置。 Include the custom config file. */
$myConfig = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'my.php';
if(file_exists($myConfig)) include $myConfig;
