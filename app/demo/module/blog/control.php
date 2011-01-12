<?php
/**
 * The control file of blog module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 * @link        http://www.zentaoms.com
 */
class blog extends control
{
    public function __construct()
    {
        parent::__construct();
        $this->app->loadLang('index');
    }

    /**
     * The index page of blog module.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->view->header->title = $this->lang->blog->index;
        $this->view->articles      = $this->blog->getList();
        $this->display();
    }

    /**
     * View a blog.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function view($id)
    {
        $this->view->header->title = $this->lang->blog->view;
        $this->view->article       = $this->blog->getInfo($id);
        $this->display();
    }

    public function del($id)
    {
        $this->blog->delArticle($id);
        header("location: " . $this->createLink($this->moduleName));
    }

    public function edit($id)
    {
        if(empty($_POST))
        {
            $header['title'] = $this->lang->page;
            $article = $this->blog->getInfo($id);
            $this->assign('header', $header);
            $this->assign('article', $article);
            $this->display();
        }
        else
        {
            $title   = filter_var($_POST['title'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_MAGIC_QUOTES);
            $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_MAGIC_QUOTES);
            $id      = (int)$id;
            $this->blog->save($id, $title, $content);
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
            $this->blog->add($title, $content);
            header("location: " . $this->createLink($this->moduleName));
        }
    }
}
