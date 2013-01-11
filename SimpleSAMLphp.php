<?php

// AUTH constants defined in core/constant_inc.php.
define( 'SIMPLESAML_AUTH', 1007 );

function custom_function_override_auth_can_change_password() {
  if ( config_get_global( 'login_method' ) == SIMPLESAML_AUTH) {
    // SAML, so no password changes.
    return false;
  }
}

class SimpleSAMLphpPlugin extends MantisPlugin {
    function register() {
        $this->name = 'SimpleSAMLphp';
        $this->description = 'Authentication integration with SimpleSAMLphp, a php implementation of the SAML2 authentication protocol.';
        $this->page = 'config_page';

        $this->version = '1.0';
        $this->requires = array(
            'MantisCore' => '>= 1.2.0',
            );

        $this->author = '';
        $this->contact = '';
        $this->url = 'http://simplesamlphp.org';
    }

    function init() {
      global $g_simplesamlphp_instance;

      // Administrator is still validated versus database.
      if (( $_SERVER['PHP_SELF'] != 'login.php' ) && ($_POST['username'] != 'administrator') ) {
        // SAML Plugin is enabled, so set these globals here.
        config_set_global( 'login_method', SIMPLESAML_AUTH );
      }

      // @todo: configurable settings.
      config_set_global('simplesamlphp_instance', NULL);
      config_set_global('simplesamlphp_auth_attributes', array(
        'username' => 'urn:mace:dir:attribute-def:uid',
        'email' => 'urn:mace:dir:attribute-def:mail'));
      config_set_global('simplesamlphp_attributes', array());

      // Specific SAML global settings.
      config_set_global( 'reauthentication', OFF );
      config_set_global( 'allow_signup', OFF );

      // Peculiar behaviour of SAML, logout will instantly re-log you in. Just to
      // clarify this is not the way. @todo: discuss.
      // config_set_global( 'logout_redirect_page', 'login_saml.php' );

      // Set the default login page. Note: needs a small patch to index.php.
      config_set_global( 'login_page', plugin_page( 'login_saml.php', true ) );

      include_once(plugin_config_get( 'simplesamlphp_install', '/var/simplesamlphp', true ) . '/lib/_autoload.php');
      $g_simplesamlphp_instance = new SimpleSAML_Auth_Simple(plugin_config_get( 'simplesamlphp_sp', 'default-sp', true));
    }

    function config() {
        return array(
          # Path to simpleSAMLphp installation.
          'simplesamlphp_install' => '/var/simplesamlphp',

          # Name of SP configuration group
          'simplesamlphp_sp' => 'default-sp',

          # Username and email attributes.
          'simplesamlphp_auth_attr_username' => 'urn:mace:dir:attribute-def:uid',
          'simplesamlphp_auth_attr_email' => 'urn:mace:dir:attribute-def:mail',
          'simplesamlphp_auth_attr_displayname' => 'urn:mace:dir:attribute-def:displayName',

          # List of all attributes and coresponding value, which return proces
          # of authentication using simpleSAMLphp about authenticated user.
          'simplesamlphp_attributes' => array(),
        );
    }

   /**
     * Check if user (browser) is authenticated, if so return username, or FALSE if
     * user (browser) isnt authenticated
     *
     * @access public
     */
    function ssphp_is_user_authenticated() {
      global $g_simplesamlphp_instance;

      return $g_simplesamlphp_instance->isAuthenticated();
    }

    /**
     * Get username of authenticated user, or FALSE if user isnt authenticated,
     * or username isnt available (check configuration parameters)
     * Also return FALSE if value for email isnt available
     *
     * Also set global variable $g_simplesamlphp_attributes with list of available
     * atributes and values
     *
     * @access public
     */

    function ssphp_get_username(){
      global $g_simplesamlphp_instance, $g_simplesamlphp_auth_attributes, $g_simplesamlphp_attributes;

      $g_simplesamlphp_attributes = $g_simplesamlphp_instance->getAttributes();

      if(isset($g_simplesamlphp_attributes[ plugin_config_get( 'simplesamlphp_auth_attr_username' ) ][0]))
        return $g_simplesamlphp_attributes[ plugin_config_get( 'simplesamlphp_auth_attr_username' ) ][0];
      else
        return FALSE;
      if(! isset($g_simplesamlphp_attributes[ plugin_config_get( 'simplesamlphp_auth_attr_mail' ) ][0]))
        return FALSE;
    }

    /**
     * Start authentication proces, and return username of authenticate user or FALSE
     *
     * Be aware that once started SSO will not return to previus page before user is actualy
     * authenticated
     *
     * @access public
     */

    function ssphp_authenticate_user(){
      global $g_simplesamlphp_instance;

      $g_simplesamlphp_instance->requireAuth();

      return ssphp_get_username();
    }

    /**
     * Attempt to login with saml credentials.
     * If the user fails validation, false is returned
     * If the user passes validation, the cookies are set and
     * true is returned.  If $p_perm_login is true, the long-term
     * cookie is created.
     * @param string $p_username a prepared username
     * @param string $p_password a prepared password
     * @param bool $p_perm_login whether to create a long-term cookie
     * @return bool indicates if authentication was successful
     * @access public
     */
    function saml_auth_attempt_login( $p_username, $p_password, $p_perm_login = false ) {
      $t_email = '';
      $t_user_id = user_get_id_by_name( $p_username );

      if ( false === $t_user_id ) {
        $t_attibutes = config_get('simplesamlphp_attributes');
        $t_auth_attributes = config_get('simplesamlphp_auth_attributes');
        $t_email = $t_attibutes[ plugin_config_get( 'simplesamlphp_auth_attr_email' ) ][0];
        $realname = $t_attibutes[ plugin_config_get('simplesamlphp_auth_attr_displayname') ][0];

        # attempt to create the user
        $t_cookie_string = user_create( $p_username, md5( $p_password ), $t_email, null, false, true, $realname);
        if ( false === $t_cookie_string ) {
          return false;
        }

        # ok, we created the user, get the row again
        $t_user_id = user_get_id_by_name( $p_username );

        if( false === $t_user_id ) {
          return false;
        }
      }

      # check for disabled account
      if( !user_is_enabled( $t_user_id ) ) {
        return false;
      }

      # ok, we're good to login now
      # increment login count
      user_increment_login_count( $t_user_id );

      user_reset_failed_login_count_to_zero( $t_user_id );
      user_reset_lost_password_in_progress_count_to_zero( $t_user_id );

      # set the cookies
      auth_set_cookies( $t_user_id, $p_perm_login );
      auth_set_tokens( $t_user_id );

      return true;
    }

}
