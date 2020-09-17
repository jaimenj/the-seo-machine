<?php

defined('ABSPATH') or die('No no no');
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

include_once 'the-seo-machine.php';

TheSeoMachine::get_instance()->uninstall();
