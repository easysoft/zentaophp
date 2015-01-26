<?php
/**
 * ZenTaoPHP的helper类。
 * The helper class file of ZenTaoPHP framework.
 *
 * The author disclaims copyright to this source code. In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/**
 * 该类实现了一些常用的方法
 * The helper class, contains the tool functions.
 *
 * @package framework
 */
class helper
{
    /**
     * 设置一个对象的成员变量
     * Set the member's value of one object.
     * <code>
     * <?php
     * $lang->db->user = 'wwccss';
     * helper::setMember('lang', 'db.user', 'chunsheng.wang');
     * ?>
     * </code>
     * @param string    $objName    the var name of the object.
     * @param string    $key        the key of the member, can be parent.child.
     * @param mixed     $value      the value to be set.
     * @static
     * @access public
     * @return bool
     */
    static public function setMember($objName, $key, $value)
    {
        global $$objName;
        if(!is_object($$objName) or empty($key)) return false;
        $key   = str_replace('.', '->', $key);
        $value = serialize($value);
        $code  = ("\$${objName}->{$key}=unserialize(<<<EOT\n$value\nEOT\n);");
        eval($code);
        return true;
    }

    /**
     * 创建一个模块方法的链接
     * control类的createLink实际上调用的是这个方法
     *
     * Create a link to a module's method.
     * This method also mapped in control class to call conveniently.
     *
     * <code>
     * <?php
     * helper::createLink('hello', 'index', 'var1=value1&var2=value2');
     * helper::createLink('hello', 'index', array('var1' => 'value1', 'var2' => 'value2');
     * ?>
     * </code>
     * @param string       $moduleName     module name
     * @param string       $methodName     method name
     * @param string|array $vars           the params passed to the method, can be array('key' => 'value') or key1=value1&key2=value2) or key1=value1&key2=value2
     * @param string       $viewType       the view type
     * @static
     * @access public
     * @return string the link string.
     */
    static public function createLink($moduleName, $methodName = 'index', $vars = '', $viewType = '')
    {
        global $app, $config;
        $link = $config->requestType == 'PATH_INFO' ? $config->webRoot : $_SERVER['PHP_SELF'];

        /* 设置视图类型和变量。 */
        /* Set the view type and vars. */
        if(empty($viewType)) $viewType = $app->getViewType();
        if(!is_array($vars)) parse_str($vars, $vars);

        /* PATH_INFO方式。 */
        /* The PATH_INFO type. */
        if($config->requestType == 'PATH_INFO')
        {
            /* 如果方法名与默认方法相等，并且参数是空的，转换为友好的链接地址。 */
            /* If the method equal the default method defined in the config file and the vars is empty, convert the link. */
            if($methodName == $config->default->method and empty($vars))
            {
                /* 如果模块名与默认模块名相等，转换为index.html。*/
                /* If the module also equal the default module, change index-index to index.html. */
                if($moduleName == $config->default->module)
                {
                    $link .= 'index.' . $viewType;
                }
                else
                {
                    $link .= $moduleName . '/';
                }
            }
            else
            {
                $link .= "$moduleName{$config->requestFix}$methodName";
                if($config->pathType == 'full')
                {
                    foreach($vars as $key => $value) $link .= "{$config->requestFix}$key{$config->requestFix}$value";
                }
                else
                {
                    foreach($vars as $value) $link .= "{$config->requestFix}$value";
                }    
                $link .= '.' . $viewType;
            }
        }
        elseif($config->requestType == 'GET')
        {
            $link .= "?{$config->moduleVar}=$moduleName&{$config->methodVar}=$methodName";
            if($viewType != 'html') $link .= "&{$config->viewVar}=" . $viewType;
            foreach($vars as $key => $value) $link .= "&$key=$value";
        }
        return $link;
    }

    /**
     * 引用一个文件，替换内置的include及require方法
     * Import a file instend of include or require.
     *
     * @param string    $file   the file to be imported.
     * @static
     * @access public
     * @return bool
     */
    static public function import($file)
    {
        if(!is_file($file)) return false;
        static $includedFiles = array();
        if(!isset($includedFiles[$file]))
        {
            include $file;
            $includedFiles[$file] = true;
            return true;
        }
        return true;
    }

    /**
     * 设置一个模块的model文件，如果存在model扩展，一起合并
     * Set the model file of one module. If there's an extension file, merge it with the main model file.
     * 
     * @param   string $moduleName the module name
     * @static
     * @access  public
     * @return  string the model file
     */
    static public function setModelFile($moduleName)
    {
        global $app;

        /* 设置主model文件，扩展文件和路径。 */
        /* Set the main model file, extension path and files. */
        $mainModelFile = $app->getModulePath($moduleName) . 'model.php';
        $modelExtPath  = $app->getModuleExtPath($moduleName, 'model');
        $extFiles      = helper::ls($modelExtPath, '.php');

        /* 如果没有扩展文件，返回主文件目录。 */
        /* If no extension file, return the main file directly. */
        if(empty($extFiles)) return $mainModelFile;

        /* 通过对比合并后的缓存文件和扩展文件的修改时间，确定是否要重新生成缓存 */
        /* Else, judge whether needed update or not .*/
        $mergedModelFile = $app->getTmpRoot() . 'model' . $app->getPathFix() . $moduleName . '.php';
        $needUpdate      = false;
        $lastTime        = file_exists($mergedModelFile) ? filemtime($mergedModelFile) : 0;
        foreach($extFiles as $extFile)
        {
            if(filemtime($extFile) > $lastTime)
            {
                $needUpdate = true;
                break;
            }
        }
        if(filemtime($mainModelFile) > $lastTime) $needUpdate = true;

        /* 如果不需要更新，返回缓存文件。 */
        /* If need'nt update, return the cache file. */
        if(!$needUpdate) return $mergedModelFile;

        /* 更新缓存文件。 */
        /* Update the cache file. */
        if($needUpdate)
        {
            $modelClass    = $moduleName . 'Model';
            $extModelClass = 'ext' . $modelClass;
            $modelLines    = trim(file_get_contents($mainModelFile));
            $modelLines    = rtrim($modelLines, '?>');     // 确保php标签未闭合  To make sure the last end tag is removed.
            $modelLines   .= "class $extModelClass extends $modelClass {\n";

            /* Cycle all the extension files. */
            foreach($extFiles as $extFile)
            {
                $extLines = trim(file_get_contents($extFile));
                if(strpos($extLines, '<?php') !== false) $extLines = ltrim($extLines, '<?php');
                if(strpos($extLines, '?>')    !== false) $extLines = rtrim($extLines, '?>');
                $modelLines .= $extLines . "\n";
            }

            /* 创建合并的文件。 */
            /* Create the merged model file. */
            $modelLines .= "}";
            file_put_contents($mergedModelFile, $modelLines);

            return $mergedModelFile;
        }
    }

    /**
     * 将数组转化成 IN( 'a', 'b') 的形式，用于数据库字符串拼接
     * Create the in('a', 'b') string.
     * 
     * @param   string|array $ids   the id lists, can be a array or a string with ids joined with comma.
     * @static
     * @access  public
     * @return  string  the string like IN('a', 'b').
     */
    static public function dbIN($ids)
    {
        if(is_array($ids)) return "IN ('" . join("','", $ids) . "')";
        return "IN ('" . str_replace(',', "','", str_replace(' ', '',$ids)) . "')";
    }

    /**
     * base64编码，框架对'/'字符比较敏感，转换为'.'
     * Create safe base64 encoded string for the framework.
     * 
     * @param   string  $string   the string to encode.
     * @static
     * @access  public
     * @return  string  encoded string.
     */
    static public function safe64Encode($string)
    {
        return strtr(base64_encode($string), '/', '.');
    }

    /**
     * 解码base64，先将之前的'.' 转换回'/'
     * Decode the string encoded by safe64Encode.
     * 
     * @param   string  $string   the string to decode
     * @static
     * @access  public
     * @return  string  decoded string.
     */
    static public function safe64Decode($string)
    {
        return base64_decode(strtr($string, '.', '/'));
    }

    /**
     *  计算两个日期相差的天数，取整
     *  Compute the diff days of two date.
     * 
     * @param   string $date1   the first date.
     * @param   string $date2   the sencode date.
     * @access  public
     * @return  int  the diff of the two days.
     */
    static public function diffDate($date1, $date2)
    {
        return round((strtotime($date1) - strtotime($date2)) / 86400, 0);
    }

    /**
     *  获取当前时间，使用common语言文件定义的DT_DATETIME1常量
     *  Get now time use the DT_DATETIME1 constant defined in the lang file.
     * 
     * @access  public
     * @return  datetime  now
     */
    static public function now()
    {
        return date(DT_DATETIME1);
    }

    /**
     *  获取当前日期，使用common语言文件定义的DT_DATE1常量
     *  Get today according to the  DT_DATE1 constant defined in the lang file.
     *
     * @access  public
     * @return  date  today
     */
    static public function today()
    {
        return date(DT_DATE1);
    }

    /**
     *  判断日期是不是零
     *  Judge a date is zero or not.
     * 
     * @access  public
     * @return  bool
     */
    static public function isZeroDate($date)
    {
        return substr($date, 0, 4) == '0000';
    }

    /**
     *  列出目录中符合该正则表达式的文件
     *  Get files match the pattern under one directory.
     * 
     * @access  public
     * @return  array   the files match the pattern
     */
    static public function ls($dir, $pattern = '')
    {
        $files = array();
        $dir = realpath($dir);
        if(is_dir($dir)) $files = glob($dir . DIRECTORY_SEPARATOR . '*' . $pattern);
        return empty($files) ? array() : $files;
    }

    /**
     * 切换目录
     * Change directory.
     * 
     * @param  string $path 
     * @static
     * @access public
     * @return void
     */
    static function cd($path = '')
    {
        static $cwd = '';
        if($path)
        {
            $cwd = getcwd();
            chdir($path);
        }
        else
        {
            chdir($cwd);
        }
    }

    /**
     * 去掉UTF8 Bom头
     * Remove UTF8 Bom 
     * 
     * @param  string    $string
     * @access public
     * @return string
     */
    public static function removeUTF8Bom($string)
    {
        if(substr($string, 0, 3) == pack('CCC', 239, 187, 191)) return substr($string, 3);
        return $string;
    }
}

/**
 *  helper::createLink()的别名，方便创建本模块的链接
 *  The short alias of helper::createLink() method. 
 *
 * @param  string        $methodName  the method name
 * @param  string|array  $vars        the params passed to the method, can be array('key' => 'value') or key1=value1&key2=value2)
 * @param  string        $viewType    
 * @return string the link string.
 */
function inLink($methodName = 'index', $vars = '', $viewType = '')
{
    global $app;
    return helper::createLink($app->getModuleName(), $methodName, $vars, $viewType);
}

/**
 *  通过一个静态游标，可以遍历数组
 *  Static cycle a array
 *
 * @param array  $items     the array to be cycled.
 * @return mixed
 */
function cycle($items)
{
    static $i = 0;
    if(!is_array($items)) $items = explode(',', $items);
    if(!isset($items[$i])) $i = 0;
    return $items[$i++];
}

/**
 * 获取当前时间的Unix时间戳，精确到微妙
 * Get current microtime.
 * 
 * @access protected
 * @return float current time.
 */
function getTime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * 打印变量的信息
 * dump a var.
 * 
 * @param mixed $var 
 * @access public
 * @return void
 */
function a($var)
{
    echo "<xmp class='a-left'>";
    print_r($var);
    echo "</xmp>";
}
