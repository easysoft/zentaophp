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
/* Judge class config and function getWebRoot exists or not, make sure php shells can work. */
if(!class_exists('config')){class config{}}
if(!function_exists('getWebRoot')){function getWebRoot(){}}

/* 基本设置。        Basic settings. */
$config->version     = '2.2';             // ZenTaoPHP的版本，不要更改。The version of ZenTaoPHP. Don't change it. 
$config->encoding    = 'UTF-8';           // ZenTaoPHP的编码。          The encoding of ZenTaoPHP.                 
$config->cookieLife  = time() + 2592000;  // Cookie的生存时间。         The cookie life time.                      
$config->timezone    = 'Asia/Shanghai';   // 时区设置。 The time zone setting, for more see http://www.php.net/manual/en/timezones.php.
$config->webRoot     = '';                // The root path of the pms.

/* 请求设置。        The request settings. */
$config->requestType = 'PATH_INFO';       // 请求类型：PATH_INFO|GET。  The request type: PATH_INFO|GET, if PATH_INFO, must use url rewrite.
$config->pathType    = 'clean';           // 如果请求类型为PATH_INFO，确定路径类型。If the request type is PATH_INFO, the path type.    
$config->requestFix  = '-';               // PATH_INFO的分隔符。                    The divider in the url when PATH_INFO.              
$config->moduleVar   = 'm';               // 请求类型为GET：模块变量名。            requestType=GET: the module var name.               
$config->methodVar   = 'f';               // 请求类型为GET：模块变量名。            requestType=GET: the method var name.               
$config->viewVar     = 't';               // 请求类型为GET：视图变量名。            requestType=GET: the view var name.                 
$config->sessionVar  = 'sid';             // 请求类型为GET：session变量名。         requestType=GET: the session var name.              

/* 支持的视图类型。   Supported view formats. */
$config->views  = ',html,json,';

/* 支持的主题。       Supported themes. */
$config->themes['default'] = 'default';

/* 支持的语言。       Supported languages. */
$config->langs['zh-cn'] = '简体';
$config->langs['en']  = 'En';

/* 默认设置。         Default settings. */
$config->default = new stdclass();
$config->default->view   = 'html';        //默认视图。        Default view.
$config->default->lang   = 'en';          //默认语言。        Default language.
$config->default->theme  = 'default';     //默认主题。        Default theme.
$config->default->module = 'index';       //默认模块。        Default module.
$config->default->method = 'index';       //默认方法。        Default method.

/* 数据库设置。       Database settings. */
$config->db = new stdclass();
$config->db->persistant = false;           // 是否为持续连接。       Pconnect or not.
$config->db->driver     = 'mysql';         // 目前只支持MySQL数据库。Must be MySQL. Don't support other database server yet.
$config->db->encoding   = 'UTF8';          // 数据库编码。           Encoding of database.
$config->db->strictMode = false;           // 关闭MySQL的严格模式。  Turn off the strict mode of MySQL.
$config->db->prefix     = '';              // 数据库表名前缀。       The prefix of the table name.

/* 从数据库设置。     Slave database settings. */
$config->slaveDB = new stdClass();
$config->slaveDB->persistant = false;
$config->slaveDB->driver     = 'mysql';
$config->slaveDB->encoding   = 'UTF8';
$config->slaveDB->strictMode = false;

/* 系统框架配置。 */
$config->framework = new stdclass();
$config->framework->jsWithPrefix = true;
$config->framework->logDays      = 14;
$config->framework->stripXSS     = false;
$config->framework->purifier     = true;

define('LANG_CREATED', false);
/* 引用自定义的配置。 Include the custom config file. */
$myConfig = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'my.php';
if(file_exists($myConfig)) include $myConfig;
