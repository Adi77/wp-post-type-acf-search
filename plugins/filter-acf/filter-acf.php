<?php
/*
Plugin Name:  Ajax Filter by ACF Fields
Plugin URI:   https://www.adrianfelder.ch
Description:  Filter ACF Fields
Version:      1.0
Author:       Adrian Felder
Author URI:   https://www.adrianfelder.ch
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wpb-tutorial
Domain Path:  /languages
*/
defined('ABSPATH') or die('Cheatin&#8217; uh?');


function generate_hotel_filters()
{
    $result='';
    $locationArr = array();
    $regionArr = array();
    $HotelClassArr = array();
    $allFilterData = array();

    $args = array(
                  'post_type'      => 'hotel',
                  'publish_status' => 'published',
                  'posts_per_page' => -1,
               );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
    ?>
<div class="container">
    <div class="row"><?php
    while ($query->have_posts()) :
        $query->the_post() ; ?>
        <form id="hotelfiltersForm" class="form-inline"><?php
    // collect filter values of all current items with no duplicates

    // Location
    foreach (get_field('location') as $sub) {
        $locationArr[$sub['value']] = $sub['label'];
    }
    $allFilterData['location'] = $locationArr;

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
            </div><?php endforeach; ?><button type="submit"
                class="btn btn-primary">Hotels anzeigen</button>
            &nbsp;&nbsp;
            <a class="reset-filter" href="#">Filter
                zurücksetzen</a>&nbsp;&nbsp;
            <span class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </span>
            <span class="hotel-item-count"></span>
        </form>
    </div>
</div>
<div class="container hotel-item-tiles">&nbsp;</div>
<?php
    wp_reset_postdata();
    endif;
    return $result;
}


$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$is_admin = strpos($request_uri, '/wp-admin/');

if (false === $is_admin) {
    add_shortcode('hotels-filters', 'generate_hotel_filters');
}


function filter_hotels_list()
{
    $filterData = array();
    $paged = 1;
    $itemcount = 0;

    if (isset($_POST["paged"])) {
        $paged = $_POST['paged'];
    }
    
    $meta_query = generateMetaQuery($filterData);

    //echo '<pre>' . print_r($paged, 1) . '</pre>';

    $count = get_option('posts_per_page', 10);
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

        if (isset($_POST["filterParams"])) {
            $filterData = $_POST['filterParams'];
        }


        if ($ajaxposts->max_num_pages > 1 && $paged < $ajaxposts->max_num_pages):
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


    <?php $itemcount = $ajaxposts->found_posts; ?>

    <div class="col-md-12 loadmore">
        <div class="itemcount"><?php echo $itemcount; ?> Hotels
        </div>
        <button class=" btn btn-primary " data-filter-params=<?php echo json_encode($filterData); ?>
            data-paged="<?php echo $pagedNext; ?>">Mehr
            anzeigen</button>
    </div>

    <?php endif; ?>
</div>
<?php
    } else {
        ?> <span>Keine Ergebnisse</span>
<?php
    }
    exit;
}

add_action('wp_ajax_filter_hotels_list', 'filter_hotels_list');
add_action('wp_ajax_nopriv_filter_hotels_list', 'filter_hotels_list');



function filter_hotels_data()
{
    $filterData = array();
    $itemcount = 0;
    $meta_query = generateMetaQuery($filterData);

    // execute db query
    $args = array(
    'post_type'      => 'hotel',
    'publish_status' => 'published',
    'posts_per_page' => -1,
    'meta_query'	=> $meta_query
    );

    $ajaxposts = new WP_Query($args);

    if ($ajaxposts->have_posts()) {
        $itemcount = $ajaxposts->found_posts;
        //echo '<pre>' . print_r($itemcount, 1) . '</pre>';
        echo $itemcount;
    } else {
        echo 0;
    }
    exit;
}

add_action('wp_ajax_filter_hotels_data', 'filter_hotels_data');
add_action('wp_ajax_nopriv_filter_hotels_data', 'filter_hotels_data');


function generateMetaQuery($filterData)
{
    $options = array();

    if (isset($_POST["filterParams"])) {
        $filterData = $_POST['filterParams'];
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

    $filtertypeIterator = 0;
    $optionsIterator = 0;
    foreach ($options as $key => $value) {
        $meta_query[]['relation'] = 'OR';
        $optionsIterator = 0;
        foreach ($value as $key2 => $value2) {
            if ($optionsIterator < count($value)) {
                $meta_query[$filtertypeIterator][] = array( 'key'=> $key, 'value' => rawurldecode($value2), 'compare' => 'LIKE'  );
            }
            $optionsIterator++;
        }
        $filtertypeIterator++;
    }
    return $meta_query;
}





add_action('wp_enqueue_scripts', 'filter_acf_js_load');
function filter_acf_js_load()
{
    wp_enqueue_script('filter-acf-main', plugin_dir_url(__FILE__) . 'assets/js/filter-acf-main.js', array( 'jquery' ));
}
