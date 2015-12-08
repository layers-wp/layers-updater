<?php  /**
 * Layers Updater Class
 *
 * This file is used to modify any Updater related filtes, hooks & modifiers
 *
 * @package Layers
 * @since Layers 1.0
 */

class Layers_Updater {

    const LAYERS_API_REMOTE_URL = 'oboxthemes.com/api/v1';

    private static $instance;

    private $theme_checks_done;
    private $plugin_checks_done;

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
            LAYERS_UPDATER_SLUG . '-welcome',
            __( 'Update' , LAYERS_UPDATER_SLUG ),
            __( 'Update' , LAYERS_UPDATER_SLUG ),
            'manage_options',
            LAYERS_UPDATER_SLUG . '-register',
            array( $this , 'options_panel_ui' )
        );

    }

    /**
    *  Add the Options Panel class
    */
    public function add_settings() {

        add_settings_section(
            'layers_update_section',
            __( 'Layers Automatic Update Settings', LAYERS_UPDATER_SLUG ),
            NULL,
            'general'
        );

        add_settings_field(
            'layers_api_key',
            __( 'API Key', LAYERS_UPDATER_SLUG ),
            array( $this, 'show_api_key_setting' ),
            'general',
            'layers_update_section',
            array( 'label_for' => 'layers_api_key' )
        );

        register_setting( 'general', 'layers_api_key' );
    }

    public function show_api_key_setting() {
        printf(
            '<input name="layers_api_key" id="layers_api_key" type="text" value="%1$s" class="regular-text" />%2$s',
            $this->_get_api_key(),
            __( '<p class="description">Follow this link to get your API credentials <a href="http://oboxthemes.com/api">Obox Themes API</a></p>', LAYERS_UPDATER_SLUG )
        );
    }

    /**
    *  Obox API Call
    */

    private function _do_api_call( $endpoint = 'verify', $apikey = NULL, $return_array = true ) {

        global $layers_api_call;

        $api_param = ( NULL != $apikey ? '?apikey=' . $apikey : '' );

        $layers_api_call = wp_remote_get(
            'http://' . self::LAYERS_API_REMOTE_URL . '/' . $endpoint . $api_param,
            array(
                    'timeout' => 60,
                    'httpversion' => '1.1'
                )
        );

        if( is_wp_error( $layers_api_call ) ) return NULL;

        $body_as_array = json_decode( $layers_api_call['body'], $return_array );

        return $body_as_array;
    }

    /**
    * Get Obox API key
    */

    private function _get_api_key(){

        global $layers_api_key;

        $layers_api_key = '41f1f19176d383480afa65d325c06ed0';

        return $layers_api_key;
    }

    /**
    * Fetch available updates
    */

    private function _get_available_updates( $type = 'themes' ){

        // Get data
        $response = $this->_do_api_call( 'updates', $this->_get_api_key(), true );

        $this->checks_done = true;

        // If the response is not successful do nothing, just return; @TODO: Add a messaging system hook in
        if( NULL == $response ) return;

        // For themes return an array, for plugins return a StdObject
        if( 'themes' == $type ) {
            return json_decode( json_encode( $response['data'] ), true );
        } else {
            return json_decode( json_encode( $response['data'] ) );
        }
    }

    /**
    *  API key Saving
    */
    public function save_api_key(){
        global $layers_register_message;

        if( isset( $_REQUEST[ '_wpnonce_layers_api_key' ] ) ){

            // Get the posted API key
            $apikey = $_POST[ 'layers_obox_api_key' ];

            // Get data
            $apicheck = $this->_do_api_call( 'verify', $apikey, true );

            if( false == $apicheck[ 'success' ] ){
                $layers_register_message = $apicheck[ 'message' ];
                return;
            }

            if( ! wp_verify_nonce( $_REQUEST[ '_wpnonce_layers_api_key' ], 'layers_save_api_key' ) ) return;

            $layers_register_message = __( 'Your API key is valid!' , LAYERS_U);

            update_option( 'layers_api_key' , $apikey );
        }
    }

    /**
    *  Get Theme Version
    */

    private function get_theme_version( $slug = NULL ){

        if( NULL == $slug ) return;

        $theme = wp_get_theme( $slug );

        return $theme->get( 'Version' );
    }

    /**
    *  Add available Theme Updates to the theme_updates transient
    */

    public function transient_theme_updates( $theme_data ) {

        if( true == $this->theme_checks_done ) return;

        // Get an array of existing themes
        $existing_themes = wp_get_themes();

        // Update API Data
        $data = $this->_get_available_updates();

        // Loop over plugins looking for the latest version
        if ( isset( $data[ 'themes' ] ) ) {
            foreach ( $data[ 'themes' ] AS $key => $t ) {

                // if the current version is ahead or equal to the 'new' version, do nothing
                if( (bool) version_compare( $this->get_theme_version( $key ), $t[ 'version' ], '>=' ) ) continue;

                // Be sure that we have this theme installed
                if( !array_key_exists( $key, $existing_themes ) ) continue;

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

            if( $base == $plugin_slug_to_find ) {
                if( !isset( $plugin[1] ) ) {
                    $plugin_slug = $plugin[0];
                    break;
                } else {
                    $file = $plugin[1];
                    $plugin_slug = $base . '/' . $file;
                    break;
                }
            }
        }

        if( isset( $plugin_slug ) ) {
            return $plugin_slug;
        } else {
            return NULL;
        }
    }

    /**
    *  Get Plugin Version
    */

    private function get_plugin_version( $available_plugins = NULL, $slug = NULL ){

        if( NULL == $slug ) return;

        if( NULL == $available_plugins ) return;

        if( isset( $available_plugins[ $slug ][ 'Version' ] ) ) {
            return $available_plugins[ $slug ][ 'Version' ];
        } else {
            return NULL;
        }
    }

    /**
    *  Add available Plugin Updates to the plugin_updates transient
    */

   public function transient_plugin_updates( $plugin_data ) {

        if( true == $this->plugin_checks_done ) return;

        // Update API Data
        $data = $this->_get_available_updates( 'plugins' );

        // Get all available plugins
        $available_plugins = get_plugins();

        // Loop over plugins looking for the latest version
        if ( isset( $data->plugins ) ) {

            foreach ( $data->plugins AS $key => $p ) {

                // Get the full plugin slug
                $plugin_slug = $this->get_plugin_slug( $available_plugins, $p->slug );

                // Make sure that we have this plugin installed
                if( !$plugin_slug ) continue;

                // if the current version is ahead or equal to the 'new' version, do nothing

                if( (bool) version_compare( $this->get_plugin_version( $available_plugins, $plugin_slug), $p->update->new_version, '>=' ) ) continue;

                $plugin_data->response[ $plugin_slug ] = new stdClass();
                $plugin_data->response[ $plugin_slug ]->package = $p->update->package;
                $plugin_data->response[ $plugin_slug ]->new_version = $p->update->new_version;
                $plugin_data->response[ $plugin_slug ]->slug = $p->update->slug;
                $plugin_data->response[ $plugin_slug ]->url = $p->url;
                $plugin_data->response[ $plugin_slug ]->plugin = $plugin_slug;
            }

        }

        return $plugin_data;

    } // transient_theme_updates

}