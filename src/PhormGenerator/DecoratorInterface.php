<?php

namespace PhormGenerator;


interface DecoratorInterface
{
    /**
     * Generates abstract class in config['directory']+config['generated'] folder
     * @param Table $table
     * @return string
     */
    function generateAbstract(Table $table);

    /**
     * Generates template class (extending abstract) in config['directory'] folder
     * @param Table $table
     * @return string
     */
    function generateTemplate(Table $table);

    /**
     * Generates stand-alone class (abstract content) in config['directory'] folder
     * @param Table $table
     * @return string
     */
    function generateModel(Table $table);
}