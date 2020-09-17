<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineAjaxController
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
        add_action('wp_ajax_tsm_urls', [$this, 'tsm_urls']);
    }

    public function tsm_urls()
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
