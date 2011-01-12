<?php
/**
 * The config file of ZenTaoPHP.
 *
 * Don't modify this file directly, copy the item to my.php and change it.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     config
 * @version     $Id$
 * @link        http://www.zentao.net
 */
/* Basic settings. */
$config->version     = '2.0';             // The version of zentaophp. Don't change it.
$config->encoding    = 'UTF-8';           // The encoding of znetaopms.
$config->cookieLife  = time() + 2592000;  // The cookie life time.
$config->timezone    = 'Asia/Shanghai';   // The time zone setting, for more see http://www.php.net/manual/en/timezones.php

/* The request settings. */
$config->requestType = 'PATH_INFO';       // The request type: PATH_INFO|GET, if PATH_INFO, must use url rewrite.
$config->pathType    = 'clean';           // If the request type is PATH_INFO, the path type.
$config->requestFix  = '-';               // The divider in the url when PATH_INFO.
$config->moduleVar   = 'm';               // requestType=GET: the module var name.
$config->methodVar   = 'f';               // requestType=GET: the method var name.
$config->viewVar     = 't';               // requestType=GET: the view var name.
$config->sessionVar  = 'sid';             // requestType=GET: the session var name.

/* Views and themes. */
$config->views  = ',html,json,';          // Supported view formats.
$config->themes = 'default';              // Supported themes.

/* Supported languages. */
$config->langs['zh-cn'] = '中文简体';
$config->langs['en']    = 'English';

/* Default settings. */
$config->default->view   = 'html';        // Default view.
$config->default->lang   = 'en';          // Default language.
$config->default->theme  = 'default';     // Default theme.
$config->default->module = 'index';       // Default module.
$config->default->method = 'index';       // Default method.

/* Database settings. */
$config->db->persistant = false;           // Pconnect or not.
$config->db->driver     = 'mysql';         // Must be MySQL. Don't support other database server yet.
$config->db->encoding   = 'UTF8';          // Encoding of database.
$config->db->strictMode = false;           // Turn off the strict mode of MySQL.

/* Include the custom config file. */
$myConfig = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'my.php';
if(file_exists($myConfig)) include $myConfig;
