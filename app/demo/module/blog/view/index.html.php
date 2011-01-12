<?php
/**
 * The html template file of index method of blog module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 */
?>
<?php include '../../common/view/header.html.php';?>
<table align='center'> 
  <tr>
    <th><?php echo $lang->blog->id;?></th>  
    <th><?php echo $lang->blog->title;?></th>  
    <th><?php echo $lang->blog->date;?></th>  
    <th><?php echo $lang->blog->action;?></th>  
  </tr>
  <?php foreach($articles as $article):?>
  <tr>
    <td><?php echo $article->id;?></td>
    <td><?php echo $article->title;?></td>
    <td><?php echo $article->date;?></td>
    <td>
      <?php
      echo html::a($this->createLink('blog', 'view',   "id=$article->id"), $lang->blog->view);
      echo html::a($this->createLink('blog', 'edit',   "id=$article->id"), $lang->blog->edit);
      echo html::a($this->createLink('blog', 'delete', "id=$article->id"), $lang->blog->delete);
      ?>
    </td>
  </tr>  
  <?php endforeach;?>  
  <tr>
    <td colspan='4'>
      <?php 
      echo html::a(inlink('create'), $lang->blog->add);
      echo html::a($this->createLink('index', 'index'), $lang->index->index);
      ?>
    </td>
  </tr>  
</table>
<?php include '../../common/view/footer.html.php';?>
