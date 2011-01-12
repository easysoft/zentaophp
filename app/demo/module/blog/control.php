<?php
/**
 * The control file of blog module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class blog extends control
{
    /**
     * The construct function.
     * 
     * @access public
     * @return void
     */
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
     * Create an article.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        if(!empty($_POST))
        {
            $blogID = $this->blog->create();
            $this->locate(inlink('index'));
        }

        $this->view->header->title = $this->lang->blog->add;
        $this->display();
    }

   /**
     * Update an article.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function edit($id)
    {
        if(!empty($_POST))
        {
            $this->blog->update($id);
            $this->locate(inlink('view', "id=$id"));
        }
        else
        {
            $this->view->header->title = $this->lang->blog->edit;
            $this->view->article       = $this->blog->getByID($id);
            $this->display();
        }
    }

    /**
     * View an article.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function view($id)
    {
        $this->view->header->title = $this->lang->blog->view;
        $this->view->article       = $this->blog->getByID($id);
        $this->display();
    }

    /**
     * Delete an article.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $this->blog->delete($id);
        $this->locate(inlink('index'));
    }

 
}
