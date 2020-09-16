<?php

defined('ABSPATH') or die('No no no');

class MySeoMachineBackendController
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
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('init', [$this, 'wgo_download_current_regexes_controller']);
    }

    public function add_admin_page()
    {
        $page_title = 'My SEO Machine';
        $menu_title = $page_title;
        $capability = 'administrator';
        $menu_slug = 'my-seo-machine';
        $function = [$this, 'msm_main_admin_controller'];
        $icon_url = 'dashicons-performance';
        $position = null;

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
    }

    public function msm_main_admin_controller()
    {
        global $current_page;

        if (isset($_REQUEST['current-page'])) {
            $current_page = intval($_REQUEST['current-page']);
        } else {
            $current_page = 1;
        }
        //var_dump($current_page);

        $submitting = false;
        foreach ($_REQUEST as $key => $value) {
            if (preg_match('/submit/', $key)) {
                $submitting = true;
            }
        }

        // Security control
        if ($submitting) {
            if (!isset($_REQUEST['msm_nonce'])) {
                $msmSms = '<div id="message" class="notice notice-error is-dismissible"><p>ERROR: nonce field is missing.</p></div>';
            } elseif (!wp_verify_nonce($_REQUEST['msm_nonce'], 'msm')) {
                $msmSms = '<div id="message" class="notice notice-error is-dismissible"><p>ERROR: invalid nonce specified.</p></div>';
            } else {
                /*
                 * Handling actions..
                 */
                if (isset($_REQUEST['btn-submit'])) {
                    $msmSms = $this->_save_main_configs();
                } else {
                    $msmSms = '<div id="message" class="notice notice-success is-dismissible"><p>Cannot understand submitting!</p></div>';
                }
            }
        }

        // Main options..
        $quantity_per_batch = get_option('msm_quantity_per_batch');
        $time_between_batches = get_option('msm_time_between_batches');

        include MSM_PATH.'view/main.php';
    }

    private function _save_main_configs()
    {
        update_option('msm_quantity_per_batch', intval($_REQUEST['quantity_per_batch']));
        update_option('msm_time_between_batches', intval($_REQUEST['time_between_batches']));

        return '<div id="message" class="notice notice-success is-dismissible"><p>Main options saved!</p></div>';
    }
}
