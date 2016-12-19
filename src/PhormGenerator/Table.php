<?php

namespace PhormGenerator;


use Phalcon\Text;

class Table extends Component
{
    /** @var Column[] */
    protected $components;
    protected $references;

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
            $reference->getReferencedColumns();
        }

        foreach($this->describeReferences() as $r) {
            $tc = $r->getColumns();
            $rc = $r->getReferencedColumns();
            $this->references[] = [
                'name' => $this->getName(),
                'ref_name' => $r->getReferencedColumns(),
                'fields' => $tc,
                'ref_fields' => $rc,
            ];
        }

        var_dump($this->references);

        /*
        if(isset($this->references[$tn])) {
            foreach($this->references[$tn] as $tc => $refs) {
                $an = Text::camelize(preg_replace('/_id$/','',$tc));
                foreach($refs as $rt => $refCols) {
                    foreach($refCols as $rc => $true) {
                        $this->belongsTo[$tn][$tc][$rt][$rc] = $an;
                    }
                }
            }
        }

        foreach($this->tables() as $t) {
            $tn = $t->getName();
            foreach($this->references as $rt => $refCols) {
                foreach($refCols as $rc => $refs) {
                    $an = Text::camelize(preg_replace('/_id$/','',$rc));
                    if(isset($refs[$tn])) {
                        foreach($refs[$tn] as $tc => $true) {
                            $this->hasMany[$tn][$tc][$rt][$rc] = $an;
                        }
                    }
                }
            }
        }
        foreach($this->hasMany as $tn => $cols) {
            foreach($cols as $tc => $refs) {
                foreach($refs as $rt => $refCols) {
                    $alias = Text::camelize($rt);
                    if(count($refCols) > 1) {
                        foreach($refCols as $rc => $av) {
                            $this->hasMany[$tn][$tc][$rt][$rc] = $alias.$av;
                        }
                    } else {
                        foreach($refCols as $rc => $av) {
                            $this->hasMany[$tn][$tc][$rt][$rc] = $alias;
                        }
                    }
                }
            }
        }
        */

        parent::update();
    }
}