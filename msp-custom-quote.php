<?php
/**
 * Plugin Name: MSP Custom Quote
 * Author Name: Greg Bastianelli
 * Author URI:  www.drunk.kiwi
 */

 class MSP_Custom_Quote {

    public static $endpoint = 'custom-quote';

    function __construct(){
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_can_customize_product_meta_box' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'add_can_customize_product_meta_save') );

        add_action( 'woocommerce_template_single_excerpt', array( $this, 'maybe_add_custom_quote_link' ) );
    }

    public function add_can_customize_product_meta_box(){
        echo '<div class="option_group">';

        woocommerce_wp_checkbox( 
            array( 
                'id'            => '_msp_can_customize', 
                'wrapper_class' => '', 
                'label'         => __('Can this product be customized?', 'msp-sc' ), 
                )
            );

        echo '</div>';
    }

    public function add_can_customize_product_meta_save( $post_id ){
        $woocommerce_checkbox = isset( $_POST['_msp_can_customize'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_msp_can_customize', $woocommerce_checkbox );
    }

    public function maybe_add_custom_quote_link(){
        $can_customize = get_post_meta( get_the_post_ID(), '_msp_can_customize', true );
        if( $can_customize ){
            echo 'can customize';
        }
    }

    public function get_custom_quote_form(){
        wc_get_template( 'custom-quote.php', array(), '', __DIR__ . '/templates/' );
    }

    public function the_slug_exists( $post_name ) {
        global $wpdb;
        return ( $wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A') );
    }

    public static function insall(){
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
 