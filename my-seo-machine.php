<?php
/**
 * Plugin Name: My SEO Machine
 * Plugin URI: https://jnjsite.com/my-seo-machine-for-wordpress/
 * License: GPLv2 or later
 * Description: A SEO machine to study and improve your WordPress website.
 * Version: 0.1
 * Author: Jaime Niñoles
 * Author URI: https://jnjsite.com/.
 */
defined('ABSPATH') or die('No no no');
define('MSM_PATH', plugin_dir_path(__FILE__));

include_once MSM_PATH.'my-seo-machine-database.php';
include_once MSM_PATH.'my-seo-machine-core.php';
include_once MSM_PATH.'my-seo-machine-backend-controller.php';

class MySeoMachine
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

        MySeoMachineDatabase::get_instance();
        MySeoMachineCore::get_instance();
        MySeoMachineBackendController::get_instance();
    }

    public function activation()
    {
        register_setting('msm_options_group', 'msm_db_version');
        register_setting('msm_options_group', 'msm_quantity_per_batch');
        register_setting('msm_options_group', 'msm_time_between_batches');

        add_option('msm_db_version', 0);
        add_option('msm_quantity_per_batch', '2');
        add_option('msm_time_between_batches', '30');

        MySeoMachineDatabase::get_instance()->create_initial_tables();
    }

    public function deactivation()
    {
        MySeoMachineDatabase::get_instance()->remove_tables();

        unregister_setting('msm_options_group', 'msm_db_version');
    }

    public function uninstall()
    {
        delete_option('msm_db_version');
        delete_option('msm_quantity_per_batch');
        delete_option('msm_time_between_batches');
    }

    /**
     * It adds assets only for the backend..
     */
    public function enqueue_admin_css_js($hook)
    {
        wp_enqueue_style('msm_custom_style', plugin_dir_url(__FILE__).'lib/msm.css', false, '0.1');
        wp_enqueue_script('msm_custom_script', plugin_dir_url(__FILE__).'lib/msm.js', [], '0.1');
    }
}

// Do all..
MySeoMachine::get_instance();
