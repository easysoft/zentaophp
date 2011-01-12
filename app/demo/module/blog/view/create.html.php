<?php
/**
 * The html template file of add method of hello module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 */
?>
<?php include '../../common/view/header.html.php';?>
<form method='post'>
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
