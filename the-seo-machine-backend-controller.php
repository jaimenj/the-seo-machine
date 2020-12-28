<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineBackendController
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
    }

    public function add_admin_page()
    {
        $page_title = 'The SEO Machine';
        $menu_title = $page_title;
        $capability = 'administrator';
        $menu_slug = 'the-seo-machine';
        $function = [$this, 'tsm_main_admin_controller'];
        $position = null;

        add_management_page($page_title, $menu_title, $capability, $menu_slug, $function, $position);
    }

    public function tsm_main_admin_controller()
    {
        $submitting = false;
        foreach ($_REQUEST as $key => $value) {
            if (preg_match('/submit/', $key)) {
                $submitting = true;
            }
        }

        // Security control
        if ($submitting) {
            if (!isset($_REQUEST['tsm_nonce'])) {
                $tsmSms = '<div id="message" class="notice notice-error is-dismissible"><p>ERROR: nonce field is missing.</p></div>';
            } elseif (!wp_verify_nonce($_REQUEST['tsm_nonce'], 'tsm')) {
                $tsmSms = '<div id="message" class="notice notice-error is-dismissible"><p>ERROR: invalid nonce specified.</p></div>';
            } else {
                /*
                 * Handling actions..
                 */
                if (isset($_REQUEST['tsm-submit'])) {
                    $tsmSms = $this->_save_main_configs();
                } elseif(isset($_REQUEST['tsm-submit-reset-queue'])) {
                    $tsmSms = $this->_reset_queue();
                } elseif(isset($_REQUEST['tsm-submit-remove-all'])) {
                    $tsmSms = $this->_remove_all_data();
                } elseif(isset($_REQUEST['tsm-submit-save-current-columns'])){
                    $tsmSms = $this->_save_current_columns_to_show();
                } else {
                    $tsmSms = '<div id="message" class="notice notice-success is-dismissible"><p>Cannot understand submitting!</p></div>';
                }
            }
        }

        // Main options..
        $quantity_per_batch = get_option('tsm_quantity_per_batch');
        $time_between_batches = get_option('tsm_time_between_batches');
        $current_columns_to_show = get_option('tsm_current_columns_to_show');
        $tsm_db_version = get_option('tsm_db_version');

        include TSM_PATH.'view/main.php';
    }

    private function _save_main_configs()
    {
        update_option('tsm_quantity_per_batch', intval($_REQUEST['quantity_per_batch']));
        update_option('tsm_time_between_batches', intval($_REQUEST['time_between_batches']));

        return '<div id="message" class="notice notice-success is-dismissible"><p>Main options saved!</p></div>';
    }

    private function _reset_queue(){
        global $wpdb;

        $wpdb->get_results('TRUNCATE '.$wpdb->prefix.'the_seo_machine_queue;');

        return '<div id="message" class="notice notice-success is-dismissible"><p>Queue truncated!</p></div>';
    }

    private function _remove_all_data(){
        global $wpdb;
        
        $wpdb->get_results('TRUNCATE '.$wpdb->prefix.'the_seo_machine_queue;');
        $wpdb->get_results('TRUNCATE '.$wpdb->prefix.'the_seo_machine_url_string;');
        $wpdb->get_results('TRUNCATE '.$wpdb->prefix.'the_seo_machine_url_text;');
        $wpdb->get_results('TRUNCATE '.$wpdb->prefix.'the_seo_machine_url_number;');
        $wpdb->get_results('DELETE FROM '.$wpdb->prefix.'the_seo_machine_url_entity;');

        return '<div id="message" class="notice notice-success is-dismissible"><p>Queue and URLs data truncated!</p></div>';
    }

    private function _save_current_columns_to_show() {
        $current_columns_to_show = [];
        
        foreach (TheSeoMachineDatabase::get_instance()->get_eav_attributes() as $key => $value) {
            if($_REQUEST['checkbox_current_columns_to_show_'.$key] == 'on') {
                $current_columns_to_show[] = $key;
            }
        }

        update_option('tsm_current_columns_to_show', implode(',', $current_columns_to_show));

        return '<div id="message" class="notice notice-success is-dismissible"><p>Current columns to show updated!</p></div>';
    }
}
