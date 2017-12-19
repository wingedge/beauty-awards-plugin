<?php

/*
  Plugin Name:  Beauty Awards
  Plugin URI:   http://extanet.com
  Description:  Beauty Awards engine
  Version:      20171102
  Author:       Extanet
  Author URI:   http://extanet.com
  License:      Proprietary
  License URI:  N/A
  Text Domain:  beautyawards
  Domain Path:  /languages
 */

namespace Extanet\BeautyAwards;

define(__NAMESPACE__ . '\ssv', '20171102' . (WP_DEBUG ? '.' . time() : ''));
define(__NAMESPACE__ . '\url', plugin_dir_url(__FILE__));

require_once plugin_dir_path(__FILE__) . '/classes/vendor/autoload.php';

$symfony_loader = new \Symfony\Component\ClassLoader\Psr4ClassLoader();
$symfony_loader->addPrefix('Extanet\BeautyAwards\\', plugin_dir_path(__FILE__) . str_replace('/', DIRECTORY_SEPARATOR, '/classes/Extanet/BeautyAwards'));
$symfony_loader->addPrefix('Alekhin\Geo\\', plugin_dir_path(__FILE__) . str_replace('/', DIRECTORY_SEPARATOR, '/classes/Alekhin/Geo'));
$symfony_loader->register();

// core
Core\BeautyAwards::initialize(__FILE__);
Core\Categories::initialize(__FILE__);
Core\Leads::initialize(__FILE__);
Core\Entries::initialize(__FILE__);
Core\Judges::initialize(__FILE__);
Core\Payments::initialize();

// admin
WPAdmin\WPAdmin::initialize(__FILE__);

// front-end
FrontEnd\EntryForm::initialize(__FILE__);
