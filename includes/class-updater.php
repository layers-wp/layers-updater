<?php  /**
 * Hatch Updater Class
 *
 * This file is used to modify any Updater related filtes, hooks & modifiers
 *
 * @package Hatch
 * @since Hatch 1.0
 */
class Hatch_Updater {

    private static $instance;

    /**
    *  Initiator
    */

    public static function init(){
        return self::$instance;
    }

    /**
    *  Constructor
    */

    public function __construct() {

        require_once( 'class-options-panel.php' );

        add_action( 'admin_menu' , array( $this, 'add_menu' ) , 70 );

        // Theme and Plugin Update Checkers
        add_filter( 'pre_set_site_transient_update_themes', array( $this, 'transient_theme_updates' ) );
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'transient_plugin_updates' ) );

    }

    /**
    *  Add Updater Menu
    */

    public function add_menu(){

        // Backup Page
        add_submenu_page(
            HATCH_THEME_SLUG . '-welcome',
            __( 'Update' , HATCH_THEME_SLUG ),
            __( 'Update' , HATCH_THEME_SLUG ),
            'manage_options',
            HATCH_THEME_SLUG . '-register',
            array( $this , 'options_panel_ui' )
        );

    }

    /**
    *  Add the Options Panel class
    */

    public function options_panel_ui(){

        $hatch_options_panel = new Hatch_Updater_Options_Panel();
        $hatch_options_panel->init();
    }

    /**
    *  Add available Theme Updates to the theme_updates transient
    */

    public function transient_theme_updates( $theme_data ) {

        // Update API Data
        $data = $this->_get_available_updates();
        // Loop over plugins looking for the latest version

        if ( isset( $data[ 'themes' ] ) ) {
            foreach ( $data[ 'themes' ] AS $key => $t ) {
                $theme_data->response[ $t['template'] ] = $t[ 'update' ];
            }
        }

        return $theme_data;
    } // transient_theme_updates

    /**
    * Get plugin slug as {plugin-folder}/{hook-file}
    */

    public function get_plugin_slug( $available_plugins, $plugin_slug_to_find ){

        foreach( $available_plugins as $slug => $details ){
            $plugin = explode( '/', $slug );

            $base = $plugin[0];
            $file = $plugin[1];

            if( $base == $plugin_slug_to_find ) {
                $plugin_slug = $base . '/' . $file;
                break;
            }
        }

        return $plugin_slug;
    }

    /**
    *  Add available Plugin Updates to the plugin_updates transient
    */

   public function transient_plugin_updates( $plugin_data ) {

        // Check if we've already done this check
        if ( empty( $plugin_data->checked ) ) {
            //return $plugin_data;
        }
        // Update API Data
        $data = $this->_get_available_updates( 'plugins' );

        // Get all available plugins
        $available_plugins = get_plugins();

        // Loop over plugins looking for the latest version
        if ( isset( $data->plugins ) ) {

            foreach ( $data->plugins AS $key => $p ) {
                $plugin_slug = $this->get_plugin_slug( $available_plugins, $p->slug );
                $plugin_data->response[ $plugin_slug ]->package = $p->update->package;
                $plugin_data->response[ $plugin_slug ]->new_version = $p->update->new_version;
                $plugin_data->response[ $plugin_slug ]->slug = $p->update->slug;
                $plugin_data->response[ $plugin_slug ]->url = $p->url;
                $plugin_data->response[ $plugin_slug ]->plugin = $plugin_slug;
            }

        }

        return $plugin_data;

    } // transient_theme_updates

    private function _get_available_updates( $type = 'themes' ){

        $theme_updates = '{
            "themes" : {
                "hatch": {
                        "template" : "hatch",
                        "update" : {
                            "package" : "https://bitbucket.org/marcperel/hatch",
                            "new_version":  "1.2"
                        },
                        "version": "1.1",
                        "name" : "Hatch"
                    }
                }
            }';

        $plugin_updates = '{
            "plugins" : {
                "hatch-woocommerce": {
                        "slug" : "hatch-woocommerce",
                        "url" : "http://oboxthemes.com/hatch/extensions/woocommerce",
                        "update" : {
                            "slug" : "hatch-woocommerce",
                            "package" : "https://downloads.wordpress.org/plugin/menu-customizer.0.2.zip",
                            "new_version":  "1.2"
                        },
                        "version": "1.1",
                        "name" : "Hatch WooCommerce"
                    }
                }
            }';

        if( 'themes' == $type ) {
            return json_decode( $theme_updates, true );
        } else {
            return json_decode( $plugin_updates );
        }
    }
}