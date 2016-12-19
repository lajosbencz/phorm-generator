<?php

namespace PhormGenerator;


use Phalcon\Cli\Dispatcher;
use Phalcon\Cli\Router;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Logger;
use Phalcon\Logger\Multiple as LoggerMultiple;
use Phalcon\Logger\Adapter\Stream as LoggerStream;
use Phalcon\Text;

class Console extends \Phalcon\Cli\Console
{
    public function __construct($di=null)
    {
        global $config;
        if(!isset($config) || !$config instanceof Config) {
            $config = null;
            // Let's look around in cwd..
            foreach([
                        'config.php',
                        'app/config.php',
                        'config/config.php',
                    ] as $path) {
                if (is_file($path)) {
                    $r = require $path;
                    if($r instanceof Config) {
                        $config = $r;
                        break;
                    }
                    if($config instanceof Config) {
                        break;
                    }
                }
            }
        }
        if(!$config instanceof Config) {
            throw new \RuntimeException('$config must be a global instance of '.Config::class);
        }

        if(!isset($config['db'])) {
            if(!isset($config['database'])) {
                throw new \RuntimeException('Missing $config key: database|db');
            }
            $config->offsetSet('db', $config->offsetGet('database'));
        }

        $cfg = null;
        foreach(['phorm-generator'] as $k) {
            if(isset($config[$k])) {
                $cfg = $config->$k->toArray();
                break;
            }
        }
        if(!$cfg) {
            throw new \RuntimeException('Missing $config key: phorm-generator');
        }

        if(!$di) {
            $di = new FactoryDefault();
        }

        $di->setShared('config', $config);

        $di->setShared('router', new Router);

        $di->setShared('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('PhormGenerator');
            return $dispatcher;
        });

        $di->setShared('db', function() use($config) {
            $cfg = $config->db->toArray();
            if(isset($cfg['adapter'])) {
                $cls = '\Phalcon\Db\Adapter\Pdo\\'.Text::camelize($cfg['adapter']);
                $db = new $cls($cfg);
            } else {
                $db = new \Phalcon\Db\Adapter\Pdo\Mysql($cfg);
            }
            return $db;
        });

        $di->setShared('log', function() {
            $logger = new LoggerMultiple();
            $stdout = new LoggerStream("php://stdout");
            $stdout->setLogLevel(Logger::DEBUG);
            $stdout->setFormatter(new \Phalcon\Logger\Formatter\Line("[%type%] %message%"));
            $logger->push($stdout);
            return $logger;
        });

        parent::__construct($di);
    }
}