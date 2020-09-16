<?php

defined('ABSPATH') or die('No no no');

class MySeoMachineAjaxController
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
        add_action('wp_ajax_msm_urls', [$this, 'msm_urls']);
    }

    public function msm_urls()
    {
        if (!current_user_can('administrator')) {
            wp_die(__('Sorry, you are not allowed to manage options for this site.'));
        }

        // Request data to the DB..
        global $wpdb;

        $sql = 'SELECT ..';
        $results = $wpdb->get_results($sql);

        // Return data..

        wp_die();
    }

   
}
