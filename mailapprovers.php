<?php

require_once 'mailapprovers.civix.php';

define('ACL_GROUP_TYPE', 1);

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function mailapprovers_civicrm_config(&$config) {
  _mailapprovers_civix_civicrm_config($config);

  $arg = explode('/', $_GET[$config->userFrameworkURLVar]);

  if (isset($arg[1]) && ('mailing' == $arg[1])) {
    if (!(CRM_Core_Config::singleton()->userPermissionTemp)) {
      CRM_Core_Config::singleton()->userPermissionTemp = new CRM_Mailapprovers_Permission($arg);
    }
    CRM_Core_Session::setStatus(kpr(CRM_Core_Config::singleton()->userPermissionTemp, TRUE));
  }
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function mailapprovers_civicrm_xmlMenu(&$files) {
  _mailapprovers_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function mailapprovers_civicrm_install() {
  _mailapprovers_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function mailapprovers_civicrm_uninstall() {
  _mailapprovers_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function mailapprovers_civicrm_enable() {
  _mailapprovers_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function mailapprovers_civicrm_disable() {
  _mailapprovers_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function mailapprovers_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mailapprovers_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function mailapprovers_civicrm_managed(&$entities) {
  _mailapprovers_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mailapprovers_civicrm_caseTypes(&$caseTypes) {
  _mailapprovers_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mailapprovers_civicrm_angularModules(&$angularModules) {
_mailapprovers_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function mailapprovers_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mailapprovers_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 *
 * Adds fields to the mail from addresses form to implement approval groups functionality.
 */
function mailapprovers_civicrm_buildForm($formName, &$form) {
  if (('CRM_Admin_Form_Options' == $formName) && !empty($form->urlPath[3]) && ('from_email_address' == $form->urlPath[3])) {
    // Fetch a list of Groups.
    $groups = civicrm_api3('Group', 'get', array('options' => array('limit' => 0)));

    $groupOpt = array();

    // Only use ACL groups.  Filtering by group type in the API does not work well.
    foreach($groups['values'] as $group) {
      if (!empty($group['group_type']) && in_array(ACL_GROUP_TYPE, $group['group_type'])){
        $groupOpt[$group['id']] = $group['title'];
      }
    };

    // Add Select widget for mail approval groups to the option form.
    $form->add('select', 'mail_approvers', ts('Approval Groups'), $groupOpt, FALSE, array(
        'multiple' => 'multiple',
        'class' => 'crm-select2',
        'placeholder' => ts('Unrestricted'),
      ));

    $approvers = Civi::settings()->get('mail_approvers');

    $default_approvers = array();
    if (isset($approvers[$form->_defaultValues['value']])) {
      $default_approvers = $approvers[$form->_defaultValues['value']];
    }

    $defaults = array ('mail_approvers' => $default_approvers);

    $form->setDefaults($defaults);

    // Add an additional template region to insert the field into the form.
    $templatePath = realpath(dirname(__FILE__).'/templates');

    CRM_Core_Region::instance('page-body')->add(array('template' => $templatePath . '/CRM/Mailapprovers/Form/Option.tpl'));
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * Saves the mail_approvers settings from the Options form.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function mailapprovers_civicrm_postProcess($formName, &$form) {
  $params = $form->exportValues();

  if('CRM_Admin_Form_Options' == $formName && isset($params['mail_approvers'])) {
    $approvers = Civi::settings()->get('mail_approvers');
    $approvers[$params['value']] = $params['mail_approvers'];
    Civi::settings()->set('mail_approvers', $approvers);
  }
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function mailapprovers_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function mailapprovers_civicrm_navigationMenu(&$menu) {
  _mailapprovers_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'au.com.agileware.mailapprovers')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _mailapprovers_civix_navigationMenu($menu);
} // */
