<?php
/*
Plugin Name: Find Us
Plugin URI: https://github.com/benignware-labs/wp-findus
Description: Create contact-maps easily.
Version: 0.0.1
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
  $api_params = array();
  $api_key = get_option('api_key');
  if ($api_key) {
    $api_params['key'] = urlencode($api_key);
  }
  $api_url = 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's' : '') . '://maps.googleapis.com/maps/api/js' . (count(array_keys($api_params)) > 0 ? '?' . urldecode(http_build_query($api_params)) : '');

  wp_register_script( 'google-maps-api', $api_url);
});

add_action('enqueue_block_editor_assets', function() {
  wp_enqueue_script('google-maps-api');
});

add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_script('google-maps');
  wp_enqueue_script('jquery-findus', plugin_dir_url( __FILE__ ) . 'assets/jquery-findus/dist/js/jquery.findus.js', array( 'jquery', 'google-maps-api' ));
  wp_enqueue_style('jquery-findus', plugin_dir_url( __FILE__ ) . 'assets/jquery-findus/dist/css/jquery.findus.css');
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
  register_setting( 'findus-settings-group', 'api_key' );
}

function findus_settings_page() {
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
          <td><input type="text" name="api_key" value="<?php echo esc_attr( get_option('api_key') ); ?>" /></td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>
</div>
<?php } ?>
