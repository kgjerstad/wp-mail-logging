<?php
/*
   Plugin Name: WP Mail Logging
   Plugin URI: http://wordpress.org/extend/plugins/wp-mail-logging/
   Support URI: https://github.com/No3x/wp-mail-logging/issues
   Version: 1.6.0
   Author: Christian Z&ouml;ller
   Author URI: http://no3x.de/
   Description: Logs each email sent by WordPress.
   Text Domain: wpml
   License: GPLv3
  */

/*
	"WordPress Plugin Template" Copyright (C) 2013 Michael Simpson  (email : michael.d.simpson@gmail.com)

	This following part of this file is part of WordPress Plugin Template for WordPress.

	WordPress Plugin Template is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	WordPress Plugin Template is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Contact Form to Database Extension.
	If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

namespace No3x\WPML;

use No3x\WPML\WPML_Init;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$WPML_minimalRequiredPhpVersion = '5.4';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function WPML_noticePhpVersionWrong() {
	global $WPML_minimalRequiredPhpVersion;
	echo '<div class="error">' .
	  __( 'Error: plugin "WP Mail Logging" requires a newer version of PHP to be running.',  'wpml' ).
			'<br/>' . __( 'Minimal version of PHP required: ', 'wpml' ) . '<strong>' . $WPML_minimalRequiredPhpVersion . '</strong>' .
			'<br/>' . __( 'Your server\'s PHP version: ', 'wpml' ) . '<strong>' . phpversion() . '</strong>' .
		 '</div>';
}


function WPML_PhpVersionCheck() {
	global $WPML_minimalRequiredPhpVersion;
	if ( version_compare( phpversion(), $WPML_minimalRequiredPhpVersion ) < 0 ) {
		add_action( 'admin_notices',  __NAMESPACE__ . '\WPML_noticePhpVersionWrong' );
		return false;
	}
	return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function WPML_i18n_init() {
	$pluginDir = dirname(plugin_basename(__FILE__));
	load_plugin_textdomain('wpml', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
WPML_i18n_init();


// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (WPML_PhpVersionCheck()) {
	// Only init and run the init function if we know PHP version can parse it
	require __DIR__ . '/autoload.php';

	// Create a new instance of the autoloader
	$loader = new \WPML_Psr4AutoloaderClass();

	// Register this instance
	$loader->register();

	// Add our namespace and the folder it maps to
	require_once __DIR__ . '/inc/redux/admin-init.php';
	$loader->addNamespace('No3x\\WPML\\', __DIR__ );
	$loader->addNamespace('No3x\\WPML\\Model\\', __DIR__ . '/model' );
	$loader->addNamespace('No3x\\WPML\\Settings\\', __DIR__ . '/inc/redux');
	$loader->addNamespace('No3x\\WPML\\ORM\\', __DIR__ . '/lib/vendor/brandonwamboldt/wp-orm/src');
	$loader->addNamespace('No3x\\WPML\\Pimple\\', __DIR__ . '/lib/vendor/pimple/pimple/src');
	if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}
	WPML_Init::getInstance()->init( __FILE__ );
}
