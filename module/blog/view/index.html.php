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
  <div class='panel'>
  <div class='panel-heading'>
    <strong> <?php echo $lang->blog->index;?></strong>
    <div class='pull-right'> <?php echo html::a(inlink('create'), $lang->blog->add, "class='btn btn-primary btn-xs'");?></div>
  </div>
  <table class='table table-list table-hover'> 
    <thead>
      <tr>
        <td width='50'><?php echo $lang->blog->id;?></td>  
        <td><?php echo $lang->blog->title;?></td>  
        <td class='text-center' width='150'><?php echo $lang->blog->date;?></td>  
        <td class='text-center' width='120'><?php echo $lang->blog->action;?></td>  
      </tr>
    </thead>
    <tbody>
      <?php foreach($articles as $article):?>
      <tr>
        <td class='text-center'><?php echo $article->id;?></td>
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
          $pager->show('right', 'short');
          ?>
        </td>
      </tr>  
    </tfoot>
  </table>
</div>
</div>
<?php include '../../common/view/footer.html.php';?>
