<?php
$config->installed    = true;  
$config->debug        = true;  
$config->requestType  = 'GET';
$config->requestFix   = '-';
$config->webRoot      = '/'; 

$config->db->host     = 'localhost';
$config->db->port     = '3306';
$config->db->name     = 'demo'; 
$config->db->user     = 'root'; 
$config->db->password = '';

/* 如果需要配置主从数据库，取消注释即可。To use master and slave database feature, uncomment this. */

//$config->slaveDB->host     = 'localhost';
//$config->slaveDB->port     = '3306';
//$config->slaveDB->name     = 'demo';
//$config->slaveDB->user     = 'root';
//$config->slaveDB->password = '';

/* 框架功能开关参数。Use these params to enable or disable some features of framework. */
$config->framework->autoConnectDB = true;  // 是否自动连接数据库。              Whether auto connect database or not.
$config->framework->multiLanguage = false; // 是否启用多语言功能。              Whether enable multi lanuage or not.
$config->framework->multiTheme    = false; // 是否启用多风格功能。              Whether enable multi theme or not.
$config->framework->detectDevice  = false; // 是否启用设备检测功能。            Whether enable device detect or not.
$config->framework->multiSite     = false; // 是否启用多站点模式。              Whether enable multi site mode or not.
$config->framework->extensionLevel= 0;     // 0=>无扩展,1=>公共扩展,2=>站点扩展 0=>no extension, 1=> common extension, 2=> every site has it's extension.
$config->framework->jsWithPrefix  = true;  // js::set()输出的时候是否增加前缀。 When us js::set(), add prefix or not.
$config->framework->filterBadKeys = true;  // 是否过滤不合要求的键值。          Whether filter bad keys or not.
$config->framework->filterTrojan  = true;  // 是否过滤木马攻击代码。            Whether strip trojan code or not.
$config->framework->filterXSS     = true;  // 是否过滤XSS攻击代码。             Whether strip xss code or not.
$config->framework->purifier      = true;  // 是否对数据做purifier处理。        Whether purifier data or not.
$config->framework->logDays       = 14;    // 日志文件保存的天数。              The days to save log files.
