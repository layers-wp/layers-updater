<?php /**
 * Hatch Options Panel
 *
 * This file outputs the WP Pointer help popups around the site
 *
 * @package Hatch
 * @since Hatch 1.0
 */

class Hatch_Updater_Options_Panel extends Hatch_Options_Panel {

    /**
    *  Constructor
    */

    public function __construct() {

        // Setup some folder variables
        $this->options_panel_dir = HATCH_UPDATER_DIR;

        // Setup the partial var
        $page =  str_replace( HATCH_THEME_SLUG . '-' , '', $_REQUEST[ 'page' ] );

        // Load template
        $this->body( $page );

    }
}