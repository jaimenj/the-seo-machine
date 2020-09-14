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

        MySeoMachineDatabase::get_instance();
        MySeoMachineCore::get_instance();
        MySeoMachineBackendController::get_instance();
    }

    public function activation()
    {
        register_setting('msm_options_group', 'msm_db_version');
        register_setting('msm_options_group', 'msm_report_email');

        add_option('msm_db_version', 0);
        add_option('msm_report_email', '');

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
        delete_option('msm_report_email');
    }
}

// Do all..
MySeoMachine::get_instance();
