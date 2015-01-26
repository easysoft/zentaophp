<?php
/**
 * The config file of zentaophp.  Don't modify this file directly, copy the item to my.php and change it.
 * ZenTaoPHP的config文件。如果更改配置，不要直接修改该文件，复制到my.php修改相应的值。
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/* Basic settings.  基本设置。*/
$config->version     = '2.2';             // The version of ZenTaoPHP. Don't change it. ZenTaoPHP的版本，不要更改。
$config->encoding    = 'UTF-8';           // The encoding of ZenTaoPHP.                 ZenTaoPHP的编码。
$config->cookieLife  = time() + 2592000;  // The cookie life time.                      Cookie的生存时间。
$config->timezone    = 'Asia/Shanghai';   // The time zone setting, for more see http://www.php.net/manual/en/timezones.php   时区设置。

/* The request settings.  请求设置。 */
$config->requestType = 'PATH_INFO';       // The request type: PATH_INFO|GET, if PATH_INFO, must use url rewrite.  请求类型：PATH_INFO|GET。
$config->pathType    = 'clean';           // If the request type is PATH_INFO, the path type.    如果请求类型为PATH_INFO，确定路径类型。
$config->requestFix  = '-';               // The divider in the url when PATH_INFO.              PATH_INFO的分隔符。
$config->moduleVar   = 'm';               // requestType=GET: the module var name.               请求类型为GET：模块变量名。
$config->methodVar   = 'f';               // requestType=GET: the method var name.               请求类型为GET：模块变量名。
$config->viewVar     = 't';               // requestType=GET: the view var name.                 请求类型为GET：视图变量名。
$config->sessionVar  = 'sid';             // requestType=GET: the session var name.              请求类型为GET：session变量名。

/* Views and themes. */
$config->views  = ',html,json,';          // Supported view formats.     支持的视图类型。

/* Supported themes.     支持的主题。 */
$config->themes['default'] = 'default';

/* Supported languages.  支持的语言。 */
$config->langs['zh-cn'] = '简体';
$config->langs['en']  = 'En';

/* Default settings.  默认设置。 */
$config->default = new stdclass();
$config->default->view   = 'html';        // Default view.                  默认视图。
$config->default->lang   = 'en';          // Default language.              默认语言。
$config->default->theme  = 'default';     // Default theme.                 默认主题。
$config->default->module = 'index';       // Default module.                默认模块。
$config->default->method = 'index';       // Default method.                默认方法。

/* Database settings.  数据库设置。 */
$config->db = new stdclass();
$config->db->persistant = false;           // Pconnect or not.                      是否为持续连接。
$config->db->driver     = 'mysql';         // Must be MySQL. Don't support other database server yet.   目前只支持MySQL数据库。
$config->db->encoding   = 'UTF8';          // Encoding of database.                 数据库编码。
$config->db->strictMode = false;           // Turn off the strict mode of MySQL.    关闭MySQL的严格模式。
$config->db->prefix     = '';              // The prefix of the table name.         数据库表名前缀。

/* Slave database settings.      从数据库设置。 */
$config->slaveDB = new stdClass();
$config->slaveDB->persistant = false;      
$config->slaveDB->driver     = 'mysql';    
$config->slaveDB->encoding   = 'UTF8';     
$config->slaveDB->strictMode = false;      

/* Include the custom config file.     引用自定义的配置。*/
$myConfig = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'my.php';
if(file_exists($myConfig)) include $myConfig;
