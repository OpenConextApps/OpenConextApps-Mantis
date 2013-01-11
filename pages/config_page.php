<?php
auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

html_page_top( lang_get( 'plugin_format_title' ) );

print_manage_menu( );

?>

<form action="<?php echo plugin_page( 'config_update' ) ?>" method="post">

<?php echo form_security_field( 'plugin_simplesamlphp_config_update' ) ?>

<br />

<table align="center" class="width50" cellspacing="1">

<tr>
	<td class="form-title" colspan="3">
<?php  echo lang_get( 'plugin_simplesamlphp_title' ) . ': ' . lang_get( 'plugin_simplesamlphp_config' ); ?>
	</td>
</tr>

<tr <?php echo helper_alternate_class( )?>>
	<td class="category" width="50%">
		<?php echo lang_get( 'plugin_simplesamlphp_location' )?>
	</td>
	<td class="center" width="50%">
		<label><input type="text" size=40 name="simplesamlphp_install" value='<?php echo( plugin_config_get( 'simplesamlphp_install' ) ) ?>'/></label>
	</td>
</tr>

<tr <?php echo helper_alternate_class( )?>>
	<td class="category" width="50%">
		<?php echo lang_get( 'plugin_simplesamlphp_sp' )?>
	</td>
	<td class="center" width="50%">
		<label><input type="text" size=40 name="simplesamlphp_sp" value='<?php echo( plugin_config_get( 'simplesamlphp_sp' ) ) ?>'/></label>
	</td>
</tr>

<tr <?php echo helper_alternate_class( )?>>
	<td class="category" width="50%">
		<?php echo lang_get( 'plugin_simplesamlphp_auth_attr_username' )?>
	</td>
	<td class="center" width="50%">
		<label><input type="text" size=40 name="simplesamlphp_auth_attr_username" value='<?php echo( plugin_config_get( 'simplesamlphp_auth_attr_username' ) ) ?>'/></label>
	</td>
</tr>

<tr <?php echo helper_alternate_class( )?>>
	<td class="category" width="50%">
		<?php echo lang_get( 'plugin_simplesamlphp_auth_attr_email' )?>
	</td>
	<td class="center" width="50%">
		<label><input type="text" size=40 name="simplesamlphp_auth_attr_email" value='<?php echo( plugin_config_get( 'simplesamlphp_auth_attr_email' ) ) ?>'/></label>
	</td>
</tr>

<tr <?php echo helper_alternate_class( )?>>
	<td class="category" width="50%">
		<?php echo lang_get( 'plugin_simplesamlphp_auth_attr_displayname' )?>
	</td>
	<td class="center" width="50%">
		<label><input type="text" size=40 name="simplesamlphp_auth_attr_displayname" value='<?php echo( plugin_config_get( 'simplesamlphp_auth_attr_displayname' ) ) ?>'/></label>
	</td>
</tr>

<tr>
	<td class="center" colspan="3">
		<input type="submit" class="button" value="<?php echo lang_get( 'change_configuration' )?>" />
	</td>
</tr>

</table>
</form>

<?php
html_page_bottom();
