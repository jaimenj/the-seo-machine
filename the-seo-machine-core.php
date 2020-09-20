<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineCore
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
    }

    // TODO
    public function study($current_queue_url)
    {
        global $wpdb;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $current_queue_url->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if it is a redirection..
        if($http_code >= 300 and $http_code <=399){
            $url_redirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

            TheSeoMachineDatabase::get_instance()->save_url_in_queue(
                $url_redirect, 
                ($current_queue_url->level + 1),
                $current_queue_url->url
            );

            
        } else{
            // It's a normal URL, saving..
            $data =[
                'url' => $current_queue_url->url,
                'updated_at' => date('Y-m-d H:i:s'),
                'level' => $current_queue_url->level,
                'http_code' => $http_code
            ];

            TheSeoMachineDatabase::get_instance()->save_url($data);
        }

        curl_close($ch);

        echo 'HTML: '.$html.' HTTP_CODE: '.$http_code.PHP_EOL;
    }
}
