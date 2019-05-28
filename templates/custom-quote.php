<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$product = wc_get_product( $_GET['product_id'] );

if( empty( $product ) ) return;

?>
<style>
#msp_custom_quote_form_preview img{
    width: auto;
}
</style>

    <div id="msp_custom_quote_form_preview" class="border-bottom">
        <div class="d-flex align-items-end">
            <?php echo $product->get_image( 'woocommerce_gallery_thumbnail' ) ?>
            <h5 class="m-0 p-0">
                <?php echo $product->get_name() ?>
            </h5>
        </div>
    </div>

    <form id="msp_custom_quote_form" method="POST" action="<?php echo admin_url( 'admin-post.php' ) ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="qty">Quantity: </label>
            <input id="qty" name="qty" type="number" class="input-text qty text" style="width: 100px" />
        </div>

        <div class="form-group">
            <label for="image">Image: </label>
            <input id="image" name="file" type="file"/>
        </div>
        
        <h6>Ship To:</h6>
        <div class="form-group">
            <label>Email</label>
            <input type="text" id="email" name="email" />
            <label>Street</label>
            <input type="text" id="street" name="street" />
            <label>ZIP</label>
            <input type="text" id="zip" name="zip" />
        </div>

        <input type="hidden" name="action" value="msp_process_custom_quote" />
        <input type="hidden" name="product_id" value="<?php echo $_GET['product_id'] ?>" />
        <?php wp_nonce_field( 'process-quote-' . $_GET['product_id'] ); ?>

        <input type="submit" value="Submit RFQ" />
    </form>
<?php

