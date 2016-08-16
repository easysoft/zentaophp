<?php
/**
 * The html template file of edit method of blog module of ZenTaoPHP.
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
  <div class='panel'>
    <div class='panel-heading'><strong><?php echo $lang->blog->edit;?></strong></div>
    <form method='post'>
      <table class='table table-borderless table-form'>
        <tr>
          <th style='width:80px'><?php echo $lang->blog->title;?></th>  
          <td><?php echo html::input('title', $article->title, "class='form-control'");?></td>
        </tr>  
        <tr>
          <th><?php echo $lang->blog->content;?></th>  
          <td><?php echo html::textarea('content', $article->content, "class='form-control' rows='10' cols='10'");?></td>
        </tr>
        <tr><th></th><td><?php echo html::submitButton();?></td></tr>  
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
