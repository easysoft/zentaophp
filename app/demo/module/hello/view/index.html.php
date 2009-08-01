<?php
/**
 * The html template file of index method of hello module of ZenTaoPHP.
 *
 * ZenTaoPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZenTaoPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with ZenTaoPHP.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright: 2009 Chunsheng Wang
 * @author      Chunsheng Wang <wwccss@263.net>
 * @package     ZenTaoPHP
 * @version     $Id: index.html.php 1160 2009-04-26 13:56:41Z wwccss $
 */
?>
<?php include '../../common/header.html.php';?>
<div id='doc3'>
  <table align='center'> 
    <tr>
      <th><?php echo $lang->id;?></th>  
      <th><?php echo $lang->title;?></th>  
      <th><?php echo $lang->date;?></th>  
      <th><?php echo $lang->action;?></th>  
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
        echo "<a href='$viewLink'>{$lang->view}</a> ";
        echo "<a href='$delLink'>{$lang->del}</a> ";
        echo "<a href='$editLink'>{$lang->edit}</a> ";
        ?>
      </td>
    </tr>  
    <?php endforeach;?>  
    <tr>
      <td colspan='4' class='textright'>
        <a href='<?php echo $this->createLink($this->moduleName, 'add');?>'><?php echo $lang->add;?></a>
        <a href='<?php echo $this->createLink('index');?>'>Home</a>
      </td>
    </tr>  
  </table>
</div>  
<?php include '../../common/footer.html.php';?>
