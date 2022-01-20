<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       adrianfelder.ch
 * @since      1.0.0
 *
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/admin
 * @author     Adrian Felder <adrianfelder@gmx.ch>
 */
class Filter_Acf_Boilerplate_Admin
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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     * @param      array    $wpdb    The version of this plugin.
     *
     */
    public function __construct($plugin_name, $version)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/filter-acf-boilerplate-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/filter-acf-boilerplate-admin.js', array( 'jquery' ), $this->version, false);
    }





    public function acf_filters_add_plugin_page()
    {
        add_menu_page(
            'ACF Filters', // page_title
            'ACF Filters', // menu_title
            'manage_options', // capability
            'filter-acf-boilerplate', // menu_slug
            array( $this, 'acf_filters_create_admin_page' ), // function
            'dashicons-list-view', // icon_url
            99 // position
        );
    }



    public function acf_filters_create_admin_page()
    {
        $this->acf_filters_options = get_option('acf_filters_option_name'); ?>

<div class="wrap">
    <h2>ACF Filters</h2>
    <p>Select Post Type to be filtered and set the ACF Fields for the Filter Types</p>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
                    settings_fields('acf_filters_option_group');
        do_settings_sections('acf-filters-admin');
        submit_button(); ?>
    </form>


    <form id="acfFilters" class="form-inline">
        <hr>
        <h2>Shortcode für Filter Anzeige generieren</h2>

        <?php
        $postTypeNames = $this->load_acf_groups_by_position();
        echo '<select name="post-type-selector" id="post-type-selector" required>';
        echo '<option disabled selected value>ACF Ansicht wählen</option>';
        foreach ($postTypeNames as $ptn) {
            echo  '<option value='.$ptn.'>'. ucfirst($ptn) .'</option>';
        }
        
        echo '</select>';
        echo '<span class="spinner-border" role="status"><span class="sr-only">Loading...</span></span>'; ?>
        <div class="acf-filter-list"></div>

        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                value="Shortcode generieren"></p>



    </form>
    <div class="generated-shortcodes-list"></div>
</div>
<?php
    }


    public function acf_filter_list()
    {
        if (isset($_POST["postType"])) {
            $postType = $_POST['postType'];
        }
        $acfParentGroups = $this->load_acf_groups_by_position($postType);
        $placeholdersForAcfParentIds = implode(', ', array_fill(0, count($acfParentGroups), '%d'));
        $query = "SELECT id, post_title, post_excerpt, post_name, post_parent FROM {$this->db->prefix}posts 
        WHERE 
        post_parent IN ($placeholdersForAcfParentIds) 
        AND post_type = 'acf-field' 
        AND post_status = 'publish'";
        $acfFieldsRes = $this->db->get_results($this->db->prepare($query, array_flip($acfParentGroups)));
        echo '<div id="'. $postType .'" class="postType">';
        foreach ($acfParentGroups as $groupId => $groupName) {
            $i=0;
            foreach ($acfFieldsRes as $row1) {
                if ($groupId == $row1->post_parent && $i==0) {
                    echo '<h2>'. $groupName .'</h2>';
                    $i++;
                }
                if ($groupId == $row1->post_parent) {
                    echo '<input type="checkbox" data-acf-id="'. $row1->id .'" id="'. $row1->post_excerpt .'" name='. urlencode($groupName) .'><label for="'. $row1->post_excerpt .'">'. $row1->post_title .'</label>&nbsp;&nbsp;&nbsp;&nbsp;';
                }
            }
        }
        echo '</div>';
        exit;
    }


    public function generated_shortcodes_list()
    {
        if (isset($_POST["filterIds"])) {
            $filterIds = $_POST['filterIds'];
        }
        if (isset($_POST["postTypeName"])) {
            $postTypeName = $_POST['postTypeName'];
        }
        $shortcode = '[acf-filters postType="'. $postTypeName .'" fields="';

        foreach ($filterIds as $filterId) {
            $shortcode .= $filterId .',';
        }
        $shortcode = rtrim($shortcode, ",");
        $shortcode .= '"]';
   
        echo $shortcode;

        exit;
    }

    

    public function load_acf_groups_by_position($postTypeName = '')
    {
        $acfFieldGroups =  $this->db->get_results("SELECT id, post_title, post_content FROM {$this->db->prefix}posts WHERE post_type = 'acf-field-group' AND post_status = 'publish'");

        $postTypeNames = [];
        $groupIds = [];

        foreach ($acfFieldGroups as $row) {
            $unserialized_pc = unserialize($row->post_content);
            foreach ($unserialized_pc['location'] as $pcIndex) {
                foreach ($pcIndex as $pc) {
                    $postTypeNames[$pc['value']] = $pc['value'];
                    if ($postTypeName == $pc['value']) {
                        $groupIds[$row->id] = $row->post_title;
                    }
                }
            }
        }

        if ($postTypeName) {
            return $groupIds;
        } else {
            return $postTypeNames;
        }
    }

    public function acf_filters_page_init()
    {
        register_setting(
            'acf_filters_option_group', // option_group
            'acf_filters_option_name', // option_name
            array( $this, 'acf_filters_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'acf_filters_setting_section', // id
            'Filter Konfiguration', // title
            array( $this, 'acf_filters_section_info' ), // callback
            'acf-filters-admin' // page
        );

        add_settings_field(
            'post_type_0', // id
            'Post Type', // title
            array( $this, 'post_type_0_callback' ), // callback
            'acf-filters-admin', // page
            'acf_filters_setting_section' // section
        );

        add_settings_field(
            'acf_field_1', // id
            'ACF Fields', // title
            array( $this, 'acf_field_1_callback' ), // callback
            'acf-filters-admin', // page
            'acf_filters_setting_section' // section
        );
    }

    public function acf_filters_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['post_type_0'])) {
            $sanitary_values['post_type_0'] = $input['post_type_0'];
        }

        if (isset($input['acf_field_1'])) {
            $sanitary_values['acf_field_1'] = $input['acf_field_1'];
        }


        return $sanitary_values;
    }

    

    public function acf_filters_section_info()
    {
    }

    public function post_type_0_callback()
    {
        ?> <select name="acf_filters_option_name[post_type_0]" id="post_type_0">
    <?php $selected = (isset($this->acf_filters_options['post_type_0']) && $this->acf_filters_options['post_type_0'] === 'Auswahl') ? 'selected' : '' ; ?>
    <option <?php echo $selected; ?>>Auswahl</option>


    <?php

    $args = array(
            'public'   => true,
            '_builtin' => false
         );
           
        $output = 'names'; // 'names' or 'objects' (default: 'names')
         $operator = 'and'; // 'and' or 'or' (default: 'and')
           
         $post_types = get_post_types($args, $output, $operator);
           
        if ($post_types) { // If there are any custom public post types.

            foreach ($post_types  as $post_type) {
                $selected = (isset($this->acf_filters_options['post_type_0']) && $this->acf_filters_options['post_type_0'] === $post_type) ? 'selected' : '' ; ?>
    <option <?php echo $selected; ?>><?php echo $post_type; ?>
    </option> <?php
            }
        } ?>





</select> <?php
    }

    public function acf_field_1_callback()
    {
        printf(
            '<input type="checkbox" name="acf_filters_option_name[acf_field_1]" id="acf_field_1" value="acf_field_1" %s> <label for="acf_field_1">check ACF Fields that need to be added to filtering</label>',
            (isset($this->acf_filters_options['acf_field_1']) && $this->acf_filters_options['acf_field_1'] === 'acf_field_1') ? 'checked' : ''
        );
    }
}
