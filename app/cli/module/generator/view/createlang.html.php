<?php echo <<<EOT
<?php
/**
 * The $module2C module $langName file of {$claim[appName]}.
 *
{$claim[license]}  
 *
 * @copyright   {$claim[copyright]}
 * @author      {$claim[author]}
 * @package     {$module2C}
 * @version     \$Id\$
 * @link        {$claim[website]}
 */

EOT;
foreach(array('common', 'index', 'create', 'read', 'update', 'delete') as $page)
{
    if($page == 'common')
    {
        $langValue = "'" . $module2C . "'";
    }
    else
    {
        $langValue = '"' . "{\$lang['page']['common']}" . '/' . $page . '"';
    }

    $pageName = str_pad("['{$page}']", $fieldsMaxLength + 4 + strlen($tableName) - strlen('page'), ' ', STR_PAD_RIGHT);
    echo "\$lang['page']$pageName = $langValue;\n";
}
foreach($fields as $field)
{
    $fieldName = str_pad("['{$field->field}']", $fieldsMaxLength + 4, ' ', STR_PAD_RIGHT);
    echo "\$lang['$tableName']$fieldName = '$field->field';\n";
}
