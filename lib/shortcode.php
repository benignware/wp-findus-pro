<?php
function findus_shortcode($params = array(), $content = "") {

  $html_atts = array(
    'id',
    'class'
  );

  $atts = shortcode_atts(array_merge(
    array(
      'id' => uniqid(),
      'class' => '',
      'address' => '',
      'content' => '',
      'latitude' => '',
      'longitude' => '',
      'info' => ''
    ),
    get_option('findus_options')
  ), $params, 'findus');

  $atts['class'] = trim('findus ' . (isset($atts['class']) ? $atts['class'] : ''));

  $options = array();
  foreach ($params as $name => $value) {
    if (!in_array($name, $html_atts)) {
      $options[$name] = $value;
    }
  }

  if ($options['marker'] && $options['marker']['icon'] && is_numeric($options['marker']['icon'])) {
    $options['marker']['icon'] = wp_get_attachment_image_src($options['marker']['icon'])[0] ?: null;
  }

  if ($options['map'] && $options['map']['styles'] && is_string($options['map']['styles'])) {
    $options['map']['styles'] = json_decode($options['map']['styles']) ?: $options['map']['styles'];
  }

  $json = json_encode(wp_findus_camelize_keys($options), JSON_UNESCAPED_SLASHES);

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
  $output.= "<script type=\"text/javascript\">//<![CDATA[\n(function(window) {\n";
  // $output.= "\t$('#{$atts['id']}').findus(" . $json . ");\n";
  $output.= "\tfindus(document.querySelector('#{$atts['id']}'), " . $json . ");\n";
  $output.= "})(window)\n//]]></script>\n";

  return $output;
}

add_shortcode('findus', 'findus_shortcode');
