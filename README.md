README
======

**Mail Approvers per Email** extension for CiviCRM

Allows specifiying one or more ACL Groups that can approve Mailings for each
"From Email Address".

Prerequisites
-------------

This extension works with the "workflow support" feature for CiviMail.  As a
result, it requires: 

  * [CiviCRM](https://www.civicrm.org) 4.6 or greater
  * [Drupal](https://www.drupal.org)
  * The Drupal [Rules](https://www.drupal.org/project/rules) module

Installation
------------

  1. Place the `au.com.agileware.mailapprovers` tree in your configured CiviCRM
     extensions directory.
  2. Go to "Administer / System Settings / Extensions" (CiviCRM 4.7+) or
     "Administer / System Settings / Manage Extensions (CiviCRM 4.6) and enable
     the "Mail Approvers per Email (au.com.agileware.mailapprovers)" extension.
  3. Go to "Administer / CiviMail / CiviMail Component Settings" and switch on
     "Enable workflow support for CiviMail"
	 This will create three additional permission in Drupal; "CiviMail: create
     mailings", "CiviMail: schedule mailings", and "CiviMail: approve
     mailings".
  4. Ensure that user roles that should be able to submit mailings
     for approval in CiviCRM have the "CiviMail: create mailings" and "CiviMail:
     schedule mailings" permissions, but *Not* "CiviMail: approve mailings" or
     "CiviMail: access CiviMail"
  5. User roles that should be able to approve *any* mailing should have at
     least one of eithe the "CiviMail: approve mailings" or "CiviMail: access
     CiviMail" permission.  These will bypass the extension's permission checks.

Configuration
-------------

After installing the extension, each "From Email Address" may have one or more
"Approval Groups" associated with it to determine what users may automatically
send Mailings or Approve other users' mailing for that From Email Address.

  1. Create an "Access Control" type group and assign contacts to it as
     required.
  2. Under the "From Email Address" Options form ("Mailings / From Email
     Addresses"), each option has an additional field "Approval Groups" - add
     your new group to this and save the "From Email Address" Option.
  3. Leave this field blank to allow anyone who can schedule a Mailing to use
     this email address without approval.

Usage
-----

  - When creating a Mailing, the current user's groups are checked and if they
    are found to be in an ACL group that had access to approve Mailings for
    their select "From" email address, the Mailing will be automatically
    approved by the system on submission. Otherwise, the Mailing is queued for
    approval.
  - Mailings that are currently pending approval will appear the list for
    "Scheduled and Sent Mailings" with an "Approve/Reject" link - this will be
    displayed to all users who have access to this list. If the user has access
    to approve mailings for the selected "From" email address, following this
    link will allow them them review the Email and either Approve or Reject it
    with an optional note advising the reason for their decision.
  - Approved Mailings are treated as final by the system and will start
    processing on their scheduled date unless cancelled before then.
  - Rejected Mailings have their schedule deleted; these become available again
    via the "Draft and Unscheduled Mailings" list where they can be continued
	as a Draft Mailing. Resubmitting a Rejected email for approval will restart
    the approval process for it.
  - Administrators with "CiviMail: access CiviMail" or "CiviMail: approve
    mailings" permissions will have all Mailings automatically approved, and
    will be able to approve or reject any Mailing.

Author
------

[Agileware](https://agileware.com.au) <projects@agileware.com.au>

Development of this extension was sponsored by [Australian Greens](https://greens.org.au)
