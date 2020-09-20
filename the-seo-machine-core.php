<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineCore
{
    private static $instance;
    private $dom;

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->dom = new \DOMDocument();
    }

    // TODO
    public function study($current_url)
    {
        global $wpdb;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $current_url->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Check if it is a redirection..
        if ($http_code >= 300 and $http_code <= 399) {
            $url_redirect = curl_getinfo($curl, CURLINFO_REDIRECT_URL);

            TheSeoMachineDatabase::get_instance()->save_url_in_queue(
                $url_redirect,
                ($current_url->level + 1),
                $current_url->url
            );
        } else {
            // It's a normal URL, saving..

            $data = [
                'url' => $current_url->url,
                'updated_at' => date('Y-m-d H:i:s'),
                'level' => $current_url->level,
            ];

            $this->_prepare_url_insights_data($curl, $data);
            $this->_prepare_url_technics_data($curl, $data);

            TheSeoMachineDatabase::get_instance()->save_url($data);
        }

        curl_close($curl);
    }

    private function _prepare_url_insights_data($curl, &$data)
    {
        $dom = $this->dom;
        @$dom->loadHTML($curl->response);

        $data['title'] = '';
        $data['meta_charset'] = '';
        $data['meta_description'] = '';
        $data['meta_keywords'] = '';
        $data['meta_author'] = '';
        $data['meta_viewport'] = '';
        if ($dom->getElementsByTagName('title')->length > 0) {
            $data['title'] = $dom->getElementsByTagName('title')[0]->textContent;
        }
        foreach ($dom->getElementsByTagName('meta') as $metaNode) {
            if ($metaNode->hasAttribute('charset')) {
                $data['meta_charset'] = $metaNode->getAttribute('charset');
            }
            switch ($metaNode->getAttribute('name')) {
                case 'description':
                    $data['meta_description'] = utf8_encode($metaNode->getAttribute('content'));
                    break;
                case 'keywords':
                    $data['meta_keywords'] = $metaNode->getAttribute('content');
                    break;
                case 'author':
                    $data['meta_author'] = $metaNode->getAttribute('content');
                    break;
                case 'viewport':
                    $data['meta_viewport'] = $metaNode->getAttribute('content');
                    break;
            }
        }
        $data['qty_bases'] = $dom->getElementsByTagName('base')->length;

        $data['qty_css_external_files'] = 0;
        foreach ($dom->getElementsByTagName('link') as $headerLinkNode) {
            if ('stylesheet' == $headerLinkNode->getAttribute('rel')) {
                ++$data['qty_css_external_files'];
            }
        }

        $data['qty_css_internal_files'] = $dom->getElementsByTagName('style')->length;
        $data['qty_javascripts'] = $dom->getElementsByTagName('script')->length;
        $data['qty_h1s'] = $dom->getElementsByTagName('h1')->length;
        $data['qty_h2s'] = $dom->getElementsByTagName('h2')->length;
        $data['qty_h3s'] = $dom->getElementsByTagName('h3')->length;
        $data['qty_h4s'] = $dom->getElementsByTagName('h4')->length;
        $data['qty_h5s'] = $dom->getElementsByTagName('h5')->length;
        $data['qty_h6s'] = $dom->getElementsByTagName('h6')->length;
        $data['qty_hgroups'] = $dom->getElementsByTagName('hgroup')->length;
        $data['qty_sections'] = $dom->getElementsByTagName('section')->length;
        $data['qty_navs'] = $dom->getElementsByTagName('nav')->length;
        $data['qty_asides'] = $dom->getElementsByTagName('aside')->length;
        $data['qty_articles'] = $dom->getElementsByTagName('article')->length;
        $data['qty_addresses'] = $dom->getElementsByTagName('address')->length;
        $data['qty_headers'] = $dom->getElementsByTagName('header')->length;
        $data['qty_footers'] = $dom->getElementsByTagName('footer')->length;
        $data['qty_ps'] = $dom->getElementsByTagName('p')->length;
        $data['qty_total_links'] = $dom->getElementsByTagName('a')->length;

        $data['qty_internal_links'] = $data['qty_external_links'] = $data['qty_targeted_links'] = 0;
        foreach ($dom->getElementsByTagName('a') as $linkNode) {
            if (substr($linkNode->getAttribute('href'), 0, strlen($theSite)) == $theSite) {
                ++$data['qty_internal_links'];
            } else {
                ++$data['qty_external_links'];
            }
            if ($linkNode->getAttribute('target')) {
                ++$data['qty_targeted_links'];
            }
        }

        $data['content_study'] = $this->_get_content_study($dom, 30);

        // text to HTML ratio
        $fullResponseLength = strlen($curl->response);
        if ($fullResponseLength > 0) {
            $theText = preg_replace('/(<script.*?>.*?<\/script>|<style.*?>.*?<\/style>|<.*?>|\r|\n|\t)/ms', '', $curl->response);
            $theText = preg_replace('/ +/ms', ' ', $theText);
            $textLength = strlen($theText);
            $data['text_to_html_ratio'] = 100 * $textLength / $fullResponseLength;
        } else {
            $data['text_to_html_ratio'] = 0;
        }
    }

    private function _prepare_url_technics_data($curl, &$data)
    {
        $data['curlinfo_efective_url'] = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        $data['curlinfo_http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $data['curlinfo_filetime'] = curl_getinfo($curl, CURLINFO_FILETIME);
        $data['curlinfo_total_time'] = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        $data['curlinfo_namelookup_time'] = curl_getinfo($curl, CURLINFO_NAMELOOKUP_TIME);
        $data['curlinfo_connect_time'] = curl_getinfo($curl, CURLINFO_CONNECT_TIME);
        $data['curlinfo_pretransfer_time'] = curl_getinfo($curl, CURLINFO_PRETRANSFER_TIME);
        $data['curlinfo_starttransfer_time'] = curl_getinfo($curl, CURLINFO_STARTTRANSFER_TIME);
        $data['curlinfo_redirect_count'] = curl_getinfo($curl, CURLINFO_REDIRECT_COUNT);
        $data['curlinfo_redirect_time'] = curl_getinfo($curl, CURLINFO_REDIRECT_TIME);
        $data['curlinfo_redirect_url'] = curl_getinfo($curl, CURLINFO_REDIRECT_URL);
        $data['curlinfo_primary_ip'] = curl_getinfo($curl, CURLINFO_PRIMARY_IP);
        $data['curlinfo_primary_port'] = curl_getinfo($curl, CURLINFO_PRIMARY_PORT);
        $data['curlinfo_size_download'] = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD);
        $data['curlinfo_speed_download'] = curl_getinfo($curl, CURLINFO_SPEED_DOWNLOAD);
        $data['curlinfo_request_size'] = curl_getinfo($curl, CURLINFO_REQUEST_SIZE);
        $data['curlinfo_content_length_download'] = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $data['curlinfo_content_type'] = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $data['curlinfo_response_code'] = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $data['curlinfo_http_connectcode'] = curl_getinfo($curl, CURLINFO_HTTP_CONNECTCODE);
        $data['curlinfo_num_connects'] = curl_getinfo($curl, CURLINFO_NUM_CONNECTS);
        $data['curlinfo_appconnect_time'] = curl_getinfo($curl, CURLINFO_APPCONNECT_TIME);
    }

    private function _get_content_study($dom, $maxReturn = 20)
    {
        $linesOfText = $this->_get_clean_body_text_in_lines($dom);

        $keywords = [];
        $words = [];
        $totalWords = $totalReturn = 0;

        foreach ($linesOfText as $line) {
            foreach (explode(' ', $line) as $word) {
                if ('' != trim($word)) {
                    ++$totalWords;
                    if (!empty($words[$word])) {
                        ++$words[$word];
                    } else {
                        $words[$word] = 1;
                    }
                }
            }
        }
        arsort($words);

        $keywordsReturn = [];
        foreach ($words as $word => $count) {
            ++$totalWords;

            if ($totalReturn < $maxReturn) {
                $keywordsReturn[] = $word.'('.$count.')';
                ++$totalReturn;
                //$output->writeln('KEYWORD: '.$word.' COUNT: '.$count);
            }
        }
        $extraData = 'Total words: '.$totalWords.' different ones: '.count($words);

        return implode(',', $keywordsReturn).' ### '.$extraData;
    }

    private function _get_clean_body_text_in_lines($dom)
    {
        while (($r = $dom->getElementsByTagName('script')) && $r->length) {
            $r->item(0)->parentNode->removeChild($r->item(0));
        }
        $body = $dom->saveHTML($dom->getElementsByTagName('body')->item(0));
        $lines = explode(PHP_EOL, strip_tags(str_replace('</', PHP_EOL.'</', $body)));
        $linesFiltered = [];
        foreach ($lines as $key => $value) {
            if ('' == trim($value)) {
                unset($lines[$key]);
            } else {
                $linesFiltered[] = trim($value);
            }
        }

        return $linesFiltered;
    }
}
