<?php
$basePath = dirname(dirname(dirname(__FILE__)));
$zentaoPath = $basePath . '/zentao/';

$baseFront  = trim(file_get_contents($zentaoPath . 'lib/front/front.class.php'));
$mergeFront = file_get_contents(dirname(__FILE__) . '/front.class.php');
$baseFront  = explode("\n", $baseFront);

$a         = $mergeFront;
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
    if($mark and $endTag == -1) $endTag = 0;
    if($startLine) unset($baseFront[$i]);
}
ksort($baseFront);
file_put_contents($zentaoPath . 'lib/front/front.class.php', join("\n", $baseFront));
