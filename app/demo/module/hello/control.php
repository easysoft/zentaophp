<?php
/**
 * The control file of hello module of ZenTaoPHP.
 *
 * ZenTaoPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

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
 * @link        http://www.zentao.cn
 */
class hello extends control
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $articles = $this->hello->getList();
        $header['title'] = $this->lang->page;
        $this->assign('header',   $header);
        $this->assign('articles', $articles);
        $this->display();
    }

    public function view($id)
    {
        $article = $this->hello->getInfo($id);
        $header['title'] = $this->lang->page;
        $this->assign('header',  $header);
        $this->assign('article', $article);
        $this->display();
    }

    public function del($id)
    {
        $this->hello->delArticle($id);
        header("location: " . $this->createLink($this->moduleName));
    }

    public function edit($id)
    {
        if(empty($_POST))
        {
            $header['title'] = $this->lang->page;
            $article = $this->hello->getInfo($id);
            $this->assign('header', $header);
            $this->assign('article', $article);
            $this->display();
        }
        else
        {
            $title   = filter_var($_POST['title'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_MAGIC_QUOTES);
            $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_MAGIC_QUOTES);
            $id      = (int)$id;
            $this->hello->save($id, $title, $content);
            header("location: " . $this->createLink($this->moduleName));
        }
    }

    public function add()
    {
        if(empty($_POST))
        {
            $header['title'] = $this->lang->page;
            $this->assign('header', $header);
            $this->display();
        }
        else
        {
            $title   = filter_var($_POST['title'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_MAGIC_QUOTES);
            $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_MAGIC_QUOTES);
            $this->hello->add($title, $content);
            header("location: " . $this->createLink($this->moduleName));
        }
    }
}
