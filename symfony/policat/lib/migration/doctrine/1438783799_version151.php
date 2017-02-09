<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version151 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('query_cache', array(
             'id' => 
             array(
              'type' => 'string',
              'primary' => '1',
              'length' => '255',
             ),
             'data' => 
             array(
              'type' => 'blob',
              'length' => '',
              'alias' => 'cacheData',
             ),
             'expire' => 
             array(
              'type' => 'timestamp',
              'length' => '25',
             ),
             ), array(
             'primary' => 
             array(
              0 => 'id',
             ),
             'collate' => 'utf8_general_ci',
             'charset' => 'utf8',
             ));
    }

    public function down()
    {
        $this->dropTable('query_cache');
    }
}