{* Extra fields for from email addresses options form *}
<table style="display: none;">
    <tr class="crm-admin-options-form-block-mail_approvers">
	<td class="label">{$form.mail_approvers.label}</td>
	<td>{$form.mail_approvers.html}
	<div class="description">{ts}Select Groups that may approve Mailings to be sent from this address in the current domain.{/ts}</td>
    </tr>
</table>
{* Position the above rows in the layout table *}
<script type="application/javascript">
  CRM.$('.crm-admin-options-form-block-mail_approvers').insertAfter('.crm-admin-options-form-block-is_default'); 
</script>
