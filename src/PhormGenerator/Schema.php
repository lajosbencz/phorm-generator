<?php

namespace PhormGenerator;



class Schema extends Component
{
    /** @var Table[] */
    protected $components;

    public function getTables()
    {
        return $this->components;
    }

    public function update()
    {
        $this->components = [];
        foreach($this->db->listTables($this->name) as $table) {
            $this->components[$table] = new Table($table, $this);
        }
        parent::update();
        return $this;
    }
}