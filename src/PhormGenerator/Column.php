<?php

namespace PhormGenerator;

use Phalcon\Db;

class Column extends Component
{

    protected static function _getBindTypeString($type) {
        switch($type) {
            case Db\Column::BIND_SKIP:
                return '';
            case Db\Column::BIND_PARAM_NULL:
                return 'null';
            case Db\Column::BIND_PARAM_BOOL:
                return 'bool';
            case Db\Column::BIND_PARAM_INT:
                return 'int';
            case Db\Column::BIND_PARAM_DECIMAL:
                return 'float';
            case Db\Column::BIND_PARAM_STR:
                return 'string';
            case Db\Column::BIND_PARAM_BLOB:
                return 'string';
            default:
                throw new \RuntimeException('Unknown bind_type: '.$type);
        }
    }

    protected static function _getTypeString($type) {
        switch($type) {
            case Db\Column::TYPE_DECIMAL:
                return 'decimal';
            case Db\Column::TYPE_INTEGER:
                return 'integer';
            case Db\Column::TYPE_BIGINTEGER:
                return 'long';
            case Db\Column::TYPE_BOOLEAN:
                return 'boolean';
            case Db\Column::TYPE_CHAR:
                return 'char';
            case Db\Column::TYPE_DATE:
                return 'date';
            case Db\Column::TYPE_DATETIME:
                return 'datetime';
            case Db\Column::TYPE_TIMESTAMP:
                return 'timestamp';
            case Db\Column::TYPE_FLOAT:
                return 'float';
            case Db\Column::TYPE_DOUBLE:
                return 'double';
            case Db\Column::TYPE_TEXT:
                return 'text';
            case Db\Column::TYPE_VARCHAR:
                return 'varchar';
            case Db\Column::TYPE_TINYBLOB:
                return 'tinyblob';
            case Db\Column::TYPE_BLOB:
                return 'blob';
            case Db\Column::TYPE_MEDIUMBLOB:
                return 'mediumblob';
            case Db\Column::TYPE_LONGBLOB:
                return 'longblob';
            case Db\Column::TYPE_JSON:
                return 'json';
            case Db\Column::TYPE_JSONB:
                return 'jsonb';
            default:
                throw new \RuntimeException('Unknown type: '.$type);
        }
    }

    protected static function _getTypeHint($type) {
        switch($type) {
            case Db\Column::TYPE_BOOLEAN:
                return 'bool';
            case Db\Column::TYPE_DECIMAL:
            case Db\Column::TYPE_FLOAT:
                return 'float';
            case Db\Column::TYPE_INTEGER:
                return 'int';
            case Db\Column::TYPE_CHAR:
            case Db\Column::TYPE_VARCHAR:
            case Db\Column::TYPE_TEXT:
            case Db\Column::TYPE_DATE:
            case Db\Column::TYPE_DATETIME:
            case Db\Column::TYPE_TIMESTAMP:
                return 'string';
            default:
                throw new \RuntimeException('Unknown type: '.$type);
        }
    }

    /** @var Db\ColumnInterface */
    protected $obj;

    /** @var array */
    protected $data = [];

    public function __construct($name, Component $parent, $obj)
    {
        parent::__construct($name, $parent);
        $this->setObj($obj);
    }

    #region PHALCON COLUMN OBJ

    /**
     * @param Db\ColumnInterface $obj
     * @return $this
     */
    public function setObj(Db\ColumnInterface $obj)
    {
        $this->obj = $obj;
        return $this;
    }

    public function getSchemaName()
    {
        return $this->obj->getSchemaName();
    }

    public function getType()
    {
        return $this->obj->getType();
    }

    public function getTypeHint()
    {
        return self::_getTypeHint($this->getType());
    }

    public function getTypeReference()
    {
        return $this->obj->getTypeReference();
    }

    public function getTypeValues()
    {
        return $this->obj->getTypeValues();
    }

    public function getSize()
    {
        return $this->obj->getSize();
    }

    public function getScale()
    {
        return $this->obj->getScale();
    }

    public function isUnsigned()
    {
        return $this->obj->isUnsigned();
    }

    public function isNotNull()
    {
        return $this->obj->isNotNull();
    }

    public function isPrimary()
    {
        return $this->obj->isPrimary();
    }

    public function isAutoIncrement()
    {
        return $this->obj->isAutoIncrement();
    }

    public function isNumeric()
    {
        return $this->obj->isNumeric();
    }

    public function isFirst()
    {
        return $this->obj->isFirst();
    }

    public function getAfterPosition()
    {
        return $this->obj->getAfterPosition();
    }

    public function getBindType()
    {
        return $this->obj->getBindType();
    }

    public function getBindTypeString()
    {
        return self::_getBindTypeString($this->getBindType());
    }

    public function getDefault()
    {
        return $this->obj->getDefault();
    }

    public function hasDefault()
    {
        return $this->obj->hasDefault();
    }

    #endregion

    public function update()
    {
        $this->data = [
            'schema_name' => $this->getSchemaName(),
            'name' => $this->getName(),
            'size' => $this->getSize(),
            'bind_type' => $this->getBindType(),
            'bind_type_string' => $this->getBindTypeString(),
            'type' => $this->getType(),
            'type_hint' => $this->getTypeHint(),
            'type_reference' => $this->getTypeReference(),
            'type_values' => $this->getTypeValues(),
            'scale' => $this->getScale(),
            'has_default' => $this->hasDefault(),
            'default' => $this->getDefault(),
            'after_position' => $this->getAfterPosition(),
            'auto_increment' => $this->isAutoIncrement(),
            'first' => $this->isFirst(),
            'not_null' => $this->isNotNull(),
            'numeric' => $this->isNumeric(),
            'primary' => $this->isPrimary(),
            'unsigned' => $this->isUnsigned(),
        ];
        parent::update();
    }

    public function toArray()
    {
        return $this->data;
    }
}