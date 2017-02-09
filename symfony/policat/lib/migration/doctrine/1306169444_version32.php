<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version32 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('owner', 'owner_first_widget_id_widget_id', array(
             'name' => 'owner_first_widget_id_widget_id',
             'local' => 'first_widget_id',
             'foreign' => 'id',
             'foreignTable' => 'widget',
             'onUpdate' => '',
             'onDelete' => 'SET NULL',
             ));
        $this->createForeignKey('owner', 'owner_campaign_id_campaign_id', array(
             'name' => 'owner_campaign_id_campaign_id',
             'local' => 'campaign_id',
             'foreign' => 'id',
             'foreignTable' => 'campaign',
             'onUpdate' => '',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('widget_owner', 'widget_owner_owner_id_owner_id', array(
             'name' => 'widget_owner_owner_id_owner_id',
             'local' => 'owner_id',
             'foreign' => 'id',
             'foreignTable' => 'owner',
             'onUpdate' => '',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('widget_owner', 'widget_owner_widget_id_widget_id', array(
             'name' => 'widget_owner_widget_id_widget_id',
             'local' => 'widget_id',
             'foreign' => 'id',
             'foreignTable' => 'widget',
             'onUpdate' => '',
             'onDelete' => 'CASCADE',
             ));
        $this->addIndex('owner', 'owner_first_widget_id', array(
             'fields' => 
             array(
              0 => 'first_widget_id',
             ),
             ));
        $this->addIndex('owner', 'owner_campaign_id', array(
             'fields' => 
             array(
              0 => 'campaign_id',
             ),
             ));
        $this->addIndex('widget_owner', 'widget_owner_owner_id', array(
             'fields' => 
             array(
              0 => 'owner_id',
             ),
             ));
        $this->addIndex('widget_owner', 'widget_owner_widget_id', array(
             'fields' => 
             array(
              0 => 'widget_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('owner', 'owner_first_widget_id_widget_id');
        $this->dropForeignKey('owner', 'owner_campaign_id_campaign_id');
        $this->dropForeignKey('widget_owner', 'widget_owner_owner_id_owner_id');
        $this->dropForeignKey('widget_owner', 'widget_owner_widget_id_widget_id');
        $this->removeIndex('owner', 'owner_first_widget_id', array(
             'fields' => 
             array(
              0 => 'first_widget_id',
             ),
             ));
        $this->removeIndex('owner', 'owner_campaign_id', array(
             'fields' => 
             array(
              0 => 'campaign_id',
             ),
             ));
        $this->removeIndex('widget_owner', 'widget_owner_owner_id', array(
             'fields' => 
             array(
              0 => 'owner_id',
             ),
             ));
        $this->removeIndex('widget_owner', 'widget_owner_widget_id', array(
             'fields' => 
             array(
              0 => 'widget_id',
             ),
             ));
    }
}