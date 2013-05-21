<?php
/**
 * The html template file of index method of blog module of ZenTaoPHP.
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
  <table class='table table-bordered table-hover'> 
    <thead>
      <tr>
        <th width='50'><?php echo $lang->blog->id;?></th>  
        <th><?php echo $lang->blog->title;?></th>  
        <th width='150'><?php echo $lang->blog->date;?></th>  
        <th width='100'><?php echo $lang->blog->action;?></th>  
      </tr>
    </thead>
    <tbody>
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
      </tbody>
    <tfoot>
      <tr>
        <td colspan='4'>
          <?php 
          echo html::a(inlink('create'), $lang->blog->add, '', "class='btn btn-primary'");
          $pager->show('right', 'short');
          ?>
        </td>
      </tr>  
    </tfoot>
  </table>
</div>
<?php include '../../common/view/footer.html.php';?>
