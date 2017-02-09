<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version97 extends Doctrine_Migration_Base {

  public function up() {
    $this->addColumn('widget', 'activity_at', 'timestamp', '25', array(
        'notnull' => '',
    ));
  }

  public function down() {
    $this->removeColumn('widget', 'activity_at');
  }

  public function postUp() {
    parent::postUp();

    $q = Doctrine_Manager::getInstance()->getCurrentConnection();
    $q->exec('UPDATE widget w SET w.activity_at = (SELECT created_at FROM petition_signing ps WHERE ps.widget_id = w.id ORDER BY ps.id DESC LIMIT 1)');
  }

}