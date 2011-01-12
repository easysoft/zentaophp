<?php
/**
 * The model file of blog module of ZenTaoPHP.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPHP
 * @version     $Id$
 */
?>
<?php
class blogModel extends model
{
    /**
     * Get article lists.
     * 
     * @access public
     * @return array
     */
    public function getList()
    {
        return $this->dao->select('*')->from('blog')->orderBy('id desc')->fetchAll();
    }

    /**
     * Get an article.
     * 
     * @param  int    $id 
     * @access public
     * @return object
     */
    public function getById($id)
    {
        return $this->dao->findById($id)->from('blog')->fetch();
    }

    /**
     * Create an article.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        $article = fixer::input('post')->specialchars('title, content')->add('date', date('Y-m-d H:i:s'))->get();
        $this->dao->insert('blog')->data($article)->exec();
        return $this->dao->lastInsertID();
    }

    /**
     * Update an article.
     * 
     * @param  int    $articleID 
     * @access public
     * @return void
     */
    public function update($articleID)
    {
        $article = fixer::input('post')->specialchars('title, content')->get();
        $this->dao->update('blog')->data($article)->where('id')->eq($articleID)->exec();
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
        $this->dao->delete()->from('blog')->where('id')->eq($id)->exec();
    }
}
