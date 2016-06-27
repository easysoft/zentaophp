<?php
/**
 * router类从baseRouter类集成而来，您可以通过修改这个文件实现对baseRouter类的扩展。
 * The router class extend from baseRouter class, you can extend baseRouter class by change this file.
 *
 * @package framework
 *
 * The author disclaims copyright to this source code. In place of 
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
define('FRAME_ROOT', dirname(__FILE__));
include FRAME_ROOT . '/base/router.class.php';
class router extends baseRouter
{
}
