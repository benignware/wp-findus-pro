<?php

global $count;

$count = 0;
// Creating the widget
class FindUsWidget extends WP_Widget {

  function __construct() {
    parent::__construct(
    // Widget Base ID
    'findus',

    // Widget name will appear in UI
    __('Findus Widget', 'findus'),

    // Widget description
    array( 'description' => __( 'Create contact maps easily', 'findus' ), )
    );
  }

  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );
    $content = isset($instance['content']) ? $instance['content'] : 'null';
    $atts = array();
    $props = array('latitude', 'longitude', 'address');
    foreach ($props as $prop) {
      if (isset($instance['latitude'])) {
        $atts[$prop] = $instance[$prop];
      }
    }
    // before and after widget arguments are defined by themes
    echo $args['before_widget'];

    if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];

    // This is where you run the code and display the output
    echo findus_shortcode($atts, nl2br($content));

    echo $args['after_widget'];
  }

  // Widget Backend
  public function form( $instance ) {

    global $count;

    $count++;

    echo "COUNT$count";

    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    } else {
      $title = __( 'New title', 'findus' );
    }

    if ( isset( $instance[ 'content' ] ) ) {
      $content = $instance[ 'content' ];
    } else {
      $content = "";
    }

    if ( isset( $instance[ 'latitude' ] ) ) {
      $latitude = $instance[ 'latitude' ];
    } else {
      $latitude = "";
    }

    if ( isset( $instance[ 'longitude' ] ) ) {
      $longitude = $instance[ 'longitude' ];
    } else {
      $longitude = "";
    }

    if ( isset( $instance[ 'address' ] ) ) {
      $address = $instance[ 'address' ];
    } else {
      $address = "";
    }
    // Widget admin form
  ?>
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
  </p>

    <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label>
    <textarea rows="7" class="widefat findus-textarea" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" type="text"><?php echo esc_attr( $content ); ?></textarea>

    <?php
      $id = $this->get_field_id( 'content' ); //has to be lower case
    ?>
    <script type="text/javascript">
      /*if (typeof tinymce !== 'undefined') {
        var editor_id = '<?= $id; ?>';
        console.log("tinymce: ", tinymce);
        var editor = tinymce.get(editor_id);
        console.log("editor: ", editor_id, editor);
        //tinymce.EditorManager.execCommand('mceRemoveEditor',true, editor_id);
        //tinymce.EditorManager.execCommand('mceAddEditor',true, editor_id);

      }*/

      /*jQuery(function(){
        console.log("dom");
        if (typeof tinymce !== 'undefined') {
          var editor = tinymce.get(editor_id);
          console.log("editor loaded: ", editor);
        }
      });*/
    </script>
    <div class="hidden-editor" style="display: none !important;">
    <?php

      echo $this->get_field_name( 'content' );
      $initial_data = esc_attr($content);
      $settings = array(
        //'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ),
        'textarea_name'=> $this->get_field_name( 'content' ) . "-none", //name you want for the textarea
        //'quicktags' => false,
        //'toolbar1'=> 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo',
       // 'tinymce' => array(
//          'theme_advanced_buttons1' => 'bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink'
       // ),
        'textarea_rows' => 6,
        //'wpautop' => false,
        //'media_buttons' => false,
        //'textarea_name' => 'mythemename_theme_options[sometextarea]', //You can use brackets here !
        //'textarea_rows' => get_option('default_post_edit_rows', 10),
        //'tabindex' => '323',
        //'editor_css' => '',
        //'editor_class' => '',
        //'teeny' => true,
        //'dfw' => false,
        //'tinymce' => true,
        //'quicktags' => true
      );

      //$id = "mylittlecontent";
      //$rand    = rand( 0, 999 );
      //$id   = $this->get_field_id( 'wp_editor_' . $rand );

      //wp_editor($initial_data, $this->get_field_id( 'content' ) . "_", $settings);
      wp_editor('', $this->get_field_id( 'content' ) . "_trigger");
    ?></div>

    <script type="text/javascript">

    jQuery(function ($) {
      var editor_id = '<?= $id; ?>';
        console.log("init: ", editor_id, jQuery('#' + editor_id));

        if (typeof tinymce !== 'undefined') {
          //initialize tinyMCE in page

          tinymce.init({
            selector:'#' + editor_id,
            setup: function (editor) {
              editor.on('change', function () {
                editor.save();
              });
            },
            //mode : "exact",
            //elements : 'pre-details',
            theme: "modern",
            skin: "lightgray",
            menubar : false,
            statusbar : false,
            //block_formats: 'Paragraph=p;Header 1=h1;Header 2=h2;Header 3=h3',
            toolbar: [
              "formatselect | bold italic | alignleft aligncenter alignright | link"
            ],
            plugins : "paste,wplink,wordpress",
            forced_root_block : 'p',
            paste_auto_cleanup_on_paste : true,
            //force_br_newlines : false,
            //force_p_newlines : true,
            //forced_root_blocks: '',
            paste_postprocess : function( pl, o ) {
              o.node.innerHTML = o.node.innerHTML.replace( /&nbsp;+/ig, " " );
            }
          });
        }
    });
    </script>

  <p></p>
  <p>
    <label for="<?php echo $this->get_field_id( 'latitude' ); ?>"><?php _e( 'Latitude:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'latitude' ); ?>" name="<?php echo $this->get_field_name( 'latitude' ); ?>" type="text" value="<?php echo esc_attr( $latitude ); ?>"/>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'longitude' ); ?>"><?php _e( 'Longitude:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'longitude' ); ?>" name="<?php echo $this->get_field_name( 'longitude' ); ?>" type="text" value="<?php echo esc_attr( $longitude ); ?>"/>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'address' ); ?>"><?php _e( 'Address:' ); ?></label>
    <textarea class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" type="text"><?php echo esc_attr( $address ); ?></textarea>
  </p>

  <?php
  }

  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['content'] = ( ! empty( $new_instance['content'] ) ) ? $new_instance['content'] : '';
    $instance['latitude'] = ( ! empty( $new_instance['latitude'] ) ) ? $new_instance['latitude'] : '';
    $instance['longitude'] = ( ! empty( $new_instance['longitude'] ) ) ? $new_instance['longitude'] : '';
    $instance['address'] = ( ! empty( $new_instance['address'] ) ) ? $new_instance['address'] : '';
    return $instance;
  }
} // Class FindUsWidget ends here

// Register and load the widget
function wp_findus_load_widget() {
  register_widget( 'FindUsWidget' );
}
add_action( 'widgets_init', 'wp_findus_load_widget' );

function my_enqueue($hook) {
    wp_enqueue_script('wp-tinymce');
    wp_enqueue_script('wplink');
    //wp_enqueue_style( 'editor-buttons' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );



add_action( 'admin_print_footer_scripts', function () {
    ?>
    <script type="text/javascript">
    jQuery(function ($) {
      return;
      console.log("dom ready", tinymce);

        if (typeof tinymce !== 'undefined') {

          //initialize tinyMCE in page
          tinymce.init({selector:'.findus-textarea'});

          console.log("wait for setup editor", tinymce.EditorManager.editors);
            tinymce.on('SetupEditor', function (editor) {
              console.log("editor: ", editor);
            });
        }
    });
    </script>
    <?php
} );
?>
