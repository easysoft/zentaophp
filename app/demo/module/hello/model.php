<?php
/**
 * The model file of hello module of ZenTaoPHP.
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
 * @version     $Id$
 */
?>
<?php
class helloModel extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    function getList()
    {
        $sql      = "SELECT * FROM blog";
        $stmt     = $this->dbh->query($sql);
        $articles = $stmt->fetchAll();
        return $articles;
    }

    function getInfo($id)
    {
        $id      = (int)$id;
        $sql     = "SELECT * FROM blog WHERE id = '$id'";
        $stmt    = $this->dbh->query($sql);
        $article = $stmt->fetch();
        return $article;
    }

    function delArticle($id)
    {
        $id  = (int)$id;
        $sql = "DELETE FROM blog WHERE id = '$id' LIMIT 1";
        return $this->dbh->exec($sql);
    }
    
    function add($title, $content)
    {
        if(empty($title) or empty($content)) return false;
        $sql = "INSERT INTO blog (title, content, date) VALUES('$title', '$content', NOW())";
        return $this->dbh->exec($sql);
    }

    function save($id, $title, $content)
    {
        if(empty($id) or empty($title) or empty($content)) return false;
        $sql = "UPDATE blog SET title = '$title', content = '$content' WHERE id = '$id'";
        return $this->dbh->exec($sql);
    }
}
