<?php
/**
 * The config file of ZenTaoPHP.
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
$config->version     = '1.0.STABLE.090620'; // 版本号，切勿修改。
$config->debug       = true;              // 是否打开debug功能。
$config->webRoot     = '/';               // web网站的根目录。
$config->encoding    = 'UTF-8';           // 网站的编码。
$config->cookiePath  = '/';               // cookie的有效路径。
$config->cookieLife  = time() + 2592000;  // cookie的生命周期。

$config->requestType = 'PATH_INFO';       // 如何获取当前请求的信息，可选值：PATH_INFO|GET
$config->pathType    = 'clean';           // requestType=PATH_INFO: 请求url的格式，可选值为full|clean，full格式会带有参数名称，clean则只有取值。
$config->requestFix  = '/';               // requestType=PATH_INFO: 请求url的分隔符，可选值为斜线、下划线、减号。后面两种形式有助于SEO。
$config->moduleVar   = 'm';               // requestType=GET: 模块变量名。
$config->methodVar   = 'f';               // requestType=GET: 方法变量名。
$config->viewVar     = 't';               // requestType=GET: 模板变量名。

$config->views       = ',html,xml,json,txt,csv,doc,pdf,'; // 支持的视图列表。
$config->langs       = 'zh-cn,zh-tw,zh-hk,en';            // 支持的语言列表。
$config->themes      = 'default';                         // 支持的主题列表。

$config->default->view   = 'html';                      // 默认的视图格式。
$config->default->lang   = 'zh-cn';                     // 默认的语言。
$config->default->theme  = 'default';                   // 默认的主题。
$config->default->module = 'index';                     // 默认的模块。当请求中没有指定模块时，加载该模块。
$config->default->method = 'index';                     // 默认的方法。当请求中没有指定方法或者指定的方法不存在时，调用该方法。

$config->db->errorMode  = PDO::ERRMODE_WARNING;         // PDO的错误模式: PDO::ERRMODE_SILENT|PDO::ERRMODE_WARNING|PDO::ERRMODE_EXCEPTION
$config->db->persistant = false;                        // 是否打开持久连接。
$config->db->driver     = 'mysql';                      // pdo的驱动类型，目前暂时只支持mysql。
$config->db->host       = 'localhost';                  // mysql主机。
$config->db->port       = '3306';                       // mysql主机端口号。
$config->db->name       = 'zentao';                     // 数据库名称。
$config->db->user       = 'root';                       // 数据库用户名。
$config->db->password   = '';                           // 密码。
$config->db->encoding   = 'UTF8';                       // 数据库的编码。
