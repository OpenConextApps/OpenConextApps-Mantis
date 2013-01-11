# SURFconext SimpleSAMLphp plugin for Mantis

This is the SURFconext version of the SimpleSAMLphp plugin for
Mantis. Provides a SAML (http://en.wikipedia.org/wiki/SAML_2.0)
authentication to your Mantis site.
library.

You will need a working SURFconext simpleSAMLphp installation.

## Pre installation

Install and configure SimpleSAMLphp
(http://www.simplesamlphp.org). For technical information on how to
install and test SURFconext, look under section 'Setting up an SP' on
page
https://wiki.surfnetlabs.nl/display/surfconextdev/My+First+SP+-+PHP


## Installation
 1. Unpack this plugin in <MantisBT_Root_Folder>/plugins/

 2. While logged into your Mantis installation as an administrator, go to
    'Manage' -> "Manage Plugins".

 3. Enable this plugin.

 4. Click on the "SimpleSAMLphp" link to configure it. You should
    configure the location of SimpleSAMLphp, and the 3 attributes:
    uid, mail and givenName.

 5. The SimpleSAML plugin installs a new login page: login_saml.php
    (located in the pages subdirectory). When the module is enabled,
    it the url: plugins/SimpleSAMLphp/pages/login_saml.php. In order
    to change the default login page we need a small modification to
    index.php from the root:

```php
@@ -28,5 +28,5 @@ require_once( 'core.php' );
 if ( auth_is_user_authenticated() ) {
        print_header_redirect( config_get( 'default_home_page' ) );
 } else {
-       print_header_redirect( 'login_page.php' );
+       print_header_redirect( config_get( 'login_page', 'login_page.php' ) );
 }

```

## Usage

Go to the root of the mantis installation (index.php), and you will be
redirected to the external authentication provider. In order to login
as adminstrator use the url:
http://<your_mantis_installation>/login_page.php. Here you can login
using the administration user and password, you entered during
installation of Mantis. This plugin disables all password changes.

The SimpleSAML plugin installs a new login page: login_saml.php
(located in the pages subdirectory). When the module is enabled, it
sets the config variable 'login_page' to this page.

## Credits

This plugin was based on the original work of Dubravko Penezic
(http://comments.gmane.org/gmane.comp.bug-tracking.mantis.devel/3195). This
version is a rewrite and uses the mantis API and minimizes the needed
number of patches. Also it uses the configuration method of MantisBT
for the attributes.

This plugin was build for SURFnet, for their SURFconext project.

Copyright (C) 2012,2013 SURFnet
