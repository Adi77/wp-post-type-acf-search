<?php

function get_template_part( $slug, $name = null, $args = array() ) {
  /**
   * Fires before the specified template part file is loaded.
   *
   * The dynamic portion of the hook name, `$slug`, refers to the slug name
   * for the generic template part.
   *
   * @since 3.0.0
   * @since 5.5.0 The `$args` parameter was added.
   *
   * @param string      $slug The slug name for the generic template.
   * @param string|null $name The name of the specialized template.
   * @param array       $args Additional arguments passed to the template.
   */
  do_action( "get_template_part_{$slug}", $slug, $name, $args );

  $templates = array();
  $name      = (string) $name;
  if ( '' !== $name ) {
      $templates[] = "{$slug}-{$name}.php";
  }

  $templates[] = "{$slug}.php";

  /**
   * Fires before a template part is loaded.
   *
   * @since 5.2.0
   * @since 5.5.0 The `$args` parameter was added.
   *
   * @param string   $slug      The slug name for the generic template.
   * @param string   $name      The name of the specialized template.
   * @param string[] $templates Array of template files to search for, in order.
   * @param array    $args      Additional arguments passed to the template.
   */
  do_action( 'get_template_part', $slug, $name, $templates, $args );

  if ( ! locate_template( $templates, true, false, $args ) ) {
      return false;
  }



  $result='';
  $visibility = array();

  foreach ($_GET as $key => $value) { 
    if($key == 'region') {
      //setFilter1($key, $value, get_field('plz_ort'), $visibility);
      if(strpos($value, ',') == true) {
      // allow multiple get parameters separated by comma
      $multiAttrRes = multiAttr($key, $value, get_field('plz_ort'));
      setFilter($multiAttrRes, $visibility);

      } else {
        $filterCheck = mb_strpos(strtolower(get_field('plz_ort')), strtolower($value));
        setFilter($filterCheck, $visibility);
      } 
    }
    if($key == 'hotelklassifikation') {
      if ($value == get_field('hotelklassifikation')) {
        setFilter(true, $visibility);
      } else {
        setFilter(false, $visibility);
      }
    }
    if($key == 'lage') {
      $filterCheck = is_numeric(array_search($value, array_column(get_field('lage'), 'value')));
      setFilter($filterCheck, $visibility);
    }
    if($key == 'hoteltyp') {
      if(strpos($value, ',') == true) {
        // allow multiple get parameters separated by comma
        $multiAttrRes = multiAttr($key, $value, get_field('hoteltyp'));
        setFilter($multiAttrRes, $visibility);
      } else {
        $filterCheck = is_numeric(array_search($value, array_column(get_field('hoteltyp'), 'value')));
        setFilter($filterCheck, $visibility);
      }

    }

  } 
  if (empty($_GET)) {
    $noParam = true;
  }

  if ($noParam || !in_array ( 0, $visibility)) {

    $result .= '<div class="hotel-item col-md-4">';
    $result .= '<div class="hotel-item__location">' . get_field('plz_ort') . '</div>';
    $result .= '<div class="hotel-item__teaser-image">' . get_the_post_thumbnail() . '</div>';
    $result .= '<div class="hotel-item__title">' . get_the_title() . '</div>';
    $result .= '<div class="hotel-item__teaser-text">' . get_field('teaser_text') . '</div>';
    $result .= '<a class="hotel-item__detail-link" href="' . get_post_permalink()  .'">Das Haus entdecken</a>';
    $result .= '</div>';
  }


  return 'huhu';
}







    

