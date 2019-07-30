<?php
/*
Plugin Name: Findus Pro
Plugin URI: https://github.com/benignware/wp-findus-pro
Description: Create contact-maps easily.
Version: 0.1.0-beta.4
Author: Rafael Nowrotek
Author URI: http://benignware.com
Author Email: mail@benignware.com
Text Domain: findus
Domain Path: /lang/
Network: false
License: MIT
License URI: https://opensource.org/licenses/MIT

Copyright 2016 benignware.com
*/

require_once "lib/helpers.php";
require_once "lib/shortcode.php";
require_once "lib/widget.php";
require_once "lib/editor.php";

add_action( 'init', function() {
  $options = get_option('findus_options');

  $api_params = array();
  $api_key = $options['api_key'];

  if ($api_key) {
    $api_params['key'] = urlencode($api_key);
  }
  $api_url = 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's' : '') . '://maps.googleapis.com/maps/api/js' . (count(array_keys($api_params)) > 0 ? '?' . urldecode(http_build_query($api_params)) : '');

  wp_register_script( 'google-maps', $api_url, true);

  wp_register_script('findus-js', plugin_dir_url( __FILE__ ) . 'dist/findus.js', array('google-maps'));
  wp_register_style('findus-css', plugin_dir_url( __FILE__ ) . 'dist/findus.css');
});

add_action('enqueue_block_editor_assets', function() {
  wp_enqueue_script('google-maps');
  wp_enqueue_script('findus-js');
  wp_enqueue_style('findus-css');
});

add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_script('google-maps');
  wp_enqueue_script('findus-js');
  wp_enqueue_style('findus-css');
});



// TODO: Add formats to tinyMCE
/*
// Callback function to insert 'styleselect' into the $buttons array
function my_mce_buttons_2( $buttons ) {
  array_unshift( $buttons, 'styleselect' );
  return $buttons;
}
// Register our callback to the appropriate filter
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );


// Callback function to filter the MCE settings
function my_mce_before_init_insert_formats( $init_array ) {
  // Define the style_formats array
  $style_formats = array(
    // Each array child is a format with it's own settings
    array(
      'title' => 'block',
      'block' => 'div',
      'classes' => 'card card-block',
      'wrapper' => true,

    ),
    array(
      'title' => '⇠.rtl',
      'block' => 'blockquote',
      'classes' => 'rtl',
      'wrapper' => true,
    ),
    array(
      'title' => '.ltr⇢',
      'block' => 'blockquote',
      'classes' => 'ltr',
      'wrapper' => true,
    ),
  );
  // Insert the array, JSON ENCODED, into 'style_formats'
  $init_array['style_formats'] = json_encode( $style_formats );

  return $init_array;

}
// Attach callback to 'tiny_mce_before_init'
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );
*/


// create custom plugin settings menu
add_action('admin_menu', 'findus_create_menu');

function findus_create_menu() {

  //create new top-level menu
  add_options_page('FindUs', 'Findus', 'administrator', __FILE__, 'findus_settings_page' , plugins_url('/images/icon.png', __FILE__) );

  //call register settings function
  add_action( 'admin_init', 'register_findus_settings' );
}

function register_findus_settings() {
  //register our settings
  /*
  register_setting( 'findus-settings-group', 'api_key' );
  register_setting( 'findus-settings-group', 'map_styles' );
  register_setting( 'findus-settings-group', 'marker_icon' );
  */
  register_setting( 'findus-settings-group', 'findus_options', array(
    'default' => array(
      'map' => array(),
      'marker' => array()
    )
  ));
}


function findus_settings_page() {

  // Set variables
  $options = get_option('findus_options');

  // API Key
  $api_key = $options['api_key'];

  // Marker
  $marker_icon = $options['marker']['icon'];
  $default_image = '';

  if ( !empty( $marker_icon ) ) {
    $image_attributes = wp_get_attachment_image_src( $marker_icon );
    $marker_icon_src = $image_attributes[0];
    $marker_icon_value = $marker_icon;
  } else {
    $marker_icon_src = $default_image;
    $marker_icon_value = '';
  }

  // Map
  $map_styles = $options['map']['styles'];
?>
<div class="wrap">
<h2>FindUs</h2>
<p>Create contact-maps easily</p>
<form method="post" action="options.php">
    <?php settings_fields( 'findus-settings-group' ); ?>
    <?php do_settings_sections( 'findus-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
          <th scope="row">Google Maps API Key</th>
          <td><input type="text" name="findus_options[api_key]" value="<?php echo esc_attr( $api_key ); ?>" /></td>
        </tr>

        <tr valign="top">
          <th scope="row">Map Styles</th>
          <td><textarea type="text" name="findus_options[map][styles]"><?php echo esc_attr( $map_styles ); ?></textarea></td>
        </tr>

        <tr valign="top">
          <th scope="row">Marker Icon</th>
          <td>
            <div class="upload">
                <img data-src="<?= $default_image; ?>" src="<?= $marker_icon_src; ?>"/>
                <div>
                    <input type="hidden" name="findus_options[marker][icon]" id="marker_icon" value="<?= $marker_icon_value; ?>" />
                    <button type="button" class="upload_image_button button"><?= __('Upload', 'findus'); ?></button>
                    <button type="button" class="remove_image_button button">&times;</button>
                </div>
            </div>
          </td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>
</div>
<?php
}


/**
 * Load scripts and style sheet for settings page
 */

add_action('admin_enqueue_scripts', function() {
  // WordPress library
  wp_enqueue_media();
  // Settings Script
  wp_enqueue_script('findus-settings', plugin_dir_url( __FILE__ ) . 'assets/settings.js', array( 'jquery' ));
});

/*
add_filter('shortcode_atts_findus', function($out, $pairs, $atts, $shortcode) {
  $marker_icon = get_option('marker_icon');
  $marker_icon_src = $marker_icon ? wp_get_attachment_image_src( $marker_icon )[0] : null;

  return array_merge($out, array(
    'marker' => array_merge($marker_icon_src ? array(
      'icon' => $marker_icon_src
    ) : array(), $out['map'] ?: array()),
    'map' => array_merge(array(
      'styles' => json_decode(get_option('map_styles'))
    ), $out['map'] ?: array())
  ), $atts);
}, 1, 4);
*/
