<?php

namespace PhormGenerator;


use Phalcon\Config;
use Phalcon\Di\Injectable;
use Phalcon\Logger\Multiple as LoggerMultiple;
use Phalcon\Text;

/**
 * @property Config $config
 * @property LoggerMultiple $log
 */
class Component extends Injectable
{
    /** @var string */
    protected $name;
    /** @var Component */
    protected $parent;
    /** @var Component */
    protected $components = [];

    protected function initialize()
    {
        $this->components = [];
    }

    /**
     * @param string $name
     * @param Component $parent
     */
    public function __construct($name, Component $parent)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->initialize();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCamelName()
    {
        return Text::camelize($this->name);
    }

    /**
     * @return Component
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function update()
    {
        /** @var Component $component */
        foreach($this->components as $component) {
            $component->update();
        }
    }
}