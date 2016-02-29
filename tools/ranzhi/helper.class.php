function getWebRoot()
{
    $path = $_SERVER['SCRIPT_NAME'];
    if(defined('IN_SHELL'))
    {
        $url  = parse_url($_SERVER['argv'][1]);
        $path = empty($url['path']) ? '/' : rtrim($url['path'], '/');
        $path = empty($path) ? '/' : preg_replace('/\/www$/', '/www/', $path);
    }

    $path = dirname(dirname($path));
    $path = str_replace('\\', '/', $path);
    if($path == '/') return '/';
    return $path . '/';
}
