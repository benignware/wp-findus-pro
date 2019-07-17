<?php

// Enqueue Scripts
add_action('wp_enqueue_scripts', function() {
  wp_register_style('twentyseventeen-style', get_template_directory_uri(). '/style.css');
  wp_enqueue_style('twentyseventeen-style', get_template_directory_uri(). '/style.css');
  wp_enqueue_style( 'childtheme-style', get_stylesheet_directory_uri().'/style.css', array('twentyseventeen-style') );
  wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
  wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ), '', true);
  wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array( 'jquery' ), '', true);
  wp_enqueue_script('turbolinks', 'https://cdn.jsdelivr.net/npm/turbolinks@5.2.0/dist/turbolinks.min.js', null, '', false);
});

// Init bootstrap hooks
if (function_exists('wp_bootstrap_hooks')) {
  wp_bootstrap_hooks();
}

// Customize Maps
/*
add_filter('shortcode_atts_findus', function($out, $pairs, $atts, $shortcode) {
  return array_merge($out, array(
    'map' => array(
      'styles' => array (
        0 =>
        array (
          'featureType' => 'all',
          'stylers' =>
          array (
            0 =>
            array (
              'color' => '#C0C0C0',
            ),
          ),
        ),
        1 =>
        array (
          'featureType' => 'road.arterial',
          'elementType' => 'geometry',
          'stylers' =>
          array (
            0 =>
            array (
              'color' => '#CCFFFF',
            ),
          ),
        ),
        2 =>
        array (
          'featureType' => 'landscape',
          'elementType' => 'labels',
          'stylers' =>
          array (
            0 =>
            array (
              'visibility' => 'off',
            ),
          ),
        ),
      )
    )
  ), $atts);
}, 10, 4);
*/

// Setup phpmailer
add_action( 'phpmailer_init', function($phpmailer) {
  $phpmailer->Host = 'mailhog';
  $phpmailer->Port = 1025;
  $phpmailer->IsSMTP();
});
