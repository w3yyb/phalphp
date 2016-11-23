<?php

/**
 * Main Application for Web Application
 *
 * @author Itv
 * @version 1.0
 */
namespace Application;

use \Interfaces\IRun as IRun,
    \Cli\Command as Command;

class Web extends \Phalcon\Mvc\Application implements IRun {

	/**
	 * @var turn on or off debug mode
	 */
	protected $_debug;

	/**
	 * @var	array of view paths used by script (for debugging)
	 */
	protected $_views;

	/**
	 * simple constructor
	 * 
	 * @param directory of the project
	 */
	public function __construct() {
		$this->_views = array();
		$this->_debug = FALSE;
	}

	/**
	 * @description turn on or off debug mode
	 */
	public function setDebugMode($debug) {
		$this->_debug = $debug === TRUE ?: FALSE;
	}


    public function setConfig($file) {
        if (!file_exists($file)) {
            throw new \Exception('Unable to load phalcon config file');
        }

        $di = new \Phalcon\Di\FactoryDefault();
        $config = new \Phalcon\Config(require $file);
        $di->set('config', $config);


        $di->set('db', function() use ($config) {
            return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                'adapter' => $config['database']['adapter'],
                'host' => $config['database']['host'],
                'username' => $config['database']['username'],
                'password' => $config['database']['password'],
                'dbname' => $config['database']['name'],
                'port' => $config['database']['port']
            ));
        });

		$di->set('db2', function() use ($config) {
			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
					'adapter' => $config['db2']['adapter'],
					'host' => $config['db2']['host'],
					'username' => $config['db2']['username'],
					'password' => $config['db2']['password'],
					'dbname' => $config['db2']['name'],
					'port' => $config['db2']['port']
			));
		});
		$di->set('logdb', function() use ($config) {
			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
				'adapter' => $config['logdb']['adapter'],
				'host' => $config['logdb']['host'],
				'username' => $config['logdb']['username'],
				'password' => $config['logdb']['password'],
				'dbname' => $config['logdb']['name'],
				'port' => $config['logdb']['port']
			));
		});
		$di->set('elements', function() {
			return new \Elements\Elements();
		});

        $this->setDI($di);
    }


    public function setSessions() {
        $di = $this->getDI();
        $di->set('session', function () {
            $session = new \Phalcon\Session\Adapter\Files();
            $session->start();

            return $session;
        });
        $this->setDI($di);
    }

    public function setConfig2($file) {
        if (!file_exists($file)) {
            throw new \Exception('Unable to load configuration file');
        }

        $di = new \Phalcon\DI\FactoryDefault();
        $di->set('config', new \Phalcon\Config(require $file));

        $di->set('db', function() use ($di) {
            $type = strtolower($di->get('config')->database->adapter);
            $creds = array(
                'host' => $di->get('config')->database->host,
                'username' => $di->get('config')->database->username,
                'password' => $di->get('config')->database->password,
                'dbname' => $di->get('config')->database->name
            );

            if ($type == 'mysql') {
                $connection =  new \Phalcon\Db\Adapter\Pdo\Mysql($creds);
            } else if ($type == 'postgres') {
                $connection =  new \Phalcon\Db\Adapter\Pdo\Postgresql($creds);
            } else if ($type == 'sqlite') {
                $connection =  new \Phalcon\Db\Adapter\Pdo\Sqlite($creds);
            } else {
                throw new Exception('Bad Database Adapter');
            }

            return $connection;
        });

        $this->setDI($di);
    }


    public function setAutoload($file, $dir) {
        if (!file_exists($file)) {
            throw new \Exception('Unable to find autoloader file');
        }

        $namespaces = include $file;

            // Tell Phalcon where to find php files
        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces($namespaces)->register();
    }


    public function setRoutes($file) {
            //	Load Routes
        $routes = include($file);

			//	Setup Routes
        $di = $this->getDI();
		$di->set('router', function() use ($routes) {

			$router = new \Phalcon\Mvc\Router();
            $router->setDefaultNamespace('Controllers');

            //	Setup Routes
            foreach($routes as $uri => $route) {
                $router->add($uri,
                    $route
                );
            }

            //$router->notFound(array('controller' => 'notfound', 'action' => 'index'));
			$router->handle();
			return $router;
		});
        $this->setDI($di);
    }


    public function setBaseUrl($base) {
        $di = $this->getDI();
		$di->set('url', function() use($base) {
			$url = new \Phalcon\Mvc\Url();
			$url->setBaseUri($base);
			return $url;
		});
        $this->setDI($di);
    }


    public function setView($viewPath = '../app/views/',$volt) {
        $isdebug=$this->_debug;
        $di = $this->getDI();
		$di->set('view', function() use ($viewPath,$volt,$isdebug) {

			$view = new \Phalcon\Mvc\View();
            $view->setViewsDir($viewPath);

            if ($volt) {
                /*
                $view->registerEngines(
                    array(
                        ".volt" => 'Phalcon\Mvc\View\Engine\Volt'
                    )
                );*/

                $view->registerEngines(array(
                    '.volt' => function ($view, $di) {

                        $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

                        $volt->setOptions(array(
                            'compiledPath' => "../app/cache/",
                            'compiledSeparator' => '_'
                        ));

                        return $volt;
                    },
                        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
                    ));
            }

			if ($isdebug) {
					//	Track Views
				$eventsManager = new \Phalcon\Events\Manager();

				$eventsManager->attach("view", function($event, $view) {
					if ($event->getType() == 'beforeRenderView') {
						$this->_views[] = $view->getActiveRenderPath();
					}
				});
				$view->setEventsManager($eventsManager);
			}

			return $view;
		});
		$this->setDI($di);
    }


	/**
	 * launch/run application
	 */
	public function run() {
		try {
			$isCaptureOn = FALSE;

				//	Execute MVC and get display
			echo $this->handle()->getContent();
			flush();

				//	Display debug info for development site only
			if ($this->_debug) {
				$this->printDebug();
			}
        //} catch(\Phalcon\Mvc\Dispatcher\Exception $e) {

		} catch(\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            //die($e);
			//header("HTTP/1.0 404 Not Found");
		}
	}


	/**
	 * display debug information
	 */
	public function printDebug() {

		$dispatcher = $this->getDI()->get('dispatcher');

		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();

		//$main = $this->getDI()->get('view')->getMainView();
		//$ = $view->getLayout(); $view->getMainView();
		$now = microtime(TRUE);
		
		$time = $now - $_SERVER['REQUEST_TIME'];

		echo "<style>
		.debug-table td, .debug-table th {
			font-size: 10px;
			margin: 0;
			padding: 0;
		}
		</style>
<h7>-----------------------------<br></h7>
		<h7>Phalcon debug info</h7>
		<table class='debug-table table table-striped table-condensed'>
			<tr>
				<td>Time</td>
				<td>$time</td>
			</tr>
			<tr>
				<td>Controller:</td>
				<td>{$controller}</td>
			</tr>
			<tr>
				<td>Action:</td>
				<td>{$action}</td>
			</tr>";

			foreach($this->_views as $view) {
				echo "<tr><td>View</td><td>{$view}</td></tr>";
			}

		echo "</table>";


			//	Print out Session Data
		if (!empty($_SESSION)) {
			echo "<h7>Session</h7>
			<table class='debug-table table table-striped table-condensed'><tr><th>Session Name</th><th>Session Value</th></tr>";
			echo "<tr><td>" . session_name() . "</td><td>" . session_id() . "</td></tr>";
			foreach($_SESSION as $index => $value) {
			//	echo "<tr><td>$index</td><td>" . $value . "</td></tr>";
			}
			echo "</table>";
		}

		//printSuperGlobal($_SESSION, "Session");
		//printSuperGlobal($_POST, "Post");
		//printSuperGlobal($_COOKIE, "Cookie");
        echo '-------------------------';


		/*$queries = DatabaseFactory::getQueries();
		if (!empty($queries)) {
			echo "<h7>Database</h7>
			<table class='table debug-table table-striped table-condensed'><tr><th>Query</th><th>File</th><th>Line</th><th>Success</th></tr>";
			foreach($queries as $query) {
				echo "<tr>
					<td>{$query->query}</td>
					<td>{$query->file}</td>
					<td>{$query->line}</td>
					<td>{$query->success}</td>
				</tr>";	
			}
			echo "</table>";
		}*/

		if (class_exists('MemcachedCache', FALSE)) {
			echo "<h7>Memcached</h7>";
			$cache = MemcachedCache::singleton();
			echo "<table class='table debug-table table-striped table-condensed'>";
			foreach($cache->getServerList() as $server) {
				echo "<tr><td>{$server['host']}</td><td>{$server['port']}</td><td></td></tr>";
			}
			echo "</table>";
		}
/*
			//	Get All CLI Commands
		$commands = Command::singleton()->getCommands();
		if (!empty($commands)) {
			echo "<h7>Shell Comamnds</h7>
			<table class='table debug-table table-striped table-condensed'><tr><th>Command</th><th>File</th><th>Line</th><th>Success</th></tr>";

			foreach($commands as $command) {
				$command->toRow();
			}
			echo "</table>";
		}*/
	}
}
