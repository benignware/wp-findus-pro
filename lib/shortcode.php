<?php
function findus_shortcode($atts = array(), $content = "") {
  
  $findus_options = array('content', 'address', 'longitude', 'latitude', 'map', 'marker', 'info');
  $atts = shortcode_atts(array(
    'id' => uniqid(),
    //'class' => '',
    //'address' => '',
    //'content' => '',
    //'latitude' => '',
    //'longitude' => '',
    //'info' => ''
  ), $atts, 'findus');
  
  $atts['class'] = trim('findus ' . (isset($atts['class']) ? $atts['class'] : ''));
  
  $options = array();
  foreach ($atts as $name => $value) {
    if (in_array($name, $findus_options) === true) {
      unset($atts[$name]);
      $options[$name] = $value;
    } 
  }
  $json = json_encode($options, JSON_UNESCAPED_SLASHES);
  
  // Tidy up html
  $content = force_balance_tags($content);
  
  // Remove empty paragraphs and trim content
  $content = trim(preg_replace('~<p>\s*(<\s*br\s*/?\s*>|&nbsp;)*\s*</p>~si', '', $content));
  
  // Strip root node if it's a paragraph
  //$content = preg_replace('~^<p>(.+?(?=</p>))</p>$~si', '$1', $content);
  
  // Create output
  $output = "<div";
  foreach ($atts as $name => $value) {
    $output.= ' ' . $name . '="' . $value . '"';
  }
  $output.= ">";
  $output.= $content;
  $output.= "</div>";
  $output.= "<script type=\"text/javascript\">//<![CDATA[\n(function($, window) {\n";
  $output.= "\t$('#{$atts['id']}').findus(" . $json . ");\n";
  $output.= "})(jQuery, window)\n//]]></script>\n";
  
  return $output;
}
 
add_shortcode('findus', 'findus_shortcode');