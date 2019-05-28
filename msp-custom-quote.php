<?php
/**
 * Plugin Name: MSP Custom Quote
 * Author Name: Greg Bastianelli
 * Author URI:  www.drunk.kiwi
 */

 class MSP_Custom_Quote {

    public static $endpoint = 'custom-quote';

    public function get_custom_quote_form(){
        wc_get_template( 'custom-quote.php', array(), '', __DIR__ . '/templates/' );
    }

    public function the_slug_exists( $post_name ) {
        global $wpdb;
        return ( $wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A') );
    }

    public static function install(){
        if( ! self::the_slug_exists( self::$endpoint ) ){
            $shortcode = str_replace( '-', '_', self::$endpoint );
            wp_insert_post( array(
                'post_title' => self::$endpoint,
                'post_content' => "[$shortcode]",
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page'
            ) );
        }
    }
}

new MSP_Custom_Quote();

register_activation_hook( __FILE__, array('MSP_Custom_Quote', 'install') );
add_shortcode( str_replace( '-', '_', MSP_Custom_Quote::$endpoint ), array( 'MSP_Custom_Quote', 'get_custom_quote_form' ) );

// add_filter( 'wc_get_template', 'cma_get_template', 10, 5 );
// function cma_get_template( $located, $template_name, $args, $template_path, $default_path ) {    
//     return $locate
// }
 