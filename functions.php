<?php /*
 * Plugin Name: Hatch - Updater
 * Version: 1.0-beta
 * Plugin URI: http://www.oboxthemes.com
 * Description: This plugin adds the Hatch Updater script
 * Author: Marc Perel
 * Author URI: http://www.oboxthemes.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: hatch-updater
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Marc Perel
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined( 'SCRIPT_DEBUG' ) && TRUE == SCRIPT_DEBUG ) {
    define( 'HATCH_UPDATER_VER', rand( 0 , 100 ) );
} else {
    define( 'HATCH_UPDATER_VER', '1.0-beta' );
}

define( 'HATCH_UPDATER_SLUG' , 'hatch-updater' );
define( 'HATCH_UPDATER_DIR' , plugin_dir_path( __FILE__ ) );
define( 'HATCH_UPDATER_URI' , plugin_dir_url( __FILE__ ) );

if( !function_exists( 'hatch_updater_init' ) ) {
    // Instantiate Plugin
    function hatch_updater_init() {

        // Check for the existance of the Hatch Options Panel class
        if( !class_exists( 'Hatch_Options_Panel' ) ) return false;

        // Load plugin class files
        $included = require_once( 'includes/class-updater.php' );

        global $hatch_updater;

        $hatch_updater = new Hatch_Updater();
        $hatch_updater->init();

        // Localization
        load_plugin_textdomain( HATCH_UPDATER_SLUG, FALSE, dirname( plugin_basename( __FILE__ ) ) . "/lang/" );
    } // hatch_updater_init

    add_action( "after_setup_theme", "hatch_updater_init", 100 );
}