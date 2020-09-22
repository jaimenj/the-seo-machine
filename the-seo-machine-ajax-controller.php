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

        // Main query..
        $sql = "SELECT ue.id id, 
        ue.url url, 
        ulevel.value level,
        utitle.value as title,
        ucurlinfo_response_code.value curlinfo_response_code
        FROM wpjnj_the_seo_machine_url_entity ue 
        LEFT JOIN wpjnj_the_seo_machine_url_number ulevel ON ulevel.id_url = ue.id AND ulevel.code = 'level'
        LEFT JOIN wpjnj_the_seo_machine_url_string utitle ON utitle.id_url = ue.id AND utitle.code = 'title'
        LEFT JOIN wpjnj_the_seo_machine_url_number ucurlinfo_response_code ON ucurlinfo_response_code.id_url = ue.id AND ucurlinfo_response_code.code = 'curlinfo_response_code'";

        // Where filtering..
        $where_clauses_or = [];
        $where_clauses_and = [];
        // ..main search..
        if (!empty($_POST['search']['value'])) {
            foreach ($_POST['columns'] as $column) {
                if ('id' == $column['name']) {
                    $where_clauses_or[] = 'ue.id = '.floatval($_POST['search']['value']);
                } elseif ('url' == $column['name']) {
                    $where_clauses_or[] = "ue.url LIKE '%".sanitize_text_field($_POST['search']['value'])."%'";
                } elseif ('number' == TheSeoMachineDatabase::get_instance()->get_eav_attributes()[$column['name']]) {
                    if (is_numeric($_POST['search']['value'])) {
                        $where_clauses_or[] = 'u'.sanitize_text_field($column['name']).'.value = '.floatval($_POST['search']['value']);
                    }
                } else {
                    $where_clauses_or[] = 'u'.sanitize_text_field($column['name']).".value LIKE '%".sanitize_text_field($_POST['search']['value'])."%'";
                }
            }
        }
        // ..column search..
        foreach ($_POST['columns'] as $column) {
            if (!empty($column['search']['value'])) {
                if ('id' == $column['name']) {
                    $where_clauses_and[] = 'ue.id = '.floatval($column['search']['value']);
                } elseif ('url' == $column['name']) {
                    $where_clauses_and[] = "ue.url LIKE '%".sanitize_text_field($column['search']['value'])."%'";
                } elseif ('number' == TheSeoMachineDatabase::get_instance()->get_eav_attributes()[$column['name']]) {
                    if (is_numeric($column['search']['value'])) {
                        $where_clauses_and[] = 'u'.sanitize_text_field($column['name']).'.value = '.floatval($column['search']['value']);
                    }
                } else {
                    $where_clauses_and[] = 'u'.sanitize_text_field($column['name']).".value LIKE '%".sanitize_text_field($column['search']['value'])."%'";
                }
            }
        }

        // Main results..
        $where_filtered = implode(' AND ', $where_clauses_and);
        if (empty($where_filtered)) {
            if (!empty($where_clauses_or)) {
                $where_filtered = implode(' OR ', $where_clauses_or);
            }
        } else {
            if (!empty($where_clauses_or)) {
                $where_filtered .= ' AND ('.implode(' OR ', $where_clauses_or).')';
            }
        }
        if (!empty($where_filtered)) {
            $sql .= 'WHERE '.$where_filtered;
        }
        $sql .= ' LIMIT '.intval($_POST['length']).' OFFSET '.intval($_POST['start']);
        $results = $wpdb->get_results($sql);

        // Totals..
        $sql_total = 'SELECT count(*) FROM '.$wpdb->prefix.'the_seo_machine_url_entity ';
        $records_total = $wpdb->get_var($sql_total);
        $records_total_filtered = $wpdb->get_var(
            $sql_total.(!empty($where_filtered) ? ' WHERE '.$where_filtered : '')
        );

        // Return data..
        $data = [];
        foreach ($results as $key => $value) {
            //var_dump($value);
            $data[] = [
                $value->id,
                $value->url,
                $value->level,
                $value->title,
                $value->curlinfo_response_code,
            ];
        }
        header('Content-type: application/json');
        echo json_encode([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_total_filtered,
            'data' => $data,
            'sql' => $sql,
            'whereFiltered' => $where_filtered,
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
