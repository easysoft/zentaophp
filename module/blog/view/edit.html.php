<?php
/**
 * The html template file of edit method of hello module of ZenTaoPHP.
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
  <form method='post'>
    <table class='table table-bordered' align='center' style='width:450px'> 
      <tr>
        <th><?php echo $lang->blog->title;?></th>  
        <td><?php echo html::input('title', $article->title);?></td>
      </tr>  
      <tr>
        <th><?php echo $lang->blog->content;?></th>  
        <td><?php echo html::textarea('content', $article->content, "rows='10' cols='100'");?></td>
      </tr>
      <tr><td colspan='2'><?php echo html::submitButton();?></td></tr>  
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
