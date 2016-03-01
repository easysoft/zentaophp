<?php
$basePath = dirname(dirname(dirname(__FILE__)));
$ranzhiPath = $basePath . '/ranzhi/';

$baseModel  = trim(file_get_contents($ranzhiPath . 'framework/model.class.php'));
$mergeModel = file_get_contents(dirname(__FILE__) . '/model.class.php');
$baseModel  = explode("\n", $baseModel);

$delete    = $mergeModel;
$startLine = 0;
$mark      = '';
$endTag    = -1;
foreach($baseModel as $i => $line)
{
    if($startLine and strpos($line, '{') !== false) $endTag ++;
    if($startLine and strpos($line, '}') !== false) $endTag --;
    if($endTag == 0 and $mark)
    {
        $baseModel[$startLine] = rtrim($$mark);
        unset($baseModel[$i]);
        $startLine = 0;
        $endTag    = -1;
        $mark      = '';
    }
    if(strpos($line, 'function delete(') !== false)
    {
        $startLine = $i;
        $mark = 'delete';
    }
    if($mark and $endTag == -1) $endTag = 0;
    if($startLine) unset($baseModel[$i]);
}
ksort($baseModel);
file_put_contents($ranzhiPath . 'framework/model.class.php', join("\n", $baseModel));

$baseHelper  = trim(file_get_contents($ranzhiPath . 'framework/helper.class.php'));
$mergeHelper = file_get_contents(dirname(__FILE__) . '/helper.class.php');
$baseHelper  = explode("\n", $baseHelper);

$getWebRoot = $mergeHelper;
$startLine  = 0;
$mark       = '';
$endTag     = -1;
foreach($baseHelper as $i => $line)
{
    if($startLine and strpos($line, '{') !== false) $endTag ++;
    if($startLine and strpos($line, '}') !== false) $endTag --;
    if($endTag == 0 and $mark)
    {
        $baseHelper[$startLine] = rtrim($$mark);
        unset($baseHelper[$i]);
        $startLine = 0;
        $endTag    = -1;
        $mark      = '';
    }
    if(strpos($line, 'function getWebRoot(') !== false)
    {
        $startLine = $i;
        $mark = 'getWebRoot';
    }
    if($mark and $endTag == -1) $endTag = 0;
    if($startLine) unset($baseHelper[$i]);
}
ksort($baseHelper);
file_put_contents($ranzhiPath . 'framework/helper.class.php', join("\n", $baseHelper));
