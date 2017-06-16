<?php
$filter = new stdclass();
$filter->rules = new stdclass();
$filter->rules->md5        = '/^[a-z0-9]{32}$/';
$filter->rules->base64     = '/^[a-zA-Z0-9\+\/\=]+$/';
$filter->rules->checked    = '/^[0-9,]+$/';
$filter->rules->idList     = '/^[0-9\|]+$/';
$filter->rules->lang       = '/^[a-zA-Z_\-]+$/';
$filter->rules->any        = '/./';
$filter->rules->number     = '/^[0-9]+$/';
$filter->rules->orderBy    = '/^\w+_(desc|asc)$/i';
$filter->rules->word       = '/^\w+$/';
$filter->rules->paramName  = '/^[a-zA-Z0-9_\.]+$/';
$filter->rules->paramValue = '/^[a-zA-Z0-9=_\-]+$/';

$filter->default = new stdclass(); 
$filter->default->moduleName = 'code';
$filter->default->methodName = 'code';
$filter->default->paramName  = 'reg::paramName';
$filter->default->paramValue = 'reg::paramValue';

$filter->default->get['onlybody'] = 'equal::yes';
$filter->default->get['HTTP_X_REQUESTED_WITH'] = 'equal::XMLHttpRequest';
