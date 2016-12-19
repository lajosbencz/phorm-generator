<?php

namespace PhormGenerator;


class Table extends Component
{
    /** @var Column[] */
    public $components;

    public function getColumns()
    {
        return $this->components;
    }

    public function describeColumns()
    {
        return $this->db->describeColumns($this->name);
    }

    public function describeIndexes()
    {
        return $this->db->describeIndexes($this->name);
    }

    public function describeReferences()
    {
        return $this->db->describeReferences($this->name);
    }

    public function update()
    {
        $this->components = [];
        foreach($this->describeColumns() as $column) {
            $name = $column->getName();
            $this->components[$name] = new Column($name, $this, $column);
        }
        foreach($this->describeReferences() as $reference) {
            //var_dump($this->name, $reference);
        }
        parent::update();
    }
}