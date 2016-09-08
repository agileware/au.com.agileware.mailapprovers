<?php
/**
 * Overrides certain permission checks in CiviCRM based on configrured Group
 * membership.
 */
class CRM_Mailapprovers_Permission extends CRM_Core_Permission_Temp {
  private $wrapped;

  private $arg;

  function __construct($arg, $wrapping = NULL) {
    $this->arg = $arg;

    if(is_a($wrapping, 'CRM_Core_Permission_Temp')) {
      $this->wrapped = $wrapping;
    }
  }

  function check($permission) {
    // We only care about the 'approve mailings' permission.
    if ($permission != 'approve mailings') {
      return ($this->wrapped ? $this->wrapped->check($permission) : FALSE);
    }

    // Pretend we can approve anything on the browse URL
    if ($this->arg[2] == 'browse') {
      return TRUE;
    }

    // Get Mailing ID from form.
    $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String');
    $mid = 0;

    if($qfKey) {
      $vars = array();
      CRM_Core_Session::singleton()->getVars($vars, 'CRM_Mailing_Form_Approve_' . $qfKey);
      // Some forms use mailing_id instead
      $mid = !empty($vars['mid'])? $vars['mid'] : $vars['mailing_id'];
    }
    else {
      // Mailing ID - must exist in request.
      $mid = CRM_Utils_Request::retrieve('mid', 'Positive');
    }

    if (!$mid) {
      return FALSE;
    }

    return self::canMail($mid);
  }

  public static function canMail($mid) {
    $approve = FALSE;
    try {

      // Get the from_email address of the email in question
      $mailing = civicrm_api('Mailing', 'getsingle', array('version' => '3', 'id' => $mid, 'return' => 'from_email'));

      // Mailing does not store the "From Email" ID - speculate based on the address.  This may return multiple results.
      $emails = civicrm_api('OptionValue', 'get', array(
                  'version' => '3',
                  'option_group_id' => 'from_email_address',
                  'domain_id' => CRM_Core_Config::singleton()->domainID(),
                  'label' => array('LIKE' => "%<{$mailing['from_email']}>%"),
                  'return' => 'value',
                ));

      // Get the list of approvers.
      $approvers = Civi::settings()->get('mail_approvers');

      // Get logged in user's groups as an array
      $groups = civicrm_api3('Contact', 'getvalue', array(
                  'id' => CRM_Core_Session::singleton()->getLoggedInContactID(),
                  'return' => 'groups'
                ));

      if($groups) {
        $groups = explode(',', $groups);
      }
      else {
        $groups = array();
      }

      // Assume at this point that the email can be approved.
      $approve = TRUE;

      // Loop through all possible approver lists.
      foreach($emails['values'] as $e) {
        if(empty($approvers[$e['value']])){
          continue;
        }

        // Restrictions found
        $approve = FALSE;

        $intersect = array_intersect($approvers[$e['value']], $groups);

        if(!empty($intersect)) {
          // User is in the approvers list, confirm and finish processing.
          $approve = TRUE;
          break;
        }
      }
    }
    catch (Exception $e) { }

    return $approve;
  }
}