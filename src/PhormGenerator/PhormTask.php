<?php

namespace PhormGenerator;


use Phalcon\Cli\Task;
use Phalcon\Config;
use Phalcon\Logger\Multiple as LoggerMultiple;

/**
 * @property Config $config
 * @property LoggerMultiple $log
 */
class PhormTask extends Task
{
    public function indexAction()
    {
        $phorm = new PhormGenerator($this->config->db->dbname);
        $phorm->update();
        /*
        $config = $this->config['phorm-generator'];
        $tables = $this->db->listTables();
        if(!is_dir($config['dirAuto']))
        foreach($tables as $table) {
            $this->log->debug($table);
            $columns = $this->db->describeColumns($table);
            $indexes = $this->db->describeIndexes($table);
            $references = $this->db->describeReferences($table);
            //var_dump(['columns'=>$columns, 'indexes'=>$indexes, 'references'=>$references]);
        }
        */
    }
}