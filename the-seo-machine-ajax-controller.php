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
        add_action('wp_ajax_tsm_get_status', [$this, 'tsm_get_status']);
        add_action('wp_ajax_tsm_do_batch', [$this, 'tsm_do_batch']);
    }

    // TODO
    public function tsm_urls()
    {
        if (!current_user_can('administrator')) {
            wp_die(__('Sorry, you are not allowed to manage options for this site.'));
        }

        // Request data to the DB..
        global $wpdb;

        $sql = 'SELECT * FROM '.$wpdb->prefix.'the_seo_machine_url_entity ';
        $results = $wpdb->get_results($sql);

        // Return data..
        $data = [];
        foreach ($results as $key => $value) {
            $data[] = [
                $value->id, 
                $value->url
            ];
        }
        header('Content-type: application/json');
        echo json_encode([
            'draw' => $_GET['draw'],
            'recordsTotal' => 211,
            'recordsFiltered' => 211,
            'data' => $data
        ]);

        wp_die();
    }

    public function tsm_get_status()
    {
        if (!current_user_can('administrator')) {
            wp_die(__('Sorry, you are not allowed to manage options for this site.'));
        }

        global $wpdb;
        $status = '';

        // Count items..
        $num_urls_in_queue = $wpdb->get_var(
            'SELECT count(*) FROM '.$wpdb->prefix.'the_seo_machine_queue;');
        $num_urls_in_queue_not_visited = $wpdb->get_var(
            'SELECT count(*) FROM '.$wpdb->prefix.'the_seo_machine_queue '
            .'WHERE visited <> true;');

        // Check status..
        if ($num_urls_in_queue > 0) {
            if ($num_urls_in_queue_not_visited > 0) {
                $status = 'processing';
            } else {
                $status = 'finished';
            }
        } else {
            $status = 'empty';
        }

        // Return data..
        echo $status;

        wp_die();
    }

    public function tsm_do_batch()
    {
        if (!current_user_can('administrator')) {
            wp_die(__('Sorry, you are not allowed to manage options for this site.'));
        }

        global $wpdb;
        $status = '';
        $quantity_per_batch = get_option('tsm_quantity_per_batch');

        $num_urls_in_queue = $wpdb->get_var(
            'SELECT count(*) FROM '.$wpdb->prefix.'the_seo_machine_queue;');
        $num_urls_in_queue_not_visited = $wpdb->get_var(
            'SELECT count(*) FROM '.$wpdb->prefix.'the_seo_machine_queue '
            .'WHERE visited <> true;');
        $num_urls = $wpdb->get_var(
            'SELECT count(*) FROM '.$wpdb->prefix.'the_seo_machine_url_entity;');

        // If starting study..
        if (0 == $num_urls_in_queue) {
            TheSeoMachineDatabase::get_instance()->save_url_in_queue(
                '/' == substr(get_site_url(), -1) ? get_site_url() : get_site_url().'/',
                0,
                'ENTRY_POINT'
            );

            $status = 'processing';
        } elseif ($num_urls_in_queue_not_visited > 0) {
            $next_queue_urls = $wpdb->get_results(
                'SELECT * FROM '.$wpdb->prefix.'the_seo_machine_queue '
                .'WHERE visited <> true '
                .'ORDER BY id ASC '
                .'LIMIT '.$quantity_per_batch.';'
            );

            foreach ($next_queue_urls as $next_queue_url) {
                TheSeoMachineCore::get_instance()->study($next_queue_url);

                $wpdb->get_results(
                    'UPDATE '.$wpdb->prefix.'the_seo_machine_queue '
                    .'SET visited = true WHERE id = '.$next_queue_url->id
                );
            }
            $status = 'processing';
        } else {
            $status = 'finished';
        }
        $status .= ' '.$num_urls_in_queue.' URLs in queue, '
            .$num_urls_in_queue_not_visited.' not visited, '
            .$num_urls.' URLs studied..';

        // Return data..
        echo $status;

        wp_die();
    }
}
