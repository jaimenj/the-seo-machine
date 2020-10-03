<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineCore
{
    private static $instance;
    private $dom;
    private $response_html;
    private $current_item;

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

    public function study($current_item)
    {
        global $wpdb;
        $this->current_item = $current_item;

        $time_start = microtime(true);
        $result = wp_remote_get($current_item->url, ['redirection' => 0]);
        $time_end = microtime(true);
        $time_consumed = $time_end - $time_start;

        $this->response_html = $result['body'];
        $http_code = $result['response']['code'];

        // Check if it is a redirection..
        if ($http_code >= 300 and $http_code <= 399) {
            TheSeoMachineDatabase::get_instance()->save_url_in_queue(
                $result['headers']['location'],
                ($current_item->level + 1),
                $current_item->url
            );
        } else {
            // It's a normal URL, saving..

            $data = [
                'url' => $current_item->url,
                'updated_at' => date('Y-m-d H:i:s'),
                'level' => $current_item->level,
                'http_code' => $http_code,
                'time_consumed' => $time_consumed,
                'size_download' => $result['headers']['content-length']
            ];

            $this->_prepare_url_insights_data($result, $data);

            TheSeoMachineDatabase::get_instance()->save_url($data);
        }
    }

    private function _prepare_url_insights_data($result, &$data)
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
            if (substr($linkNode->getAttribute('href'), 0, strlen(get_site_url())) == get_site_url()) {
                ++$data['qty_internal_links'];

                $new_url = $linkNode->getAttribute('href');
                if (!empty(trim($new_url))
                and '#' != substr($new_url, 0, 1)
                and 'email:' != substr($new_url, 0, 6)
                and 'mailto:' != substr($new_url, 0, 7)
                and 'tel:' != substr($new_url, 0, 4)
                and 'skype:' != substr($new_url, 0, 6)
                and 'javascript:' != substr($new_url, 0, 11)
                and 'whatsapp:' != substr($new_url, 0, 9)) {
                    $new_url = $this->_prepare_new_url($new_url);

                    TheSeoMachineDatabase::get_instance()->save_url_in_queue(
                        $new_url,
                        $this->current_item->level + 1,
                        $this->current_item->url
                    );
                }
            } else {
                ++$data['qty_external_links'];
            }
            if ($linkNode->getAttribute('target')) {
                ++$data['qty_targeted_links'];
            }
        }

        $data['content_study'] = $this->_get_content_study($dom, 30);

        // TODO FIX text to HTML ratio
        $fullResponseLength = strlen($this->response_html);
        if ($fullResponseLength > 0) {
            $theText = preg_replace('/(<script.*?>.*?<\/script>|<style.*?>.*?<\/style>|<.*?>|\r|\n|\t)/ms', '', $this->response_html);
            $theText = preg_replace('/ +/ms', ' ', $theText);
            $textLength = strlen($theText);
            $data['text_to_html_ratio'] = 100 * $textLength / $fullResponseLength;
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

    private function _prepare_new_url($new_url)
    {
        // remove query string
        $new_url = preg_replace('/\?.*/', '', $new_url);

        // remove anchors
        $new_url = preg_replace('/#.*/', '', $new_url);

        // relative and absolute URLs
        if ('http' != substr($new_url, 0, 4)) {
            if ('//' == substr($new_url, 0, 2)) {
                if ('https' == substr($this->current_item->url, 0, 5)) {
                    $new_url = 'https:'.$new_url;
                } else {
                    $new_url = 'http:'.$new_url;
                }
            } elseif ('/' == substr($new_url, 0, 1)) {
                $new_url = ('/' == substr(get_site_url(), -1) ? get_site_url() : get_site_url().'/')
                    .substr($new_url, 1, strlen($new_url) - 1);
            }
        }

        if (get_site_url() == $new_url and '/' != substr($new_url, -1)) {
            $new_url .= '/';
        }

        return $new_url;
    }
}
