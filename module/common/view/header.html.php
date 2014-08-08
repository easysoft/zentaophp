<?php
$webRoot = $this->app->getWebRoot();
$jsRoot  = $webRoot . 'js/';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
 <?php
 if(isset($title)) echo html::title($title);

 echo html::meta('charset', 'utf-8');
 echo html::meta('viewport', 'width=device-width, initial-scale=1.0');

 css::import($webRoot . 'theme/bootstrap/css/bootstrap.min.css');
 css::import($webRoot . 'theme/my.css');
 css::import($webRoot . 'theme/bootstrap/css/bootstrap-responsive.min.css');
 if(isset($pageCss)) css::internal($pageCss);

 js::import($jsRoot . 'jquery.min.js',    $config->version);
 js::import($jsRoot . 'bootstrap.min.js', $config->version);
 js::import($jsRoot . 'html5shiv.min.js', $config->version, 'lt IE 9');
 js::import($jsRoot . 'my.js', $config->version);
 js::exportConfigVars();

 echo html::icon($webRoot . 'favicon.ico');
 ?>
</head>
<body><div id='main'>
<?php include dirname(__FILE__) . '/nav.html.php';?>
