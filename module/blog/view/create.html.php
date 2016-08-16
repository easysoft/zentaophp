<?php
/**
 * The html template file of add method of blog module of ZenTaoPHP.
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
    <div class='panel-heading'><strong><?php echo $lang->blog->add;?></strong></div>
    <form method='post'>
      <table class='table table-borderless table-form' align='center'>
        <tr>
          <th style='width:80px'><?php echo $lang->blog->title;?></th>  
          <td><?php echo html::input('title', '', "class=form-control");?></td>
        </tr>  
        <tr>
          <th><?php echo $lang->blog->content;?></th>  
          <td><?php echo html::textarea('content', '', "class='form-control' cols='50' rows='10'");?></td>
        </tr>
        <tr><th></th><td><?php echo html::submitButton();?></td></tr>  
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
