<?php

namespace PhormGenerator;


use Phalcon\Config;

class PhormGenerator extends Component
{
    const CONFIG = [
        'namespace' => 'Root namespace of model files',
        'directory' => 'Root directory of model files',
        'generated' => 'If set, additional abstract classes will be generated within this namespace (default: Auto)',
        'baseModel' => 'Base class for models (default: Phalcon\Mvc\Model)',
        'baseView' => 'Base class for views (default: Phalcon\Mvc\View)',
    ];

    /** @var Schema[] */
    protected $components;

    public function getSchemas()
    {
        return $this->components;
    }

    public function __construct(...$schemas)
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
        $this->getDI()->setShared('config', $config);
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
        $this->getDI()->setShared('config',$config);
        parent::__construct('phorm-generator', $this);
        if(count($schemas) < 1) {
            $schemas[] = $this->config->db->dbname;
        }
        foreach($schemas as $schema) {
            $this->components[$schema] = new Schema($schema, $this);
        }
    }

    public function update()
    {
        parent::update();
        $decorator = new Decorator($this->config->toArray());
        /** @var Table $table */
        foreach($this->components[$this->config->db->dbname]->getTables() as $table) {
            echo str_repeat("=",48),PHP_EOL,
            $table->getName(),PHP_EOL,
            str_repeat("-",48),PHP_EOL,
            $decorator->generateAbstract($table),PHP_EOL,
            str_repeat("=",48),PHP_EOL,
            PHP_EOL;
        }
    }
}