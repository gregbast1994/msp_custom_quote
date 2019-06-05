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

        add_action( 'woocommerce_single_product_summary', array( $this, 'maybe_add_custom_quote_link' ), 35 );

        add_action( 'admin_post_msp_process_custom_quote', array( $this, 'process_rfq' ) );
        add_action( 'admin_post_nopriv_msp_process_custom_quote', array( $this, 'process_rfq' ) );

    }

    public function process_rfq(){
        if( ! wp_verify_nonce( 'process-quote-' . $_POST['product_id'] ) ){
            if( isset( $_FILES['file'] ) ){
                $img_id = media_handle_upload( 'file', 0, array( 'post_name' => 'rfq_' . $_POST['product_id'] . '_' . $_POST['email']) );
            }

            if ( ! is_wp_error( $img_id ) ) {
                $this->send_rfq_to_admin( get_attached_file( $img_id ), $_POST );
            } else {
                wp_redirect( $_POST['_wp_http_referer'] );
            }
        }
    }

    public function send_rfq_to_admin( $attachment, $data ){
        $product = wc_get_product( $data['product_id'] );

        if( empty( $product ) ) return;

        $to = $data['email'];
        $subject = "RFQ - " . $data['company'];
        $headers = array('Content-Type: text/html; charset=UTF-8');

        ob_start();
        ?>
            <h1><?php echo $subject ?></h1>
            <p>Product: <?php echo $product->get_name(); ?></p>
            <p>Quantity: <?php echo $data['qty'] ?></p>
            <p>Image ( also attached ): <?php echo $attachment ?></p>
            <p>Tagline: <?php echo $data['tagline'] ?></p>

            <hr>

            <h3>Ship to: </h3>
            <p>Company Name: <?php echo $data['company'] ?></p>
            <p>Street: <?php echo $data['street'] ?></p>
            <p>Zip: <?php echo $data['zip'] ?></p>

            <p>Reply To: <?php echo $data['email'] ?></p>
            <p>Phone: <?php echo $data['phone'] ?></p>
        <?php
        $html = ob_get_clean();
        wp_mail( get_bloginfo( 'admin_email' ), $subject, $html, $headers, $attachment );
        wp_mail( $to, $subject, $html, $headers, $attachment );
        wp_redirect( $product->get_permalink() );
        exit;
    }



    public function add_can_customize_product_meta_box(){
        echo '<div class="option_group">';

        woocommerce_wp_checkbox( array(
            'id'            => '_msp_can_customize',
            'wrapper_class' => '',
            'label'         => __('Can this product be customized?', 'msp-sc' ),
        ) );

        echo '</div>';
    }

    public function add_can_customize_product_meta_save( $post_id ){
        $woocommerce_checkbox = isset( $_POST['_msp_can_customize'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_msp_can_customize', $woocommerce_checkbox );
    }

    public static function maybe_add_custom_quote_link(){
        $can_customize = get_post_meta( get_the_ID(), '_msp_can_customize', true );
        $url = site_url() . '/' . self::$endpoint . '?product_id=' . get_the_ID();
        $message = 'Kustom Klever Kutter Branding Sticker RFQ';
        if( $can_customize == 'yes' ){
            echo sprintf( '<a href="%s" style="font-size: 24px; text-decoration: underline;">%s</a>', $url, $message );
        }

    }

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

// add link to end of description tab
add_filter( 'woocommerce_product_tabs', 'msp_custom_description_tab', 98 );
function msp_custom_description_tab( $tabs ){
  $tabs['description']['callback'] = 'msp_add_custom_quote_link_to_description_tab'; // Custom description callback
  return $tabs;
}

function msp_add_custom_quote_link_to_description_tab(){
  woocommerce_product_description_tab();
  MSP_Custom_Quote::maybe_add_custom_quote_link();
}

register_activation_hook( __FILE__, array('MSP_Custom_Quote', 'install') );
add_shortcode( str_replace( '-', '_', MSP_Custom_Quote::$endpoint ), array( 'MSP_Custom_Quote', 'get_custom_quote_form' ) );
