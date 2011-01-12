<?php
/**
 * The html template file of index method of index module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 */
?>
<?php include '../../common/view/header.html.php';?>
<h1><?php echo $lang->welcome;?></h1>
<h3><?php echo html::a($this->createLink('blog'), $lang->index->blog);?></h3>
<?php include '../../common/view/footer.html.php';?>
