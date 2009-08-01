<?php
/**
 * The html template file of view method of hello module of ZenTaoPHP.
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
 * @version     $Id: view.html.php 1160 2009-04-26 13:56:41Z wwccss $
 */
?>
<?php include '../../common/header.html.php';?>
<div id='doc3'>
  <h1><?php echo $article->title;?></h1>
  <p><?php echo nl2br(strip_tags($article->content));?></p>
  <a href='<?php echo $this->createLink($this->moduleName);?>'>Back</a>
</div>  
<?php include '../../common/footer.html.php';?>
