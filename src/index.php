<?php

// @codeCoverageIgnoreStart
Bootstrap::run();
// @codeCoverageIgnoreEnd

/**
 * @codeCoverageIgnore
 */
class Bootstrap
{
    static public function run()
    {
        require_once dirname(__FILE__) . '/Sys/Autoload.php';
        \Sys\Autoload::register();

        $router = new \Sys\Router;
        $router->setRoutes(include dirname(__FILE__) . '/App/Config/Routes.php');
        $uri = $_SERVER['REQUEST_URI'];
        $controller = $router->getController($uri);
        if (!$controller || !class_exists($controller)) {
            header('404 Not Found');
            die('Not Found');
        } else {
            $ambiance = !getEnv('AMBIANCE') ? 'development' : getEnv('AMBIANCE');
            \Sys\Config::setFile(dirname(__FILE__).'/App/Config/Config.ini');
            \Sys\Config::setAmbiance($ambiance);

            if (!is_dir(sys_get_temp_dir() . '/forum')) {
                mkdir(sys_get_temp_dir() . '/forum');
            }
            $cache = new \Sys\Cache(
                new \Sys\Cache\Filesystem,
                sys_get_temp_dir() . '/forum'
            );

            $method = isset($_SERVER['REQUEST_METHOD'])
                    ? strtolower($_SERVER['REQUEST_METHOD'])
                    : 'get';
            $router->setController(new $controller);
            $router->run($method);
        }
    }
}
