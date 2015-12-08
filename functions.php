<?php /*
 * Plugin Name: Layers - Updater
 * Version: 1.3
 * Plugin URI: http://www.oboxthemes.com
 * Description: This plugin makes sure that your Layers Themes & Extensions are always up to date
 * Author: Marc Perel
 * Author URI: http://www.oboxthemes.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: layers-updater
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Marc Perel
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined( 'SCRIPT_DEBUG' ) && TRUE == SCRIPT_DEBUG ) {
    define( 'LAYERS_UPDATER_VER', rand( 0 , 100 ) );
} else {
    define( 'LAYERS_UPDATER_VER', '1.3' );
}

define( 'LAYERS_UPDATER_SLUG' , 'layer-updater' );
define( 'LAYERS_UPDATER_DIR' , plugin_dir_path( __FILE__ ) );
define( 'LAYERS_UPDATER_URI' , plugin_dir_url( __FILE__ ) );

if( !function_exists( 'layer_updater_init' ) ) {
    // Instantiate Plugin
    function layer_updater_init() {

        // Load plugin class files
        $included = require_once( 'includes/class-updater.php' );

        global $layer_updater;

        $layer_updater = new Layers_Updater();
        $layer_updater->init();

        // Localization
        load_plugin_textdomain( LAYERS_UPDATER_SLUG, FALSE, dirname( plugin_basename( __FILE__ ) ) . "/lang/" );
    } // layer_updater_init

    add_action( "after_setup_theme", "layer_updater_init", 100 );
}