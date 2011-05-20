<?php
$webRoot = $this->app->getWebRoot();
$jsRoot  = $webRoot . "js/";
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dli'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
  <?php
  if(isset($header->title)) echo html::title($header->title);
  css::import($this->app->getClientTheme() . 'style.css');

  js::exportConfigVars();
  js::import($jsRoot . 'my.js', $config->version);

  if(isset($pageCss)) css::internal($pageCss);
  echo html::icon($webRoot . 'favicon.ico');
  ?>
</head>
<body>
