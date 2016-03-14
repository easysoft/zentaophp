<?php
/**
 * Control类从baseControl类继承而来，每个模块的control对象从control类集成。
 * 您可以对baseControl类进行扩展，扩展的逻辑可以定义在这个文件中。
 *
 * This control class extends from the baseControl class and extened by every module's control. 
 * You can extend the baseControl class by change this file.
 *
 * @package framework
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
include dirname(__FILE__) . '/base/control.class.php';
class control extends baseControl
{
}
