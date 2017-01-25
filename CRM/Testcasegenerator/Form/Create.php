<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Testcasegenerator_Form_Create extends CRM_Core_Form {

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Aanmaken van test cases'));

    $this->addSelect('group', array(
      'entity' => 'group_contact',
      'label' => ts('Kies een groep'),
      'context' => 'search',
      'placeholder' => ts('- any group -'),
    ));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Maak cases aan voor contacten in de geselecteerde groep'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $groupID = $values['group'];

    if ($groupID) {
      // create queue
      $queue = CRM_Testcasegenerator_Helper::singleton()->getQueue();

      // get all contacts in that group
      $params = array(
        'sequential' => 1,
        'group_id' => $groupID,
        'status' => 'Added',
        'options' => array('limit' => 250000),
      );
      $result = civicrm_api3('GroupContact', 'get', $params);

      // create a queue task for all contacts in the group
      $i = 0;
      foreach ($result['values'] as $contact) {
        //create a task
        $task = new CRM_Queue_Task(
          array('CRM_Testcasegenerator_Tasks', 'CreateCase'),
          array($contact['contact_id'])
        );

        //now add this task to the queue
        $queue->createItem($task);

        $i++;
      }

      CRM_Core_Session::setStatus("$i contacten toegevoegd.", 'Voorbereiding aanmaken cases', 'success');
    }

    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
