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
        'supports' => array( 'title', 'editor', 'author', 'thumbnail' )
    );

    register_post_type( 'spbs-product', $args );

}
add_action('init', 'spbs_core');
