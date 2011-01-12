<?php
/**
 * The router file of ZenTaoPHP.
 *
 * All request should be routed by this router.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 * @link        http://www.zentao.net
 */
error_reporting(0);

/* Load the framework. */
include '../../../framework/router.class.php';
include '../../../framework/control.class.php';
include '../../../framework/model.class.php';
include '../../../framework/helper.class.php';

/* Instance the app. */
$startTime = getTime();
$app = router::createApp('demo');

/* Run the app. */
$common = $app->loadCommon();
$app->parseRequest();
$app->loadModule();
$common->printRunInfo($startTime);
