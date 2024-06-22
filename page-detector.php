<?php
/*
Plugin Name: Page Detector
Description: It detects what kind of page you are visiting and the template. Useful for debugging.
Author: Jose Mortellaro
Author URI: https://josemortellaro.com
Domain Path: /languages/
Text Domain: page-detector
Version: 0.0.2
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

add_action( 'template_redirect',function(){
  $log = '';
  if( is_singular() ){
    $log .= __( 'It is singular.','page-detector' ).PHP_EOL;
    global $post;
    if( $post && is_object( $post ) ){
      $log .= sprintf( __( 'ID: %s','page-detector' ),absint( $post->ID ) ).PHP_EOL;
      $log .= sprintf( __( 'Post type: %s','page-detector' ),esc_attr( $post->post_type ) ).PHP_EOL;
    }
    if( is_single() ){
      $log .= __( 'Single post','page-detector' ).PHP_EOL;
    }
    elseif( is_page() ){
      if( is_home() ){
        $log .= __( 'Blog homepage','page-detector' ).PHP_EOL;
      }
      elseif( is_front_page() ){
        $log .= __( 'Front page','page-detector' ).PHP_EOL;
      }
      else{
        $log .= __( 'Static page','page-detector' ).PHP_EOL;
      }
      if( $post && is_object( $post ) && $post->post_parent ){
        $log .= __( 'This is a child page','page-detector' ).PHP_EOL;
        $log .= sprintf( __( 'Parent page ID: %s','page-detector' ),$post->post_parent ).PHP_EOL;
      }
    }
    else{
      if( $post && is_object( $post ) ){
        $log .= sprintf( __( 'Single %s','page-detector' ),$post->post_type ).PHP_EOL;
      }
    }
  }
  elseif( is_archive() ){
    $queried_object = get_queried_object();
    if( $queried_object && is_object( $queried_object ) && isset( $queried_object->name ) ){
      $log .= sprintf( __( 'Queried object: %s','page-detector' ),$queried_object->name ).PHP_EOL;
    }
    $log .= __( 'Archive page ','page-detector' ).PHP_EOL;
  }
  else{
    $log .= __( 'it is neither singular nor an archive page.','page-detector' ).PHP_EOL;
  }
  if( function_exists( 'is_woocommerce' ) && is_woocommerce() ){
    $log .= __( 'WooCommerce page ','page-detector' ).PHP_EOL;
  }
  elseif( function_exists( 'is_cart' ) && is_cart() ){
    $log .= __( 'Cart page','page-detector' ).PHP_EOL;
  }
  elseif( function_exists( 'is_checkout' ) && is_cart() ){
    $log .= __( 'Checkout page','page-detector' ).PHP_EOL;
  }
  if( function_exists( 'is_shop' ) && is_shop() ){
    $log .= __( 'Shop page ','page-detector' ).PHP_EOL;
  }
  global $page_detector_template;
  if( $page_detector_template ){
    $log .= sprintf( __( 'Template: %s','page-detector' ),$page_detector_template ).PHP_EOL;
  }
  $GLOBALS['page_detector_log'] = esc_attr( $log ); //Assign log information to global variable
} );

add_action( 'wp_footer',function(){
  global $page_detector_log,$page_detector_template;
  $log = PHP_EOL.PHP_EOL;
  $log = '*******************'.PHP_EOL;
  $log .= esc_attr__( 'PAGE DETECTOR','page-detector' ).PHP_EOL.PHP_EOL;
  if( $page_detector_log ){
    $log .= $page_detector_log;
  }
  if( $page_detector_template ){
    $log .= sprintf( esc_attr__( 'Template: %s','page-detector' ),$page_detector_template ).PHP_EOL;
  }
  $log .= '*******************'.PHP_EOL.PHP_EOL.PHP_EOL;
  ?>
  <script id=="page-detector-js">console.log('<?php echo esc_js( esc_attr( $log ) ); ?>');</script>
  <?php
} );

add_filter( 'template_include',function( $t ){
  $GLOBALS['page_detector_template'] = esc_html( str_replace( ABSPATH,'',$t ) );
  return $t;
},999 );


add_filter( 'plugin_action_links_'.untrailingslashit( plugin_basename( __FILE__ ) ),function( $links ){
  $settings_link = '<a class="page-detector-setts" href="'.admin_url( 'admin.php?page=page-detector' ).'">' . __( 'How it works','page-detector' ). '</a>';
  array_push( $links, $settings_link );
  return $links;
} );

add_action( 'admin_menu',function(){
  add_menu_page( __( 'Page Detector','page-detector' ),__( 'Page Detector','page-detector' ),'edit_posts','page-detector','eos_page_detector_page_callback','dashicons-code-standards',65 );
} );

function eos_page_detector_page_callback(){
  ?>
  <h1><?php esc_html_e( 'How to check which kind of page you are visiting on the frontend and the template that is used','page-detector' ); ?></h1>
  <ul>
    <li><?php esc_html_e( 'Visit the page','page-detector' ); ?></li>
    <li><?php esc_html_e( 'Right click => Inspect Elements => Console','page-detector' ); ?></li>
    <li><?php esc_html_e( 'See the information provided in the section "Page detector"','page-detector' ); ?></li>
    <li><?php esc_html_e( "That's it. No settings for this plugin.",'page-detector' ); ?></li>
  </ul>
  <div><img style="width:1544px;height:500px;max-width:100%;height:auto" src="https://ps.w.org/page-detector/assets/banner-1544x500.png" /></div>
  <?php
}
