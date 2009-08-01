<?php
echo <<<EOT
<?php
/**
 * The model file of {$module2C} module of {$claim[appName]}.
 *
{$claim[license]}  
 *
 * @copyright   {$claim[copyright]}
 * @author      {$claim[author]}
 * @package     {$module2C}
 * @version     \$Id\$
 * @link        {$claim[website]}
 */
?>
<?php
class {$module2C}Model extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    function create()
    {
    }

    function read(\$id)
    {
    }

    function update(\$id)
    {
    }
    
    function delete(\$id)
    {
    }
}
EOT;
