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

#msp_custom_quote_form #ship-to input{
    margin-bottom: 1rem;
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
            <label for="qty">Quantity: ( 100 piece minimum )</label>
            <input id="qty" name="qty" type="number" min="100" class="input-text qty text" style="width: 100px" />
        </div>

        <div class="form-group">
            <label for="image">Logo/Image: ( .ai, .eps, .pdf, .jpg, .png, etc. )</label>
            <input id="image" name="file" type="file"/>
        </div>

        <div class="form-group">
            <label for="tagline">Tagline/Slogan</label>
            <input type="text" id="tagline" name="tagline" />
        </div>

        <div class="form-group">
            <label for="comments">Comments/Questions/Concerns</label>
            <textarea id="comments" name="comments" placeholder="Use this area for whatever you like...">
            </textarea>
        </div>
        
        <h6>Ship To:</h6>
        <div id="ship-to" class="form-group">
            <label>Company</label>
            <input type="text" id="company" name="company" />

            <label>Street</label>
            <input type="text" id="street" name="street" />

            <label>ZIP</label>
            <input type="text" id="zip" name="zip" />

            <label>Email</label>
            <input type="text" id="email" name="email" />

            <label>Phone</label>
            <input type="text" id="phone" name="phone" />
        </div>

        <input type="hidden" name="action" value="msp_process_custom_quote" />
        <input type="hidden" name="product_id" value="<?php echo $_GET['product_id'] ?>" />
        <?php wp_nonce_field( 'process-quote-' . $_GET['product_id'] ); ?>

        <input type="submit" value="Submit RFQ" class="btn btn-success" />
    </form>
<?php

