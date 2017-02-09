<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version69 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addIndex('campaign', 'campaign_status', array(
             'fields' => 
             array(
              0 => 'status',
             ),
             ));
        $this->addIndex('campaign', 'campaign_name', array(
             'fields' => 
             array(
              'name' => 
              array(
              'length' => '100',
              ),
             ),
             ));
        $this->addIndex('petition', 'petition_name', array(
             'fields' => 
             array(
              'name' => 
              array(
              'length' => '100',
              ),
             ),
             ));
    }

    public function down()
    {
        $this->removeIndex('campaign', 'campaign_status', array(
             'fields' => 
             array(
              0 => 'status',
             ),
             ));
        $this->removeIndex('campaign', 'campaign_name', array(
             'fields' => 
             array(
              'name' => 
              array(
              'length' => '100',
              ),
             ),
             ));
        $this->removeIndex('petition', 'petition_name', array(
             'fields' => 
             array(
              'name' => 
              array(
              'length' => '100',
              ),
             ),
             ));
    }
}