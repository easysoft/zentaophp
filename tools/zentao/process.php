<?php
$basePath = dirname(dirname(dirname(__FILE__)));
$zentaoPath = $basePath . '/zentao/';

$baseModel  = trim(file_get_contents($zentaoPath . 'framework/model.class.php'));
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
file_put_contents($zentaoPath . 'framework/model.class.php', join("\n", $baseModel));

$baseFront  = trim(file_get_contents($zentaoPath . 'lib/front/front.class.php'));
$mergeFront = file(dirname(__FILE__) . '/front.class.php');
$baseFront  = explode("\n", $baseFront);

$mark  = '';
foreach($mergeFront as $i => $line)
{
    $first = false;
    if(strpos($line, 'function a(') !== false)
    {
        $a = $line;
        $mark = 'a';
        $first = true;
    }
    if(strpos($line, 'function selectButton(') !== false)
    {
        $selectButton = $line;
        $mark = 'selectButton';
        $first = true;
    }
    if($mark and !$first) $$mark .= $line;
}

$startLine = 0;
$mark      = '';
$endTag    = -1;
foreach($baseFront as $i => $line)
{
    if($startLine and strpos($line, '{') !== false) $endTag ++;
    if($startLine and strpos($line, '}') !== false) $endTag --;
    if($endTag == 0 and $mark)
    {
        $baseFront[$startLine] = rtrim($$mark);
        unset($baseFront[$i]);
        $startLine = 0;
        $endTag    = -1;
        $mark      = '';
    }
    if(strpos($line, 'function a(') !== false)
    {
        $startLine = $i;
        $mark = 'a';
    }
    if(strpos($line, 'function selectButton(') !== false)
    {
        $startLine = $i;
        $mark = 'selectButton';
    }
    if($mark and $endTag == -1) $endTag = 0;
    if($startLine) unset($baseFront[$i]);
}
ksort($baseFront);
file_put_contents($zentaoPath . 'lib/front/front.class.php', join("\n", $baseFront));
