<?php
/**
 * The html template file of add method of hello module of ZenTaoPHP.
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
<form method='post' target='hiddenwin'>
  <table align='center'> 
    <tr>
      <th><?php echo $lang->blog->title;?></th>  
      <td><?php echo html::input('title');?></td>
    </tr>  
    <tr>
      <th><?php echo $lang->blog->content;?></th>  
      <td><?php echo html::textarea('content', '', "cols='50' rows='10'");?></td>
    </tr>
    <tr><td colspan='2'><?php echo html::submitButton();?></td></tr>  
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>
