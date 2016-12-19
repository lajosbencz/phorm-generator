<?php

namespace PhormGenerator;


class PhormGenerator extends Component
{
    const CONFIG = [
        'namespace' => 'Root namespace of model files',
        'directory' => 'Root directory of model files',
        'generated' => 'If set, additional abstract classes will be generated within this namespace (default: Auto)',
        'baseModel' => 'Base class for models (default: Phalcon\Mvc\Model)',
        'baseView' => 'Base class for views (default: Phalcon\Mvc\View)',
    ];

    public function __construct(...$schemas)
    {
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