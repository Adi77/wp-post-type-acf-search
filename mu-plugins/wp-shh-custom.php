<?php
/**
 * Plugin Name: Custom Plugin for Swiss Historic Hotels
 * Description: Hotel filtered List
 * Author:      Adrian Felder
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Basic security, prevents file from being loaded directly.
defined('ABSPATH') or die('Cheatin&#8217; uh?');




// Register Custom Post Type
function hotels_post_type()
{
    $labels = array(
        'name'                  => _x('Hotels', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Hotel', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Hotels', 'text_domain'),
        'name_admin_bar'        => __('Hotel', 'text_domain'),
        'archives'              => __('Hotel Archiv', 'text_domain'),
        'attributes'            => __('Hotel Attribute', 'text_domain'),
        'parent_item_colon'     => __('Parent Hotel:', 'text_domain'),
        'all_items'             => __('Alle Hotels', 'text_domain'),
        'add_new_item'          => __('Neues Hotel hinzufügen', 'text_domain'),
        'add_new'               => __('Neues Hotel', 'text_domain'),
        'new_item'              => __('Neues Element', 'text_domain'),
        'edit_item'             => __('Hotel bearbeiten', 'text_domain'),
        'update_item'           => __('Hotel aktualisieren', 'text_domain'),
        'view_item'             => __('Hotel anzeigen', 'text_domain'),
        'view_items'            => __('Elemente anzeigen', 'text_domain'),
        'search_items'          => __('Hotels suchen', 'text_domain'),
        'not_found'             => __('Kein Hotel gefunden', 'text_domain'),
        'not_found_in_trash'    => __('Kein Hotel gefunden im Papierkorb', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into item', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
        'items_list'            => __('Items list', 'text_domain'),
        'items_list_navigation' => __('Items list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter items list', 'text_domain'),
    );
    $args = array(
        'label'                 => __('Hotel', 'text_domain'),
        'description'           => __('Hotel information.', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
        'taxonomies'            => array( 'category', 'post_tag', 'location', 'type' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-admin-home',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type('hotel', $args);
}
add_action('init', 'hotels_post_type', 0);





// Create Shortcode Hotels Filter Navigation

function create_shortcode_hotels_filter_navigation()
{
    $result='';
    $lageArr = array();
    $regionArr = array();
    $HotelClassArr = array();
    
    $args = array(
                  'post_type'      => 'hotel',
                  'publish_status' => 'published'
               );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
    $result .= '<div class="container">';
    $result .= '<div class="row">';
    while ($query->have_posts()) :
        $query->the_post() ; ?> 
        <form id="hotelfiltersForm" class="form-inline"> 
    <?php
    // collect filter values of all current items with no duplicates
    // Lage
    foreach (get_field('lage') as $sub) {
        $lageArr[$sub['value']] = $sub['label'];
    }
    // Region
    $regionArr[urlencode(get_field('plz_ort'))] = get_field('plz_ort');

    // Hoteltyp
    foreach (get_field('hoteltyp') as $sub) {
        $hotelTypArr[$sub['value']] = $sub['label'];
    }

    // Hotel Klassifikation
    $HotelClassArr[get_field('hotelklassifikation')] = get_field('hotelklassifikation');
    
    endwhile; ?> 




<div class="form-group mb-2 col-md-12" id="region"> <?php
        foreach ($regionArr as $key => $value) {
            ?> 
            <label class="form-check-label" for="<?php echo $key ?>">
                <?php echo $value ?>
            </label>
                <input class="form-check-input hotel-list_filter" type="checkbox" value="<?php echo $key ?>" id="<?php echo $key ?>" name="hotels-filter-checkbox"> 
                <?php
        } ?> </div> 

    <div class="form-group mb-2 col-md-12" id="lage"> <?php
        foreach ($lageArr as $key => $value) {
            ?> 
            <label class="form-check-label" for="<?php echo $key ?>">
                <?php echo $value ?>
            </label>
                <input class="form-check-input hotel-list_filter" type="checkbox" value="<?php echo $key ?>" id="<?php echo $key ?>" name="hotels-filter-checkbox"> 
                <?php
        } ?> </div> 
        
        <div class="form-group mb-2 col-md-12" id="hoteltyp"> <?php
        foreach ($hotelTypArr as $key => $value) {
            ?> 
            <label class="form-check-label" for="<?php echo $key ?>">
                <?php echo $value ?>
            </label>
                <input class="form-check-input hotel-list_filter" type="checkbox" value="<?php echo $key ?>" id="<?php echo $key ?>" name="hotels-filter-checkbox"> 
                <?php
        } ?> </div>


<div class="form-group mb-2 col-md-12" id="hotelklassifikation"> Sterne:<?php
        foreach ($HotelClassArr as $key => $value) {
            ?> 
            <label class="form-check-label" for="<?php echo $key ?>">
                <?php echo $value ?>
            </label>
                <input class="form-check-input hotel-list_filter" type="checkbox" value="<?php echo $key ?>" id="<?php echo $key ?>" name="hotels-filter-checkbox"> 
                <?php
        } ?> </div>

<button type="submit" class="btn btn-primary">Hotels anzeigen</button></form> 
</div></div>
<?php

    wp_reset_postdata();
    endif;
    return $result;
}
add_shortcode('hotels-filters', 'create_shortcode_hotels_filter_navigation');





function multiAttr($filterKey, $filterValue, $acfFieldValue)
{
    $multiParam = explode(',', $filterValue);
    $hasMatched = 0;
    foreach ($multiParam as &$value) {
        if (filterCheckMethod($filterKey, $value, $acfFieldValue)) {
            $hasMatched = 1;
            break;
        }
    }
    return $hasMatched;
}

function filterCheckMethod($urlGetKey, $urlGetValue, $acfFieldValue)
{
    if ($urlGetKey == 'hoteltyp' || $urlGetKey == 'lage') {
        $result = is_numeric(array_search($urlGetValue, array_column($acfFieldValue, 'value')));
    }
    if ($urlGetKey == 'region' || $urlGetKey == 'hotelklassifikation') {
        $result = strcmp(strtolower($acfFieldValue), strtolower($urlGetValue)) == 0;
    }
    return $result;
}

function setFilter($filterCheck, &$visibility)
{
    if ($filterCheck) {
        array_push($visibility, 1);
    } else {
        array_push($visibility, 0);
    }
}

// shortcode cossde ends here



function filter_hotels()
{
    $filterData = $_POST['filterParams'];

    $args = array(
    'post_type'      => 'hotel',
    'posts_per_page' => '10',
    'publish_status' => 'published'
 );

    $ajaxposts = new WP_Query($args);


    $result = '';

    $result .= '<div class="container">';
    $result .= '<div class="row">';
    if ($ajaxposts->have_posts()) {
        while ($ajaxposts->have_posts()) : $ajaxposts->the_post();

    
        $visibility = array();

        foreach ($filterData as $key => $value) {
            if ($key == 'region') {
                $acfFieldValueEnc = urlencode(get_field('plz_ort'));
                if (strpos($value, ',') == true) {
                    // allow multiple get parameters separated by comma
                    $multiAttrRes = multiAttr($key, $value, $acfFieldValueEnc);
                    setFilter($multiAttrRes, $visibility);
                } else {
                    if (filterCheckMethod($key, strtolower($value), $acfFieldValueEnc)) {
                        $filterCheck = 1;
                    } else {
                        $filterCheck = 0;
                    }
                    setFilter($filterCheck, $visibility);
                }
            }
            if ($key == 'hotelklassifikation') {
                $acfFieldValueEnc = urlencode(get_field('hotelklassifikation'));
                if (strpos($value, ',') == true) {
                    // allow multiple get parameters separated by comma
                    $multiAttrRes = multiAttr($key, $value, $acfFieldValueEnc);
                    setFilter($multiAttrRes, $visibility);
                } else {
                    if (filterCheckMethod($key, strtolower($value), $acfFieldValueEnc)) {
                        $filterCheck = 1;
                    } else {
                        $filterCheck = 0;
                    }
                    setFilter($filterCheck, $visibility);
                }
            }
            if ($key == 'lage') {
                $acfFieldValueEnc = get_field('lage');
                if (strpos($value, ',') == true) {
                    // allow multiple get parameters separated by comma
                    $multiAttrRes = multiAttr($key, $value, $acfFieldValueEnc);
                    setFilter($multiAttrRes, $visibility);
                } else {
                    if (filterCheckMethod($key, strtolower($value), $acfFieldValueEnc)) {
                        $filterCheck = 1;
                    } else {
                        $filterCheck = 0;
                    }
                    setFilter($filterCheck, $visibility);
                }
            }
            if ($key == 'hoteltyp') {
                $acfFieldValueEnc = get_field('hoteltyp');
                if (strpos($value, ',') == true) {
                    // allow multiple get parameters separated by comma
                    $multiAttrRes = multiAttr($key, $value, $acfFieldValueEnc);
                    setFilter($multiAttrRes, $visibility);
                } else {
                    if (filterCheckMethod($key, strtolower($value), $acfFieldValueEnc)) {
                        $filterCheck = 1;
                    } else {
                        $filterCheck = 0;
                    }
                    setFilter($filterCheck, $visibility);
                }
            }
        }

        if (!in_array(0, $visibility)) {
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
    } else {
        $result = 'empty';
    }

    echo $result;
    exit;
}
add_action('wp_ajax_filter_hotels', 'filter_hotels');
add_action('wp_ajax_nopriv_filter_hotels', 'filter_hotels');
