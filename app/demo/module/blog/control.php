<?php
/**
 * The control file of blog module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
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
    public function index($recTotal = 0, $recPerPage = 20, $pageID = 0)
    {
        $this->app->loadClass('pager');
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->header->title = $this->lang->blog->index;
        $this->view->articles      = $this->blog->getList($pager);
        $this->view->pager         = $pager;
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
