<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineCore
{
    private static $instance;
    private $dom;
    private $response_html;
    private $current_item;
    private $response;
    private $base_url;

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

    public function study($current_item, $debug = false)
    {
        global $wpdb;
        $this->current_item = $current_item;

        $time_start = microtime(true);
        $response = wp_remote_get($current_item->url, ['redirection' => 0]);
        $time_end = microtime(true);
        $time_consumed = $time_end - $time_start;
        $this->response = $response;
        $this->base_url = $this->_get_base_url($current_item->url);

        if ($debug) {
            echo 'TSM> URL = '.$current_item->url.PHP_EOL
                .'TSM> base URL = '.$this->base_url.PHP_EOL;
        }

        if (is_array($response) && !is_wp_error($response)) {
            $this->response_html = $response['body'];
            $http_code = $response['response']['code'];

            if ($debug) {
                echo 'TSM> http_code = '.$http_code.PHP_EOL;
            }

            // Check if it is a redirection..
            if ($http_code >= 300 and $http_code <= 399) {
                $new_url = $this->_prepare_new_url($response['headers']['location'], $debug);

                TheSeoMachineDatabase::get_instance()->save_url_in_queue(
                    $new_url,
                    ($current_item->level + 1),
                    $current_item->url
                );
            }

            // If it's a normal URL or also a redirection, saving..
            $data = [
                'url' => $current_item->url,
                'found_in_url' => $current_item->found_in_url,
                'updated_at' => date('Y-m-d H:i:s'),
                'level' => $current_item->level,
                'time_consumed' => $time_consumed,
            ];

            $this->_prepare_url_insights_data($data, $debug);
            $this->_prepare_url_ttfb_data($data);
            $this->_prepare_url_technics_data($data);

            TheSeoMachineDatabase::get_instance()->save_url($data);
        } else {
            // WP_error!
            $data = [
                'url' => $current_item->url,
                'updated_at' => date('Y-m-d H:i:s'),
                'level' => $current_item->level,
                'http_code' => '-1',
                'time_consumed' => $time_consumed,
            ];

            TheSeoMachineDatabase::get_instance()->save_url($data);
        }
    }

    private function _prepare_url_insights_data(&$data, $debug = false)
    {
        $dom = $this->dom;
        @$dom->loadHTML($this->response_html);

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

        // The links..
        $data['qty_internal_links'] = $data['qty_external_links'] = $data['qty_targeted_links'] = 0;
        foreach ($dom->getElementsByTagName('a') as $linkNode) {
            $new_url = $linkNode->getAttribute('href');
            $new_url = $this->_prepare_new_url($new_url, $debug);

            if (!empty($new_url)) {
                if (substr($new_url, 0, strlen($this->base_url)) == $this->base_url) {
                    ++$data['qty_internal_links'];

                    TheSeoMachineDatabase::get_instance()->save_url_in_queue(
                        $new_url,
                        $this->current_item->level + 1,
                        $this->current_item->url
                    );

                    if ($debug) {
                        echo 'TSM> internal link found = '.$new_url.PHP_EOL;
                    }
                } else {
                    ++$data['qty_external_links'];

                    if ($debug) {
                        echo 'TSM> external link found = '.$new_url.PHP_EOL;
                    }
                }
                if ($linkNode->getAttribute('target')) {
                    ++$data['qty_targeted_links'];
                }
            }
        }

        $data['content_study'] = $this->_get_content_study($dom, 30);

        // Text to HTML ratio
        $fullResponseLength = strlen($this->response_html);
        if ($fullResponseLength > 0) {
            $theText = preg_replace('/(<script.*?>.*?<\/script>|<style.*?>.*?<\/style>|<.*?>|\r|\n|\t)/ms', '', $this->response_html);
            $theText = preg_replace('/ +/ms', ' ', $theText);
            $textLength = strlen($theText);
            $data['text_to_html_ratio'] = number_format($textLength / $fullResponseLength, 2);
        } else {
            $data['text_to_html_ratio'] = 0;
        }
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
        $lines_filtered = [];
        foreach ($lines as $key => $value) {
            if ('' == trim($value)) {
                unset($lines[$key]);
            } else {
                $lines_filtered[] = trim($value);
            }
        }

        return $lines_filtered;
    }

    public function _get_base_url($string)
    {
        $result = parse_url($string);

        return $result['scheme'].'://'.$result['host'];
    }

    private function _prepare_new_url($new_url, $debug = false)
    {
        if ($debug) {
            echo 'TSM> '.$new_url;
        }

        if (!empty(trim($new_url))
        and '#' != substr($new_url, 0, 1)
        and 'email:' != substr($new_url, 0, 6)
        and 'mailto:' != substr($new_url, 0, 7)
        and 'tel:' != substr($new_url, 0, 4)
        and 'skype:' != substr($new_url, 0, 6)
        and 'javascript:' != substr($new_url, 0, 11)
        and 'whatsapp:' != substr($new_url, 0, 9)) {
            // Remove query string..
            $new_url = preg_replace('/\?.*/', '', $new_url);

            // Remove anchors..
            $new_url = preg_replace('/#.*/', '', $new_url);

            // Relative and absolute URLs..
            if ('http' != substr($new_url, 0, 4)) {
                if ('//' == substr($new_url, 0, 2)) {
                    if ('https' == substr($this->current_item->url, 0, 5)) {
                        $new_url = 'https:'.$new_url;
                    } else {
                        $new_url = 'http:'.$new_url;
                    }
                } elseif ('/' == substr($new_url, 0, 1)) {
                    $new_url = ('/' == substr($this->base_url, -1) ? $this->base_url : $this->base_url.'/')
                    .substr($new_url, 1, strlen($new_url) - 1);
                }
            }

            // Add / to the home URL..
            if ($this->base_url == $new_url and '/' != substr($new_url, -1)) {
                $new_url .= '/';
            }
        } else {
            $new_url = '';
        }

        if ($debug) {
            echo ' => '.$new_url.PHP_EOL;
        }

        return $new_url;
    }

    /**
     * The plugin is refactored without using curl, so using your wp_remote_get.
     * But it's missing the info of CURLINFO_STARTTRANSFER_TIME. Because that value is the TTFB (Time To First Byte),
     * an important value to study the SEO of a page. The TTFB is the time between the request of page is received
     * to the server, then WordPress process it, and it starts returning the content of the page (not finishing). If
     * this TTFB is too high that's very bad. That's because this CURL usage is here..
     */
    public function _prepare_url_ttfb_data(&$data)
    {
        if (false !== function_exists('curl_init')) {
            // curl_init is defined, cURL is enabled..

            usleep(0.2 * 1000000);

            $curlHandler = curl_init();
            curl_setopt($curlHandler, CURLOPT_URL, $this->current_item->url);
            curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curlHandler);
            $data['starttransfer_time'] = curl_getinfo($curlHandler)['starttransfer_time'];
            curl_close($curlHandler);

            usleep(0.2 * 1000000);
        }
    }

    public function _prepare_url_technics_data(&$data)
    {
        $response = $this->response;

        $data['http_code'] = $response['response']['code'];

        foreach ($this->get_available_headers() as $header_name) {
            $header_name = strtolower($header_name);
            if (isset($this->response['headers'][$header_name])) {
                $data[$header_name] = $this->response['headers'][$header_name];
            }
        }
    }

    public function get_available_headers()
    {
        return [
            'Access-Control-Allow-Origin',
            'Access-Control-Allow-Credentials',
            'Access-Control-Expose-Headers',
            'Access-Control-Max-Age',
            'Access-Control-Allow-Methods',
            'Access-Control-Allow-Headers',
            'Accept-Patch',
            'Accept-Ranges',
            'Age',
            'Allow',
            'Alt-Svc',
            'Cache-Control',
            'Connection',
            'Content-Disposition',
            'Content-Encoding',
            'Content-Language',
            'Content-Length',
            'Content-Location',
            'Content-Range',
            'Content-Type',
            'Date',
            'Delta-Base',
            'ETag',
            'Expires',
            'IM',
            'Last-Modified',
            'Link',
            'Location',
            'P3P',
            'Pragma',
            'Proxy-Authenticate',
            'Public-Key-Pins',
            'Retry-After',
            'Server',
            'Set-Cookie',
            'Strict-Transport-Security',
            'Trailer',
            'Transfer-Encoding',
            'Tk',
            'Upgrade',
            'Vary',
            'Via',
            'Warning',
            'WWW-Authenticate',
            'X-Frame-Options',
            'Content-Security-Policy',
            'Refresh',
            'Status',
            'Timing-Allow-Origin',
            'X-Content-Duration',
            'X-Content-Type-Options',
            'X-Content-Security-Policy',
            'X-Correlation-ID',
            'X-WebKit-CSP',
            'X-Powered-By',
            'X-Request-ID',
            'X-UA-Compatible',
            'X-XSS-Protection',
        ];
    }
}
