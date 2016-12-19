<?php

namespace PhormGenerator;


use Phalcon\Text;

class Decorator implements DecoratorInterface
{
    protected $_config = [
        'reusable' => false,
    ];

    /** @var Table[] */
    protected $tables = [];
    /** @var Table */
    protected $table;
    /** @var array */
    protected $references = [];
    /** @var array */
    protected $belongsTo = [];
    /** @var array */
    protected $hasMany = [];
    /** @var array */
    protected $hasManyToMany = [];


    function generateAbstract(Table $table)
    {
        $cfg = $table->config['phorm-generator'];
        $content = [];
        $content[] = $this->getInitialize([]);
        foreach($table->getColumns() as $column) {
            $content[] = $this->getProperty($column->getName(), $column->getTypeHint());
        }
        return $this->generate(
            trim($cfg['namespace'],'\\').'\\'.trim($cfg['generated'],'\\'),
            [],
            $table->getCamelName(),
            '\\'.trim($cfg['base' . ($table->db->viewExists($table->getName(), $table->getParent()->getName()) ? 'View' : 'Model')],'\\'),
            [],
            true,
            ...$content
        );
    }

    function generateTemplate(Table $table)
    {
        $cfg = $table->config['phorm-generator'];
        return $this->generate(
            trim($cfg['namespace'],'\\'),
            [],
            $table->getCamelName(),
            '\\'.trim($cfg['namespace'],'\\').'\\'.trim($cfg['generated'],'\\'),
            [],
            false,
            "\t\t// Custom overrides go here..."
        );
    }

    function generateModel(Table $table)
    {
        $cfg = $table->config['phorm-generator'];
        return $this->generate(
            trim($cfg['namespace'],'\\'),
            [],
            $table->getCamelName(),
            '\\'.trim($cfg['base' . ($table->db->viewExists($table->getName(), $table->getParent()->getName()) ? 'View' : 'Model')],'\\'),
            [],
            false,
            "\t\t// Stuff goes here..."
        );
    }

    function generate($namespace, array $uses, $camelName, $extends, array $implements, $abstract, ...$content)
    {
        return $this->getFile(
            $this->getNamespace($namespace),
            $this->getUses($uses),
            $this->getContainer(
                $camelName,
                $extends,
                $implements,
                $abstract,
                ...$content
            )
        );
    }

    function getFile(...$content)
    {
        return "<?php\r\n".join("\r\n",$content)."\r\n";
    }

    function getNamespace($namespace)
    {
        return "namespace $namespace;\r\n";
    }

    function getUses(array $aliasToNamespaces)
    {
        $r = "";
        foreach($aliasToNamespaces as $alias => $namespace) {
            if(is_numeric($alias)) {
                $r.= "use $namespace;\r\n";
            } else {
                $r.= "use $namespace as $alias;\r\n";
            }
        }
        return $r;
    }

    function getContainer($name, $extends='', array $implements=[], $abstract=false, ...$content)
    {
        $r = ($abstract ? 'abstract ' : '') . 'class ' . $name;
        if($extends) {
            $r.= ' extends '.$extends;
        }
        if(count($implements)>0) {
            $r.= ' implements '.join(', ', $implements);
        }
        $r.= "\r\n";
        $r.= "{\r\n";
        if(count($content)>0) {
            $r .= join("\r\n",$content)."\r\n";
        }
        $r.= "}\r\n";
        return $r;
    }

    function getInitialize(array $relations, ...$content)
    {
        $r = "\tpublic function initialize()\r\n";
        $r.= "\t{\r\n\t\t// init relations\r\n";
        foreach($relations as $relation) {
            $r.= $this->getRelation($relation[0],$relation[1],$relation[2],$relation[3]);
        }
        foreach($content as $k=>$v) {
            $content[$k] = "\t\t".trim($v);
        }
        $r.= join("\r\n", $content);
        $r.= "\t}\r\n";
        return $r;
    }

    function getRelation($type, array $fields, $referencedModel, array $referencedFields, array $options=[])
    {
        $opt = '';
        foreach($options as $k=>$v) {
            $opt.= "\t\t\t'$k' => '".escapeshellarg($v)."',\r\n";
        }
        return "\t\t\$this->$type('".join("','",$fields)."', ${referencedModel}::class, '".join("','",$referencedFields)."', [\r\n$opt]);\r\n";
    }

    function getRelationHint($type, array $fields, $referencedModel, array $referencedFields, array $options=[])
    {

    }

    function getTraits(array $aliasToTraits=[])
    {
        $r = '';
        foreach($aliasToTraits as $alias => $trait) {
            if(is_numeric($alias)) {
                $r.= "use $trait;\r\n";
            } else {
                $r.= "use $trait as $alias;\r\n";
            }
        }
        return $r;
    }

    function getSource($source)
    {
        return "\tpublic function getSource()\r\n{\r\n\treturn '$source';\r\n}\r\n";
    }

    function getColumnMap(array $map)
    {
        $r = "\tpublic function getColumnMap()\r\n\t{\r\n\t\treturn [\r\n";
        foreach($map as $alias => $column) {
            $r.= "\t\t\t'$alias' => '$column',\r\n";
        }
        $r.= "\t\t]\r\n\t}\r\n";
        return $r;
    }

    function getProperty($name, $type)
    {
        $ccName = Text::camelize($name);
        $r = '';
        $r.= "\t/** @var $type */\r\n";
        $r.= "\tprotected \$$name;\r\n\r\n";
        $r.= "\t/**\r\n";
        $r.= "\t * @param $type \$$name\r\n";
        $r.= "\t * @return \$this\r\n";
        $r.= "\t */\r\n";
        $r.= "\tpublic set$ccName(\$$name)\r\n";
        $r.= "\t{\r\n";
        $r.= "\t\t\$this->$name = \$$name;\r\n";
        $r.= "\t\treturn \$this;\r\n";
        $r.= "\t}\r\n\r\n";
        $r.= "\t/**\r\n";
        $r.= "\t * @return $type\r\n";
        $r.= "\t */\r\n";
        $r.= "\tpublic get$ccName()\r\n";
        $r.= "\t{\r\n";
        $r.= "\t\treturn \$this->$name;\r\n";
        $r.= "\t}\r\n\r\n";
        if(substr($name,0,3)=='is_' && ($type=='bool' || $type=='boolean')) {
            $isName = lcfirst($ccName);
            $r.= "\t/**\r\n";
            $r.= "\t * @return $type\r\n";
            $r.= "\t */\r\n";
            $r.= "\tpublic $isName()\r\n";
            $r.= "\t{\r\n";
            $r.= "\t\treturn \$this->$name;\r\n";
            $r.= "\t}\r\n\r\n";
        }
        return $r;
    }

    function getFinders($name, $type)
    {
        // TODO: Implement getFinders() method.
    }


    protected function _sourceWrite(Table $table) {
        $n = $table->getName();
        $name = Text::camelize($n);
        $file = $this->config->scaffold->model->output . $name . '.php';
        $ns = $this->config->scaffold->model->namespace;
        $ex = $table->isView() ? $this->config->scaffold->model->extend_view : $this->config->scaffold->model->extend;
        //dump($file); return;
        $source = "<?php\n\nnamespace " . $ns . ";\n\n";
        $source.= "use " . $ex . " as BaseModel;\n\n";
        $source.= $this->_sourceProperties($table) . "\n";
        $source.= "abstract class " . $name . " extends BaseModel\n";
        $source.= "{\n";
        $source.= $this->_sourceTable($table) . "\n";
        $source.= $this->_sourceColumnMap($table) . "\n";
        $source.= $this->_sourceInitialize($table) . "\n";
        $source.= $this->_sourceFields($table) . "\n";
        $source.= $this->_sourceFind($table) . "\n";
        $source.= "}\n";
        file_put_contents($file, $source);
    }
    protected function _sourceProperties(Table $table) {
        $tn = $table->getName();
        $ns = $this->config->scaffold->model->children_namespace;
        $source = "";
        $source.= "/**\n";
        if(isset($this->belongsTo[$tn])) {
            foreach($this->belongsTo[$tn] as $t2) {
                foreach($t2 as $rt => $t3) {
                    $rtc = Text::camelize($rt);
                    foreach($t3 as $name) {
                        $source.= " * @property \\" . $ns . "\\" . $rtc . " \$" . $name . "\n";
                    }
                }
            }
        }
        if(isset($this->hasMany[$tn])) {
            foreach($this->hasMany[$tn] as $t2) {
                foreach($t2 as $rt => $t3) {
                    $rtc = Text::camelize($rt);
                    foreach($t3 as $name) {
                        $source.= " * @property \\" . $ns . "\\" . $rtc . "[] \$" . $name . "\n";
                    }
                }
            }
        }
        $source.= " */";
        return $source;
    }
    protected function _sourceTable(Table $table) {
        $source = "\tpublic function getSource() {\n";
        $source.= "\t\treturn '" . $table->getName() . "';\n";
        $source.= "\t}\n";
        return $source;
    }
    protected function _sourceColumnMap(Table $table) {
        $source = "\tpublic function columnMap() {\n";
        $source.= "\t\treturn [\n";
        foreach($table->columns() as $c) {
            $n = $c->getName();
            $source.= "\t\t\t'" . $n . "' => '" . $n . "',\n";
        }
        $source.= "\t\t];\n";
        $source.= "\t}\n";
        return $source;
    }
    protected function _sourceInitialize(Table $table) {
        $source = "";
        $source.= "\t/**\n";
        $source.= "\t * @internal Virtual constructor\n";
        $source.= "\t */\n";
        $source.= "\tpublic function initialize() {\n";
        $tn = $table->getName();
        $ns = $this->config->scaffold->model->children_namespace;
        if(isset($this->belongsTo[$tn])) {
            foreach ($this->belongsTo[$tn] as $tc => $refs) {
                foreach ($refs as $rt => $cols) {
                    $alias = Text::camelize($rt);
                    foreach ($cols as $rc => $av) {
                        $source .= "\t\t\$this->belongsTo('" . $tc . "', '" . $ns . "\\" . $alias . "', '" . $rc . "', ['alias'=>'" . $av . "'".($this->_config['reusable']?", 'reusable'=>true":"")."]);\n";
                    }
                }
            }
        }
        if(isset($this->hasMany[$tn])) {
            foreach ($this->hasMany[$tn] as $tc => $refs) {
                foreach ($refs as $rt => $cols) {
                    $alias = Text::camelize($rt);
                    foreach ($cols as $rc => $av) {
                        $source .= "\t\t\$this->hasMany('" . $tc . "', '" . $ns . "\\" . $alias . "', '" . $rc . "', ['alias'=>'" . $av . "'".($this->_config['reusable']?", 'reusable'=>true":"")."]);\n";
                    }
                }
            }
        }
        $source.= "\t}\n";
        return $source;
    }
    protected function _sourceFields(Table $table) {
        $source = "";
        foreach($table->columns() as $c) {
            $n = $c->getName();
            $name = Text::camelize($n);
            $source.= "\t/** @var " . $c->getHint() . " */\n";
            $source.= "\tprotected \$" . $n . ";\n";
            if(!$table->isView()) {
                $source.= "\t/**\n";
                $source.= "\t * @param " . $c->getHint() . " \$" . $n . "\n";
                $source.= "\t * @return \$this\n";
                $source.= "\t */\n";
                $source .= "\tpublic function set" . $name . "(\$" . $n . ") {\n";
                $source .= "\t\t\$this->" . $n . " = \$" . $n . ";\n";
                $source .= "\t\treturn \$this;\n";
                $source .= "\t}\n";
            }
            $source .= "\t/**\n";
            $source .= "\t * @return " . $c->getHint() . "\n";
            $source .= "\t */\n";
            $source.= "\tpublic function get" . $name . "() {\n";
            $source.= "\t\treturn \$this->" . $n . ";\n";
            $source.= "\t}\n";
            if($c->getHint() == 'bool' && substr($n,0,3)=='is_') {
                $source.= "\t/**\n";
                $source.= "\t * @return " . $c->getHint() . "\n";
                $source.= "\t */\n";
                $source.= "\tpublic function " . lcfirst($name) . "() {\n";
                $source.= "\t\treturn \$this->" . $n . ";\n";
                $source.= "\t}\n";
            }
            $source.= "\n";
        }
        return $source;
    }
    protected function _sourceFind(Table $table) {
        $hint = '\\' . $this->config->scaffold->model->children_namespace . '\\' . Text::camelize($table->getName());
        $source = "";
        $source.= "\t/**\n";
        $source.= "\t * @param mixed \$parameters (optional)\n";
        $source.= "\t * @return " . $hint . "[]\n";
        $source.= "\t */\n";
        $source.= "\tpublic static function find(\$parameters=null) {\n";
        $source.= "\t\treturn parent::find(\$parameters);\n";
        $source.= "\t}\n\n";
        $source.= "\t/**\n";
        $source.= "\t * @param mixed \$parameters (optional)\n";
        $source.= "\t * @return " . $hint . "\n";
        $source.= "\t */\n";
        $source.= "\tpublic static function findFirst(\$parameters=null) {\n";
        $source.= "\t\treturn parent::findFirst(\$parameters);\n";
        $source.= "\t}\n\n";
        foreach($table->columns() as $c) {
            $n = $c->getName();
            $cn = Text::camelize($n);
            $source.= "\t/**\n";
            $source.= "\t * @param mixed \$" . $n . "\n";
            $source.= "\t * @return " . $hint . "\n";
            $source.= "\t */\n";
            $source.= "\tpublic static function findFirstBy" . $cn . "(\$" . $n . ") {\n";
            $source.= "\t\treturn parent::findFirstBy" . $cn . "(\$" . $n . ");\n";
            $source.= "\t}\n\n";
        }
        return $source;
    }
    public function __construct(array $config=[]) {
        $this->_config = $config;
    }
    public function initialize() {
        if(!is_dir($this->config->scaffold->model->output)) {
            mkdir($this->config->scaffold->model->output, 0777, true);
        }
    }
    public function tables() {
        return $this->tables;
    }
    public function table($table) {
        return $this->tables[$table];
    }
    public function column($table, $column) {
        return $this->table($table)->column($column);
    }
    public function write() {
        foreach($this->tables() as $t) {
            $cn = Text::camelize($t->getName());
            echo 'Generating auto for ', $cn, PHP_EOL;
            $this->_sourceWrite($t);
            $childPath = $this->config->scaffold->model->children_output . '/' . $cn . '.php';
            if(!is_file($childPath)) {
                echo 'Generating child for ', $cn, PHP_EOL;
                $autoNS = $this->config->scaffold->model->namespace;
                $childNS = $this->config->scaffold->model->children_namespace;
                if(strpos($autoNS,$childNS) === 0) {
                    $use = "";
                    $extNS = substr($autoNS, strlen($childNS)+1) . "\\" . $cn;
                } else {
                    $use = "use " . $autoNS . " as Base".$cn.";\n\n";
                    $extNS = "Base".$cn;
                }
                file_put_contents($childPath,
                    "<?php\n\n" .
                    "/** {@inheritdoc} */\n" .
                    "namespace " . $childNS . ";\n\n" .
                    $use .
                    "class " . $cn . " extends " . $extNS . "\n" .
                    "{\n" .
                    "}\n"
                );
            }
        }
    }
}