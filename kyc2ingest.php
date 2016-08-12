<?php
/**
 * @package KYC
 */
/*
Plugin Name: Know Your City ONA ingester
Plugin URI: http://knowyourcity.info/
Description: This Wordpress plugin takes data from the ONA API and puts it into Know Your City's Wordpress website
Version: 0.0.1
Author: Jason Norwood-Young
Author URI: http://10layer.com
License: MIT
Text Domain: kyc
*/

/*
The MIT License (MIT)
Copyright (c) <year> <copyright holders>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Copyright 2016 Slum Dwellers International
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'KYC2INGEST_VERSION', '3.1.7' );
define( 'KYC2INGEST__MINIMUM_WP_VERSION', '3.2' );
define( 'KYC2INGEST__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KYC2INGEST__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// define( 'AKISMET_DELETE_LIMIT', 100000 );

// register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
// register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );

// require_once( KYC2INGEST__PLUGIN_DIR . 'class.akismet.php' );
// require_once( KYC2INGEST__PLUGIN_DIR . 'class.akismet-widget.php' );

// add_action( 'init', array( 'Akismet', 'init' ) );

if ( is_admin() ) {
	require_once( KYC2INGEST__PLUGIN_DIR . 'class.kyc2ingest-admin.php' );
	add_action( 'init', array( 'KYC2Ingest_Admin', 'init' ) );
}

require_once( KYC2INGEST__PLUGIN_DIR . 'class.kyc2ingest-frontend.php' );
add_action("init", array("KYC2Ingest_Frontend", "init"));

// //add wrapper class around deprecated akismet functions that are referenced elsewhere
// require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );

