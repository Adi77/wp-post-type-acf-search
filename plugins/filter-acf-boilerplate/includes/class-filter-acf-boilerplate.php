<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       adrianfelder.ch
 * @since      1.0.0
 *
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/includes
 * @author     Adrian Felder <adrianfelder@gmx.ch>
 */
class Filter_Acf_Boilerplate
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Filter_Acf_Boilerplate_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('FILTER_ACF_BOILERPLATE_VERSION')) {
            $this->version = FILTER_ACF_BOILERPLATE_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'filter-acf-boilerplate';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Filter_Acf_Boilerplate_Loader. Orchestrates the hooks of the plugin.
     * - Filter_Acf_Boilerplate_i18n. Defines internationalization functionality.
     * - Filter_Acf_Boilerplate_Admin. Defines all hooks for the admin area.
     * - Filter_Acf_Boilerplate_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filter-acf-boilerplate-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-filter-acf-boilerplate-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-filter-acf-boilerplate-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-filter-acf-boilerplate-public.php';

        $this->loader = new Filter_Acf_Boilerplate_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Filter_Acf_Boilerplate_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Filter_Acf_Boilerplate_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Filter_Acf_Boilerplate_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        $this->loader->add_action('admin_init', $plugin_admin, 'acf_filters_page_init');
        $this->loader->add_action('admin_menu', $plugin_admin, 'acf_filters_add_plugin_page');

        $this->loader->add_action('wp_ajax_acf_filter_list', $plugin_admin, 'acf_filter_list');
        $this->loader->add_action('wp_ajax_nopriv_acf_filter_list', $plugin_admin, 'acf_filter_list');

        $this->loader->add_action('wp_ajax_generated_shortcodes_list', $plugin_admin, 'generated_shortcodes_list');
        $this->loader->add_action('wp_ajax_nopriv_generated_shortcodes_list', $plugin_admin, 'generated_shortcodes_list');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Filter_Acf_Boilerplate_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');


        $this->loader->add_action('wp_ajax_filtered_content_list', $plugin_public, 'filtered_content_list');
        $this->loader->add_action('wp_ajax_nopriv_filtered_content_list', $plugin_public, 'filtered_content_list');

        $this->loader->add_action('wp_ajax_filtered_content_json', $plugin_public, 'filtered_content_json');
        $this->loader->add_action('wp_ajax_nopriv_filtered_content_json', $plugin_public, 'filtered_content_json');
    }

    

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Filter_Acf_Boilerplate_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
