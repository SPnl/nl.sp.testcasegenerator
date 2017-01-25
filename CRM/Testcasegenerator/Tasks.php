<?php
class CRM_Testcasegenerator_Tasks {
  public static function CreateCase(CRM_Queue_TaskContext $ctx, $contactID) {
    $params = array(
      'sequential' => 1,
      'subject' => "testcase " . $contactID,
      'case_type_id' => 3,
      'start_date' => date('Y-m-d'),
      'end_date' => '2018-' . date('m-d'),
      'contact_id' => $contactID,
    );
    $case = civicrm_api3('Case', 'create', $params);

    $params = array(
      'entity_id' => $case['id'],
      'custom_205' => 'TK2017',
    );
    $results = civicrm_api3('CustomValue', 'Create', $params);

    return true;
  }
}