<?php


// >> Create Shortcode to Display Hotels Post Types
  
/* function create_shortcode_hotels_post_type()
{
    $result='';
    $noParam=false;

    $args = array(
                  'post_type'      => 'hotel',
                  'posts_per_page' => '10',
                  'publish_status' => 'published'
               );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
    $result .= '<div class="container">';
    $result .= '<div class="row">';
    while ($query->have_posts()) :

        $query->the_post() ;
    $visibility = array();

    foreach ($_GET as $key => $value) {
        if ($key == 'region') {
            //setFilter1($key, $value, get_field('plz_ort'), $visibility);
            if (strpos($value, ',') == true) {
                // allow multiple get parameters separated by comma
                $multiAttrRes = multiAttr($key, $value, get_field('plz_ort'));
                setFilter($multiAttrRes, $visibility);
            } else {
                $filterCheck = mb_strpos(strtolower(get_field('plz_ort')), strtolower($value));
                setFilter($filterCheck, $visibility);
            }
        }
        if ($key == 'hotelklassifikation') {
            if ($value == get_field('hotelklassifikation')) {
                setFilter(true, $visibility);
            } else {
                setFilter(false, $visibility);
            }
        }
        if ($key == 'lage') {
            echo $value;
            $filterCheck = is_numeric(array_search($value, array_column(get_field('lage'), 'value')));
            setFilter($filterCheck, $visibility);
        }
        if ($key == 'hoteltyp') {
            if (strpos($value, ',') == true) {
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

    if ($noParam || !in_array(0, $visibility)) {
        $result .= '<div class="hotel-item col-md-4">';
        $result .= '<div class="hotel-item__location">' . get_field('plz_ort') . '</div>';
        $result .= '<div class="hotel-item__teaser-image">' . get_the_post_thumbnail() . '</div>';
        $result .= '<div class="hotel-item__title">' . get_the_title() . '</div>';
        $result .= '<div class="hotel-item__teaser-text">' . get_field('teaser_text') . '</div>';
        $result .= '<a class="hotel-item__detail-link" href="' . get_post_permalink()  .'">Das Haus entdecken</a>';
        $result .= '</div>';
    }

    endwhile;
    $result .= '</div></div>';
    wp_reset_postdata();
    endif;
    return $result;
}
add_shortcode('hotels-list', 'create_shortcode_hotels_post_type'); */


/* function setFilter1($getParamKey, $getParamValue, $acfFieldValue, $visibility) {

  if(strpos($getParamValue, ',') == true) {
    // allow multiple get parameters separated by comma
    $multiAttrRes = multiAttr1($getParamKey, $getParamValue, $acfFieldValue);
    setFilter2($multiAttrRes, $visibility);

    } else {
      $filterCheckMethod1 = filterCheckMethod($getParamKey, $getParamValue, $acfFieldValue);

      var_dump($filterCheckMethod1);

      setFilter2($filterCheckMethod1, $visibility);
    }
return $visibility;
}
function multiAttr1($filterKey, $filterValue, $acfFieldValue) {
  $multiParam = explode( ',', $filterValue );
  $hasMatched = 0;
  foreach ($multiParam as &$value) {
    if(filterCheckMethod($filterKey, $value, $acfFieldValue)  ) {
      $hasMatched = 1;
      break;
    }
  }
return $hasMatched;
}
function setFilter2($filterCheck, &$visibility) {
  if($filterCheck) {
    array_push($visibility, 1);
  } else {
    array_push($visibility, 0);
  }
} */
