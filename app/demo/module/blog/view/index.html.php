<?php
/**
 * The html template file of index method of hello module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 青岛易软天创网络科技有限公司(www.cnezsoft.com)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 */
?>
<?php include '../../common/header.html.php';?>
<table align='center'> 
  <tr>
    <th><?php echo $lang->hello->id;?></th>  
    <th><?php echo $lang->hello->title;?></th>  
    <th><?php echo $lang->hello->date;?></th>  
    <th><?php echo $lang->hello->action;?></th>  
  </tr>
  <?php foreach($articles as $article):?>
  <tr>
    <td><?php echo $article->id;?></td>
    <td><?php echo $article->title;?></td>
    <td><?php echo $article->date;?></td>
    <td>
      <?php
      $vars     = array('id' => $article->id);
      $viewLink = $this->createLink($this->moduleName, 'view', $vars);
      $delLink  = $this->createLink($this->moduleName, 'del',  $vars);
      $editLink = $this->createLink($this->moduleName, 'edit', $vars);
      echo html::a($viewLink, $lang->hello->view);
      echo html::a($editLink, $lang->hello->edit);
      echo html::a($delLink, $lang->hello->del);
      ?>
    </td>
  </tr>  
  <?php endforeach;?>  
  <tr>
    <td colspan='4'>
      <?php 
      echo html::a(inlink('add'), $lang->hello->add);
      echo html::a($this->createLink('index', 'index'), $lang->index->index);
      ?>
    </td>
  </tr>  
</table>
<?php include '../../common/footer.html.php';?>
