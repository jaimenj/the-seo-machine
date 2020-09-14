<?php

defined('ABSPATH') or die('No no no');
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

include_once 'my-seo-machine.php';

MySeoMachine::get_instance()->uninstall();
