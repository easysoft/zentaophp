#!/usr/bin/env php
<?php
/**
 * 压缩框架文件测试。
 *
 * @copyright   Copyright 2009-2010 青岛易软天创网络科技有限公司(www.cnezsoft.com)
 * @author      chunsheng.wang <chunsheng@cnezsoft.com>
 * @package     Testing
 * @version     $Id:
 * @link        http://www.zentaoms.com
 * @license     http://opensource.org/licenses/lgpl-3.0.html LGPL
 */
chdir('../');
$allFile = '../../framework/all.class.php';
unlink($allFile);

/* 压缩之后检查文件是否存在，并检查语法是否有错。*/
`./ztphp compress/compressFramework`;
var_dump(file_exists($allFile));
echo `php -l $allFile`;
?>
