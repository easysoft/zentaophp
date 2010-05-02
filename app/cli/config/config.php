<?php
/**
 * The cli config file of ZenTaoPHP.
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
$config->requestType = 'PATH_INFO';                           // 请求方式。
$config->requestFix  = '/';
$config->cookiePath  = '/';                                   // cookie的有效路径。
$config->cookieLife  = time() + 2592000;                      // cookie的生命周期。
$config->langs       = 'zh-cn,zh-tw,zh-hk,en';                // 支持的语言列表。
$config->views       = ',html,xml,json,txt,csv,doc,pdf,';     // 支持的视图列表。
$config->themes      = 'default';                             // 支持的主题列表。

$config->default->view   = 'html';                          // 默认的视图格式。
$config->default->lang   = 'zh-cn';                         // 默认的语言。
$config->default->theme  = 'default';                       // 默认的主题。
