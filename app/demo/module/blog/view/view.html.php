<?php
/**
 * The html template file of view method of hello module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 */
?>
<?php include '../../common/view/header.html.php';?>
<h1><?php echo $article->title;?></h1>
<p><?php echo nl2br(strip_tags($article->content));?></p>
<h3><?php echo html::a(inlink('index'), $lang->blog->index);?></h3>
<?php include '../../common/view/footer.html.php';?>
