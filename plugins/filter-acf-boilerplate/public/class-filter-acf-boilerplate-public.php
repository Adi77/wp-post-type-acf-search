<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       adrianfelder.ch
 * @since      1.0.0
 *
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/public
 * @author     Adrian Felder <adrianfelder@gmx.ch>
 */
class Filter_Acf_Boilerplate_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Filter_Acf_Boilerplate_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Filter_Acf_Boilerplate_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/filter-acf-boilerplate-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Filter_Acf_Boilerplate_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Filter_Acf_Boilerplate_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/filter-acf-boilerplate-public.js', array( 'jquery' ), $this->version, false);
    }




    public static function generate_hotel_filters()
    {
        $html='';
        $locationArr = array();
        $regionArr = array();
        $HotelClassArr = array();
        $allFilterData = array();
        $typeSaveKey = '';
    
        $args = array(
                      'post_type'      => 'hotel',
                      'publish_status' => 'published',
                      'posts_per_page' => -1,
                   );
    
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            $html .= '<div class="container">';
            $html .= '<div class="row">';
            $html .= '<form id="hotelfiltersForm" class="form-inline">';
            while ($query->have_posts()) {
                $query->the_post() ;
                
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
            }
            
            //echo '<pre>' . var_export($allFilterData, true) . '</pre>';
    
            foreach ($allFilterData as $fieldName => $fieldRows) {
                $html .='<div class="form-group mb-2 col-md-12"
                id="'. $fieldName .'">';
                foreach ($fieldRows as $key => $value) {
                    $html .= '<label class="form-check-label"
                    for="'. $key .'">
                    '. $value .'
                </label>';
                    $typeSaveKey = str_replace("%", "", $key);
                    $html .= '<input class="form-check-input hotel-list_filter" type="checkbox"
                    value="'. $key .'" id="'. $key .'" name="hotels-filter-checkbox">';
                }
                $html .= '</div>';
            }
            $html .='<button type="submit" class="btn btn-primary">Hotels anzeigen</button>
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
<div class="container hotel-item-tiles">&nbsp;</div>';

            wp_reset_postdata();
        }
        return $html;
    }
    
    
    
        
    
    
    
    public function filter_hotels_list()
    {
        $filterData = array();
        $paged = 1;
        $itemcount = 0;
    
        if (isset($_POST["paged"])) {
            $paged = $_POST['paged'];
        }
        
        $meta_query = $this->generateMetaQuery($filterData);
    
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
    
    

    public function filter_hotels_data()
    {
        $filterData = array();
        $itemcount = 0;
        $meta_query = $this->generateMetaQuery($filterData);
    
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
    
    
    
    
    public function generateMetaQuery($filterData)
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
}


add_shortcode('hotels-filters', array( 'Filter_Acf_Boilerplate_Public', 'generate_hotel_filters' ));
