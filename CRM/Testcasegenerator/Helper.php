<?php


class CRM_Testcasegenerator_Helper {
  const QUEUE_NAME = 'nl.sp.testcasegenerator.queue';

  private $queue;

  static $singleton;

  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Testcasegenerator_Helper();
    }
    return self::$singleton;
  }

  private function __construct() {
    $this->queue = CRM_Queue_Service::singleton()->create(array(
      'type' => 'Sql',
      'name' => self::QUEUE_NAME,
      'reset' => false, //do not flush queue upon creation
    ));
  }

  public function getQueue() {
    return $this->queue;
  }
}