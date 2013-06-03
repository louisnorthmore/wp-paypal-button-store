<?php
/*
Plugin Name: Simple PayPal Button Store
Plugin URI: http://interactivedimension.com
Description: WordPress simple store for PayPal Button items
Version: 1.0
Author: @weareid, @louisnorthmore
Author URI: http://interactivedimension.com
*/

function spbs_core() {


//products post type
    $labels = array(
        'name' => 'Products',
        'singular_name' => 'Product',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Product',
        'edit_item' => 'Edit Product',
        'new_item' => 'New Product',
        'all_items' => 'All Products',
        'view_item' => 'View Product',
        'search_items' => 'Search Products',
        'not_found' =>  'No products found',
        'not_found_in_trash' => 'No products found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Products'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'product' ),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'editor', 'thumbnail' )
    );

    register_post_type( 'spbs-product', $args );


    add_action( 'save_post', 'spbs_save_postdata' );

}
add_action('init', 'spbs_core');

//styles
add_action( 'wp_enqueue_scripts', 'spbs_style' );
function spbs_style() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('/style/style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}

function spbs_product_meta() {
//product meta
    $screens = array( 'spbs-product' );
    foreach ($screens as $screen) {
        add_meta_box(
            'myplugin_sectionid',
            __( 'Product Information', 'myplugin_textdomain' ),
            'spbs_product_meta_box',
            $screen
        );
    }


}
add_action('add_meta_boxes', 'spbs_product_meta');

function spbs_product_meta_box($post) {

    // The actual fields for data entry
    // Use get_post_meta to retrieve an existing value from the database and use the value for the form
    $value = get_post_meta( $post->ID, 'spbs_paypal_button_code', true );


    echo '<h4>PayPal Button Code:</h4>
    <textarea rows="8" cols="140" id="spbs_paypal_button_code" name="spbs_paypal_button_code">'.$value.'</textarea>';


    $shortcode = "[spbs_paypal_button product_id=\"".$post->ID."\"]";
    echo '<h4>Shortcode</h4>
    <pre>'.$shortcode.'</pre>';
}

/* When the post is saved, saves our custom data */
function spbs_save_postdata( $post_id ) {

    // First we need to check if the current user is authorised to do this action.
    if ( 'spbs-product' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

    //if saving in a custom table, get post_ID
    $post_ID = $_POST['post_ID'];
    //sanitize user input
    $mydata = $_POST['spbs_paypal_button_code'];

    // Do something with $mydata
    add_post_meta($post_ID, 'spbs_paypal_button_code', $mydata, true) or
    update_post_meta($post_ID, 'spbs_paypal_button_code', $mydata);


    }
}

function get_paypal_button($product_id) {

    $button = get_post_meta($product_id, 'spbs_paypal_button_code', true);

    return $button;

}

function paypal_button_shortcode( $atts ) {
    extract( shortcode_atts( array(
        'product_id' => '',
    ), $atts ) );

    $button = get_paypal_button($product_id);

    return $button;
}
add_shortcode( 'spbs_paypal_button', 'paypal_button_shortcode' );

function spbs_templates() {
    //products page
    if(is_page('products')) {
        $page_template = dirname( __FILE__ ) . '/templates/products.php';
    }

    return $page_template;
}
add_filter( 'page_template', 'spbs_templates' );

function spbs_list_products() {

    $args = array(
        'numberposts' => -1,
        'post_type' => 'spbs-product',
        'order_by' => 'name',
        'order' => 'ASC'
    );
    $products = get_posts($args);

    foreach($products as $product) { ?>

        <?php //print_r($product) ?>

        <div class="spbs-product">
            <h2 class="entry-title"><?php echo $product->post_title ?></h2>
            <?php echo $product->post_content ?>
            <?php echo get_paypal_button($product->ID) ?>
        </div>

    <?php }

}
