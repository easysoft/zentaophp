<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dli'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
  <?php
  if(isset($header['title']))   echo "<title>$header[title]</title>\n";
  if(isset($header['keyword'])) echo "<meta name='keywords' content='$header[keyword]'>\n";
  if(isset($header['desc']))    echo "<meta name='description' content='$header[desc]'>\n";
  ?>
  <link rel='stylesheet' href='<?php echo $this->app->getClientTheme() . 'style.css';?>' type='text/css' media='screen' />
</head>
<body>
