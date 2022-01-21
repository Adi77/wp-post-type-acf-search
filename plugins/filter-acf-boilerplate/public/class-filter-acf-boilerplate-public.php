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



    public static function generate_filter_form_fields($attr)
    {
        $html='';
        $allFilterData = array();
        $typeSaveKey = '';

        $scArgs = shortcode_atts(array(
            'posttype' => '',
            'fields' => ''

        ), $attr);
    
        $args = array(
                      'post_type'      => $scArgs['posttype'],
                      'post_status' => 'publish',
                      'posts_per_page' => -1,
                      'no_found_rows' => true, // counts posts, remove if you need pagination
                      'update_post_term_cache' => false, // queries terms, remove if you need categories or tags
                      'update_post_meta_cache' => false, // queries post meta, remove if you need post meta
                   );
    
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $allFilterData = Filter_Acf_Boilerplate_Public::collect_filter_data($query, $scArgs['fields']);


            $html .= '<div class="container">';
            $html .= '<div class="row">';
            $html .= '<form id="hotelfiltersForm" class="form-inline">';

            $html .= '<input type="hidden" id="postType" name="postType" value="'. $scArgs['posttype'] .'">';
            $html .= '<input type="hidden" id="acfFieldIds" name="acfFieldIds" value="'. $scArgs['fields'] .'">';


            foreach ($allFilterData as $fieldName => $fieldRows) {
                $html .='<div class="form-group mb-2 col-md-12" id="'. $fieldName .'">';
                foreach ($fieldRows as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        if ($key2 == 'filterLabel') {
                            $html .= '<label class="form-check-label" for="'. $key .'"> '. $value2;
                        }
                        if ($key2 == 'filterCount') {
                            $html .= '<span class="count">'. $value2 .'</span></label>';
                        }
                    }
                    
                    $typeSaveKey = str_replace("%", "", $key);
                    $html .= '<input class="form-check-input hotel-list_filter" type="checkbox" value="'. $key .'" id="'. $typeSaveKey .'" name="hotels-filter-checkbox">';
                }
                $html .= '</div>';
            }
            $html .='<button type="submit" class="btn btn-primary">Hotels anzeigen</button>&nbsp;&nbsp;';
            $html .= '<a class="reset-filter" href="#">Filter zurücksetzen</a>&nbsp;&nbsp;';
            $html .= '<span class="spinner-border" role="status"><span class="sr-only">Loading...</span></span>';
            $html .='<span class="hotel-item-count"></span>';
            $html .= '</form></div></div>';
            $html .= '<div class="container hotel-item-tiles">&nbsp;</div>';

            wp_reset_postdata();
        }
        return $html;
    }
    
    //
    // collect filter values of all current items with no duplicates
    //

    public static function collect_filter_data($query, $acfFieldIds)
    {
        $acfFieldIdsArr = explode(",", $acfFieldIds);
        
        $fieldArr = array();
        $count_values = array();
        $acfFieldVal = '';

        while ($query->have_posts()) {
            $query->the_post() ;
      
            foreach ($acfFieldIdsArr as $acfFieldId) {
                $acfFieldVal = get_field($acfFieldId);
                if (gettype($acfFieldVal) == 'string') {
                    // count items with same filter value
                    if (!isset($count_values[$acfFieldVal])) {
                        $count_values[$acfFieldVal] = 0;
                    }
                    $count_values[$acfFieldVal]++;
                    $fieldArr[$acfFieldId][rawurlencode($acfFieldVal)]['filterValue'] = rawurlencode($acfFieldVal);
                    $fieldArr[$acfFieldId][rawurlencode($acfFieldVal)]['filterLabel'] = $acfFieldVal;
                    $fieldArr[$acfFieldId][rawurlencode($acfFieldVal)]['filterCount'] = $count_values[$acfFieldVal];
                } else {
                    foreach ($acfFieldVal as $acfFieldIdItem) {
                        // count items with same filter value
                        if (!isset($count_values[$acfFieldIdItem['value']])) {
                            $count_values[$acfFieldIdItem['value']] = 0;
                        }
                        $count_values[$acfFieldIdItem['value']]++;
                        $fieldArr[$acfFieldId][$acfFieldIdItem['value']]['filterValue'] = $acfFieldIdItem['value'];
                        $fieldArr[$acfFieldId][$acfFieldIdItem['value']]['filterLabel'] = $acfFieldIdItem['label'];
                        $fieldArr[$acfFieldId][$acfFieldIdItem['value']]['filterCount'] = $count_values[$acfFieldIdItem['value']];
                    }
                }
            }
        }

        return $fieldArr;
    }
    
    public function filtered_content_list()
    {
        $filterData = array();
        $paged = 1;
        $itemcount = 0;
        $shortcodeAttrPostType = '';
    
        if (isset($_POST["paged"]) && $_POST["paged"] != null) {
            $paged = $_POST['paged'];
        }
        if (isset($_POST["shortcodeAttrPostType"])) {
            $shortcodeAttrPostType = $_POST['shortcodeAttrPostType'];
        }
        
        
        $meta_query = $this->generateMetaQuery($filterData);

        $count = get_option('posts_per_page', 10);
        $offset = ($paged - 1) * $count;
        $args = array(
        'post_type'      => $shortcodeAttrPostType,
        'post_status' => 'publish',
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
    <div class="col-md-12 itemcount">
        <span class="total-hotels-count"><?php echo $itemcount; ?></span> Hotels
    </div>
    <div class="col-md-12 loadmore">

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
    
    

    public function filtered_content_preview()
    {
        $allFilterData = array();
        $itemcount = 0;
        $meta_query = $this->generateMetaQuery();
    
   
        if (isset($_POST["shortcodeAttrPostType"])) {
            $shortcodeAttrPostType = $_POST['shortcodeAttrPostType'];
        }

     
        // execute db query
        $args = array(
        'post_type'      => $shortcodeAttrPostType,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query'	=> $meta_query,
        //'no_found_rows' => true, // counts posts, remove if you need pagination
        'update_post_term_cache' => false, // queries terms, remove if you need categories or tags
        'update_post_meta_cache' => false, // queries post meta, remove if you need post meta
        );
    
        $ajaxposts = new WP_Query($args);

        $itemcount = $ajaxposts->found_posts;
        

        //
        // collect filter data from preview selected posts
        //


        if (isset($_POST["shortcodeAttrAcfFieldIds"])) {
            $shortcodeAttrAcfFieldIds = $_POST['shortcodeAttrAcfFieldIds'];
        }

        $allFilterData = Filter_Acf_Boilerplate_Public::collect_filter_data($ajaxposts, $shortcodeAttrAcfFieldIds);

        $allFilterData['itemcount'] = $itemcount;


        echo json_encode($allFilterData);

        exit;
    }
    
    
    
    
    public function generateMetaQuery($filterData = array())
    {
        $options = array();
    
        if (isset($_POST["filterParams"]) && $_POST["filterParams"] != null) {
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


//add_shortcode('hotels-filters', array( 'Filter_Acf_Boilerplate_Public', 'generate_filter_form_fields' ));


add_shortcode('acf-filters', array( 'Filter_Acf_Boilerplate_Public', 'generate_filter_form_fields' ));

// echo '<pre>' . print_r($itemcount, 1) . '</pre>';
