<?php

function wp_findus_snakeify_keys($array, $arrayHolder = array()) {
  $result = !empty($arrayHolder) ? $arrayHolder : array();

  foreach ($array as $key => $val) {
    $str = $key;
    $str[0] = strtolower($str[0]);
    $func = create_function('$c', 'return "_" . strtolower($c[1]);');
    $newKey = preg_replace_callback('/([A-Z])/', $func, $str);

    if (!is_array($val)) {
      $result[$newKey] = $val;
    } else {
      $result[$newKey] = wp_findus_snakeify_keys($val, $result[$newKey]);
    }
  }
  return $result;
}
