<?php
/**
 * The html template file of edit method of hello module of ZenTaoPHP.
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
 * @version     $Id: edit.html.php 1160 2009-04-26 13:56:41Z wwccss $
 */
?>
<?php include '../../common/header.html.php';?>
<div id='doc3'>
  <form method='post'>
    <table align='center'> 
      <tr>
        <th><?php echo $lang->title;?></th>  
        <td><input type='text' name='title' value='<?php echo $article->title;?>' /></td>
      </tr>  
      <tr>
        <th><?php echo $lang->content;?></th>  
        <td><textarea name='content' rows='7' cols='50'><?php echo $article->content;?></textarea></td>  
      </tr>
      <tr>
        <td colspan='2' class='textcenter'><input type='submit' /></td>
        <input type='hidden' value='<?php echo $article->id;?>' name='id' />
      </tr>  
    </table>
  </form>
</div>  
<?php include '../../common/footer.html.php';?>
