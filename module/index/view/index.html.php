<?php
/**
 * The html template file of index method of index module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php include '../../common/view/header.html.php';?>
<h1 class='a-center'><?php echo $lang->welcome;?></h1>
<h3 class='a-center'><?php echo html::a($this->createLink('blog'), $lang->index->blog);?></h3>
<?php include '../../common/view/footer.html.php';?>
