<?php
/**
 * model类从baseModel类继承而来，每个模块的model对象从model类集成。
 * 您可以对baseModel类进行扩展，扩展的逻辑可以定义在这个文件中。
 *
 * This model class extends from the baseModel class and extened by every module's model. 
 * You can extend the baseModel class by change this file.
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
include dirname(__FILE__) . '/base/model.class.php';
class model extends baseModel
{
}
