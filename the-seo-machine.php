<?php
/**
 * Plugin Name: The SEO Machine
 * Plugin URI: https://jnjsite.com/the-seo-machine-for-wordpress/
 * License: GPLv2 or later
 * Description: A SEO machine to study and improve your WordPress website.
 * Version: 0.1
 * Author: Jaime Niñoles
 * Author URI: https://jnjsite.com/.
 */
defined('ABSPATH') or die('No no no');
define('tsm_PATH', plugin_dir_path(__FILE__));

include_once tsm_PATH.'the-seo-machine-database.php';
include_once tsm_PATH.'the-seo-machine-core.php';
include_once tsm_PATH.'the-seo-machine-backend-controller.php';
include_once tsm_PATH.'the-seo-machine-ajax-controller.php';

class TheSeoMachine
{
    private static $instance;

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        // Activation and deactivation..
        register_activation_hook(__FILE__, [$this, 'activation']);
        register_deactivation_hook(__FILE__, [$this, 'deactivation']);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_css_js']);
        add_action('in_admin_header', [$this, 'add_admin_header_libraries']);

        TheSeoMachineDatabase::get_instance();
        TheSeoMachineCore::get_instance();
        TheSeoMachineBackendController::get_instance();
        TheSeoMachineAjaxController::get_instance();
    }

    public function activation()
    {
        register_setting('tsm_options_group', 'tsm_db_version');
        register_setting('tsm_options_group', 'tsm_quantity_per_batch');
        register_setting('tsm_options_group', 'tsm_time_between_batches');
        register_setting('tsm_options_group', 'tsm_current_columns_to_show');

        add_option('tsm_db_version', 0);
        add_option('tsm_quantity_per_batch', '2');
        add_option('tsm_time_between_batches', '30');
        add_option('tsm_current_columns_to_show', 'id,url,updated_at,level,title,curlinfo_response_code');

        TheSeoMachineDatabase::get_instance()->create_initial_tables();
    }

    public function deactivation()
    {
        TheSeoMachineDatabase::get_instance()->remove_tables();

        unregister_setting('tsm_options_group', 'tsm_db_version');
    }

    public function uninstall()
    {
        delete_option('tsm_db_version');
        delete_option('tsm_quantity_per_batch');
        delete_option('tsm_time_between_batches');
        delete_option('tsm_current_columns_to_show');
    }

    /**
     * It adds assets only for the backend..
     */
    public function enqueue_admin_css_js()
    {
        wp_enqueue_style('tsm_custom_style', plugin_dir_url(__FILE__).'lib/tsm.min.css', false, '0.1.3');
        wp_enqueue_script('tsm_custom_script', plugin_dir_url(__FILE__).'lib/tsm.min.js', [], '0.1.3');
    }

    public function add_admin_header_libraries()
    {
        echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/cr-1.5.2/fc-3.3.1/r-2.2.6/datatables.min.css"/>';
        echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>';
        echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>';
        echo '<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/cr-1.5.2/fc-3.3.1/r-2.2.6/datatables.min.js"></script>';
    }
}

// Do all..
TheSeoMachine::get_instance();
