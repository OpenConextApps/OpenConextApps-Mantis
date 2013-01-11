<?php
form_security_validate( 'plugin_simplesamlphp_config_update' );

plugin_config_set( 'simplesamlphp_install', gpc_get_string( 'simplesamlphp_install' ), true );

plugin_config_set( 'simplesamlphp_sp', gpc_get_string( 'simplesamlphp_sp' ), true );

plugin_config_set( 'simplesamlphp_auth_attr_username', gpc_get_string( 'simplesamlphp_auth_attr_username' ) );
plugin_config_set( 'simplesamlphp_auth_attr_email', gpc_get_string( 'simplesamlphp_auth_attr_email' ) );
plugin_config_set( 'simplesamlphp_auth_attr_displayname', gpc_get_string( 'simplesamlphp_auth_attr_displayname' ) );

form_security_purge( 'plugin_simplesamlphp_config_update' );

print_successful_redirect( plugin_page( 'config_page', true ) );

