<?php
/**
 * The html template file of view method of blog module of ZenTaoPHP.
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
<div class='container'>
  <div class='article'>
    <h1><?php echo $article->title;?></h1>
    <p><?php echo nl2br(strip_tags($article->content));?></p>
  </div>
  <?php echo html::backButton();?>
  <?php echo html::a(inlink('index'), $lang->blog->index, "class='btn'");?>
</div>
<?php include '../../common/view/footer.html.php';?>
