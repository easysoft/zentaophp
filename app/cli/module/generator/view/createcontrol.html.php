<?php 
echo <<<EOT
<?php
/**
 * The control file of {$module2C} module of {$claim[appName]}.
 *
{$claim[license]}  
 *
 * @copyright   {$claim[copyright]}
 * @author      {$claim[author]}
 * @package     {$module2C}
 * @version     \$Id\$
 * @link        {$claim[website]}
 */
class {$module2C} extends control
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        \$header['title'] = \$this->lang->page->index;
        \$this->assign('header', \$header);
        \$this->display();
    }

    public function create()
    {
        \$header['title'] = \$this->lang->page->create;
        \$this->assign('header', \$header);
        \$this->display();
    }

    public function read(\$id)
    {
        \$header['title'] = \$this->lang->page->read;
        \$this->assign('header', \$header);
        \$this->display();
    }

    public function update(\$id)
    {
        \$header['title'] = \$this->lang->page->update;
        \$this->assign('header', \$header);
        \$this->display();
    }

    public function delete(\$id)
    {
        \$header['title'] = \$this->lang->page->delete;
        \$this->assign('header', \$header);
        \$this->display();
    }
}
EOT;
