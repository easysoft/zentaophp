<?php
$basePath = dirname(dirname(dirname(__FILE__)));
$chanzhiPath = $basePath . '/chanzhi/';

$baseControl  = trim(file_get_contents($chanzhiPath . 'framework/control.class.php'));
$mergeControl = file(dirname(__FILE__) . '/control.class.php');
$baseControl  = explode("\n", $baseControl);

$mark  = '';
foreach($mergeControl as $i => $line)
{
    $first = false;
    if(strpos($line, 'function setViewPrefix(') !== false)
    {
        $setViewPrefix = $line;
        $mark = 'setViewPrefix';
        $first = true;
    }
    if(strpos($line, 'function setViewFile(') !== false)
    {
        $setViewFile = $line;
        $mark = 'setViewFile';
        $first = true;
    }
    if(strpos($line, 'function getExtViewFile(') !== false)
    {
        $getExtViewFile = $line;
        $mark = 'getExtViewFile';
        $first = true;
    }
    if(strpos($line, 'function getCSS(') !== false)
    {
        $getCSS = $line;
        $mark = 'getCSS';
        $first = true;
    }
    if(strpos($line, 'function getJS(') !== false)
    {
        $getJS = $line;
        $mark = 'getJS';
        $first = true;
    }
    if(strpos($line, 'function parse(') !== false)
    {
        $parse = $line;
        $mark = 'parse';
        $first = true;
    }
    if(strpos($line, 'function parseJSON(') !== false)
    {
        $parseJSON = $line;
        $mark = 'parseJSON';
        $first = true;
    }
    if(strpos($line, 'function parseDefault(') !== false)
    {
        $parseDefault = $line;
        $mark = 'parseDefault';
        $first = true;
    }
    if(strpos($line, 'function display(') !== false)
    {
        $display = $line;
        $mark = 'display';
        $first = true;
    }
    if(strpos($line, 'function createLink(') !== false)
    {
        $createLink = $line;
        $mark = 'createLink';
        $first = true;
    }
    if(strpos($line, 'function inlink(') !== false)
    {
        $inlink = $line;
        $mark = 'inlink';
        $first = true;
    }
    if(strpos($line, '/**') !== false)
    {
        if(!isset($addLines)) $addLines = '';
        $addLines .= $line;
        $mark = 'addLines';
        $first = true;
    }
    if($mark and !$first) $$mark .= $line;
}

$startLine = 0;
$mark      = '';
$endTag    = -1;
foreach($baseControl as $i => $line)
{
    if($startLine and strpos($line, '{') !== false) $endTag ++;
    if($startLine and strpos($line, '}') !== false) $endTag --;
    if($endTag == 0 and $mark)
    {
        $baseControl[$startLine] = rtrim($$mark);
        unset($baseControl[$i]);
        $startLine = 0;
        $endTag    = -1;
        $mark      = '';
    }
    if(strpos($line, 'function setViewPrefix(') !== false)
    {
        $startLine = $i;
        $mark = 'setViewPrefix';
    }
    if(strpos($line, 'function setViewFile(') !== false)
    {
        $startLine = $i;
        $mark = 'setViewFile';
    }
    if(strpos($line, 'function getExtViewFile(') !== false)
    {
        $startLine = $i;
        $mark = 'getExtViewFile';
    }
    if(strpos($line, 'function getCSS(') !== false)
    {
        $startLine = $i;
        $mark = 'getCSS';
    }
    if(strpos($line, 'function getJS(') !== false)
    {
        $startLine = $i;
        $mark = 'getJS';
    }
    if(strpos($line, 'function parse(') !== false)
    {
        $startLine = $i;
        $mark = 'parse';
    }
    if(strpos($line, 'function parseJSON(') !== false)
    {
        $startLine = $i;
        $mark = 'parseJSON';
    }
    if(strpos($line, 'function parseDefault(') !== false)
    {
        $startLine = $i;
        $mark = 'parseDefault';
    }
    if(strpos($line, 'function display(') !== false)
    {
        $startLine = $i;
        $mark = 'display';
    }
    if(strpos($line, 'function createLink(') !== false)
    {
        $startLine = $i;
        $mark = 'createLink';
    }
    if(strpos($line, 'function inlink(') !== false)
    {
        $startLine = $i;
        $mark = 'inlink';
    }
    if($mark and $endTag == -1) $endTag = 0;
    if($startLine) unset($baseControl[$i]);
}
ksort($baseControl);
$baseControl[$i] = "\n" . $addLines . $baseControl[$i];
file_put_contents($chanzhiPath . 'framework/control.class.php', join("\n", $baseControl));
unset($addLines);

$baseModel  = trim(file_get_contents($chanzhiPath . 'framework/model.class.php'));
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
file_put_contents($chanzhiPath . 'framework/model.class.php', join("\n", $baseModel));

$baseHelper  = trim(file_get_contents($chanzhiPath . 'framework/helper.class.php'));
$mergeHelper = file(dirname(__FILE__) . '/helper.class.php');
$baseHelper  = explode("\n", $baseHelper);

$mark  = '';
foreach($mergeHelper as $i => $line)
{
    $first = false;
    if(strpos($line, 'function createLink(') !== false)
    {
        $createLink = $line;
        $mark   = 'createLink';
        $first  = true;
    }
    if(strpos($line, 'function inLink(') !== false)
    {
        $inLink = $line;
        $mark   = 'inLink';
        $first  = true;
    }
    if(strpos($line, 'function getWebRoot(') !== false)
    {
        $getWebRoot = $line;
        $mark   = 'getWebRoot';
        $first  = true;
    }
    if(strpos($line, '/**') !== false)
    {
        if(!isset($addLines)) $addLines = '';
        $addLines .= $line;
        $mark   = 'addLines';
        $first  = true;
    }
    if($mark and !$first) $$mark .= $line;
}

$startLine = 0;
$mark      = '';
$endTag    = -1;
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
    if(strpos($line, 'function createLink(') !== false)
    {
        $startLine = $i;
        $mark = 'createLink';
    }
    if(strpos($line, 'function inLink(') !== false)
    {
        $startLine = $i;
        $mark = 'inLink';
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
$baseHelper[$i] = $baseHelper[$i] . "\n" . $addLines;
file_put_contents($chanzhiPath . 'framework/helper.class.php', join("\n", $baseHelper));
