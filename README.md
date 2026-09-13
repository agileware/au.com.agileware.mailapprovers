# Mail Approvers per Email (au.com.agileware.mailapprovers)

This is a [CiviCRM](https://civicrm.org) extension that adds per-"From Email Address" approval
control to CiviMail's built-in mailing approval workflow. Normally, CiviMail's "workflow support"
feature (Administer / CiviMail / CiviMail Component Settings) applies a single, system-wide
"approve mailings" permission to every mailing, regardless of which From address is used. This
extension lets you instead nominate one or more ACL Groups *per From Email Address*, so that:

* Users who belong to the approval group for a given From address can submit and have their own
  mailings automatically approved when sent from that address.
* Users who are not in the approval group for that address have their mailings queued for
  approval, and can only be approved by someone in that address's group (or by a user with the
  site-wide "access CiviMail" or "approve mailings" permission).

This is useful for organisations where different teams or brands send mail from different
addresses and need independent sign-off, rather than a single organisation-wide approver group.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Usage

* **"Approval Groups" field on From Email Addresses** — Under **Mailings / From Email Addresses**
  (`civicrm/admin/options/from_email_address`), each From Email Address option has an additional
  "Approval Groups" field. Select one or more "Access Control" (ACL) type Groups here; membership
  of any of these groups grants the ability to automatically approve mailings sent from that
  address. Leave the field blank to allow anyone with permission to schedule mailings to use that
  address without requiring approval.
* **Automatic approval on submission** — When a mailing is scheduled, the current user's group
  memberships are checked against the Approval Groups configured for the mailing's "From" address.
  If the user belongs to one of those groups, the mailing is automatically approved on submission.
  Otherwise it is queued for approval and the user sees a "Mailing has been submitted for
  approval" status message.
* **Approve/Reject pending mailings** — Mailings pending approval appear in the "Scheduled and
  Sent Mailings" list with an "Approve/Reject" link, visible to all users with access to that
  list. Only users in the relevant address's Approval Group (or with "access CiviMail"/"approve
  mailings" permission) can actually follow through and approve or reject the mailing, with an
  optional note explaining the decision.
* **Approved mailings** are treated as final and will begin processing on their scheduled date
  unless cancelled beforehand.
* **Rejected mailings** have their schedule cleared and return to the "Draft and Unscheduled
  Mailings" list, where they can be edited and resubmitted as a Draft. Resubmitting restarts the
  approval process.
* Users with the site-wide "access CiviMail" or "approve mailings" permission bypass this
  extension's checks entirely — their mailings are always automatically approved, and they can
  approve or reject any mailing regardless of From address.

This extension does not add any Scheduled Jobs, CiviRules actions, or API entities — its only
integration points are the From Email Address option form and CiviMail's existing approval
workflow.

## Special configuration requirements

* **CiviMail workflow support must be enabled.** Go to **Administer / CiviMail / CiviMail
  Component Settings** and turn on "Enable workflow support for CiviMail". This is a core CiviCRM
  setting (not specific to this extension) that introduces the "create mailings", "schedule
  mailings", and "approve mailings" permissions, and the Approve/Reject step this extension hooks
  into. Without it enabled, this extension has nothing to act on.
* **Permissions** — grant "create mailings" and "schedule mailings" (but *not* "approve mailings"
  or "access CiviMail") to roles that should be able to submit mailings for approval. Grant
  "approve mailings" or "access CiviMail" to roles that should be able to approve/reject *any*
  mailing regardless of From address, bypassing this extension's group-based checks.
* **At least one "Access Control" type Group per approval team** — create an ACL-type Group (not a
  regular Mailing List group) and assign the relevant contacts/users to it, then select that group
  in the "Approval Groups" field for the relevant From Email Address(es).
* No API keys, external credentials, or other extensions are required.

> **Note on CMS requirements:** the extension's permission override (`CRM_Mailapprovers_Permission`,
> extending `CRM_Core_Permission_Temp`) uses core CiviCRM APIs and is not written against any
> CMS-specific module. An earlier version of this README stated that the Drupal contrib **Rules**
> module was a prerequisite; nothing in the current code depends on it, so this requirement could
> not be confirmed and has been removed. If your site still relies on that module for some other
> reason, please verify independently.

## Requirements

* CiviCRM 5.51 or greater (as declared in `info.xml`; the code also contains fallbacks for
  pre-`Civi::settings()` versions of CiviCRM).
* CiviMail with "workflow support" enabled (see above).

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin
Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/). Once installed, enable
"Mail Approvers per Email (au.com.agileware.mailapprovers)" from **Administer / System Settings /
Extensions**.

## About the Authors

This CiviCRM extension was developed by [Agileware](https://agileware.com.au) with sponsorship by the [Australian Greens](https://greens.org.au)

[Agileware](https://agileware.com.au) provide a range of CiviCRM services including:
 - CiviCRM migration
 - CiviCRM integration
 - CiviCRM extension development
 - CiviCRM support
 - CiviCRM hosting
 - CiviCRM remote training services.

Support your CiviCRM developers. [Contact Agileware](https://agileware.com.au/contact) today!
