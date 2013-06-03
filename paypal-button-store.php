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


    echo 'PayPal Button Code:<br><textarea rows="4" cols="50" id="spbs_paypal_button_code" name="spbs_paypal_button_code">'.$value.'</textarea>';
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
