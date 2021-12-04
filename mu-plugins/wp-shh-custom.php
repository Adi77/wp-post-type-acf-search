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
    $allFilterData = array();


    
    $args = array(
                  'post_type'      => 'hotel',
                  'publish_status' => 'published',
                  'posts_per_page' => -1,
               );

    $query = new WP_Query($args);

    if ($query->have_posts()) : ?>
<div class="container">
    <div class="row">
        <?php
    while ($query->have_posts()) :
        $query->the_post() ; ?>

        <form id="hotelfiltersForm" class="form-inline">
            <?php
    // collect filter values of all current items with no duplicates

    // Lage
    foreach (get_field('lage') as $sub) {
        $lageArr[$sub['value']] = $sub['label'];
    }
    $allFilterData['lage'] = $lageArr;

    // plz_ort
    $regionArr[rawurlencode(get_field('plz_ort'))] = get_field('plz_ort');
    $allFilterData['plz_ort'] = $regionArr;

    // Hoteltyp
    foreach (get_field('hoteltyp') as $sub) {
        $hotelTypArr[$sub['value']] = $sub['label'];
    }
    $allFilterData['hoteltyp'] = $hotelTypArr;

    // Hotel Klassifikation
    $HotelClassArr[get_field('hotelklassifikation')] = get_field('hotelklassifikation');
    $allFilterData['hotelklassifikation'] = $HotelClassArr;
    endwhile;
        
    //echo '<pre>' . var_export($allFilterData, true) . '</pre>';

    foreach ($allFilterData as $fieldName => $fieldRows): ?>

            <div class="form-group mb-2 col-md-12"
                id="<?php echo $fieldName; ?>">
                <?php foreach ($fieldRows as $key => $value): ?>
                <label class="form-check-label"
                    for="<?php echo $key ?>">
                    <?php echo $value ?>
                </label>
                <input class="form-check-input hotel-list_filter" type="checkbox"
                    value="<?php echo $key ?>" <?php $typeSaveKey = str_replace("%", "", $key); ?>
                id="<?php echo $typeSaveKey ?>"
                name="hotels-filter-checkbox">
                <?php endforeach; ?>
            </div>

            <?php endforeach; ?>


            <button type="submit" class="btn btn-primary">Hotels anzeigen</button>
            &nbsp;&nbsp;
            <a class="reset-filter" href="#">Filter
                zurücksetzen</a>&nbsp;&nbsp;
            <span class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </span>
        </form>
    </div>
</div>
<?php

    wp_reset_postdata();
    endif;
    return $result;
}
add_shortcode('hotels-filters', 'create_shortcode_hotels_filter_navigation');

// shortcode hotels-filters ends here






function filter_hotels()
{
    $filterData = array();
    $options = array();
    $paged = 1;

    if (isset($_POST["filterParams"])) {
        $filterData = $_POST['filterParams'];
    }

    if (isset($_POST["paged"])) {
        $paged = $_POST['paged'];
    }
    
    //
    // generate meta_query arrays
    //
    foreach ($filterData as $key => $value) {
        $options[$key] = explode(",", $value);
    }
    $meta_query = array(
        'relation' => 'And',
    );

    $i = 0;
    $ii = 0;
    foreach ($options as $key => $value) {
        $meta_query[]['relation'] = 'OR';
        $ii = 0;
        foreach ($value as $key2 => $value2) {
            if ($ii < count($value)) {
                $meta_query[$i][] = array( 'key'=> $key, 'value' => rawurldecode($value2), 'compare' => 'LIKE'  );
            }
            $ii++;
        }
        $i++;
    }

    //echo '<pre>' . print_r($paged, 1) . '</pre>';

    $count = get_option('posts_per_page', 10);
    //$paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $offset = ($paged - 1) * $count;
    $args = array(
    'post_type'      => 'hotel',
    'publish_status' => 'published',
    'posts_per_page' => $count,
    'paged' => $paged,
    'offset' => $offset,
    'meta_query'	=> $meta_query
 );


    $ajaxposts = new WP_Query($args); ?>
<div class="container">
    <div class="row">
        <?php
    if ($ajaxposts->have_posts()) {
        while ($ajaxposts->have_posts()) : $ajaxposts->the_post(); ?>
        <div class="hotel-item col-md-4">
            <div class="hotel-item__location"><?php echo get_field('plz_ort'); ?>
            </div>
            <div class="hotel-item__teaser-image"><?php echo get_the_post_thumbnail(); ?>
            </div>
            <div class="hotel-item__title"><?php echo get_the_title(); ?>
            </div>
            <div class="hotel-item__teaser-text"><?php echo get_field('teaser_text'); ?>
            </div>
            <a class="hotel-item__detail-link"
                href="<?php echo get_post_permalink(); ?>">Das Haus
                entdecken</a>
        </div>

        <?php
        endwhile;

       

        //echo '<pre>' . print_r($args, 1) . '</pre>';

        /*  global $wpdb;
         echo '<pre>';
         print_r($wpdb->queries);
         echo '</pre>'; */

        // echo '<pre>' . print_r($ajaxposts, 1) . '</pre>';

        if ($ajaxposts->max_num_pages > 1):
            
            //previous_posts_link('Zurück static', $ajaxposts->max_num_pages);
        //next_posts_link('Weiter static', $ajaxposts->max_num_pages);
        $pagedNext = 1;
        $pageprevious=1;
        if ($paged == $ajaxposts->max_num_pages) {
            $pageprevious=$paged-1;
            $pagedNext=$ajaxposts->max_num_pages;
        } elseif ($paged <= 1) {
            $pageprevious=1;
            $pagedNext=2;
        } else {
            $pageprevious=$paged-1;
            $pagedNext=$paged+1;
        } ?>
        <a class="pagination" href="#" data-filter-params=<?php echo json_encode($filterData); ?> data-paged="<?php echo  $pageprevious; ?>">Zurück</a>
        <a class="pagination" href="#" data-filter-params=<?php echo json_encode($filterData); ?> data-paged="<?php echo $pagedNext; ?>">Weiter</a>
        <?php endif; ?>
    </div>
</div>
<?php
    } else {
        ?> <span>empty</span>
<?php
    }
    exit;
}

add_action('wp_ajax_filter_hotels', 'filter_hotels');
add_action('wp_ajax_nopriv_filter_hotels', 'filter_hotels');
