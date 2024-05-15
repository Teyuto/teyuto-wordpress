<?php
/*
 * Plugin Name: Teyuto
 * Description: <a target="_blank" href="https://teyuto.com">Teyuto</a> offers video infrastructure tailored for product developers. Leverage <a target="_blank" href="https://teyuto.com">Teyuto</a>'s high-speed video APIs to seamlessly incorporate, expand, and oversee on-demand & low-latency live streaming functionalities within your WordPress platform.
 * Version: 1.0.0
 * Author: teyuto.com
 * Author URI: https://teyuto.com/
 * 
 * 
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: teyuto
 * Domain Path: where to find the translation files (see How to Internationalize Your Plugin)
 */
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
define ('TEYUTO_ROOT_URL', plugin_dir_path(__FILE__));
require_once TEYUTO_ROOT_URL . 'includes/teyuto_functions.php';
require_once TEYUTO_ROOT_URL . 'includes/teyuto_pages.php'; 
require_once TEYUTO_ROOT_URL . 'includes/teyuto_page_add_new_video.php'; 
require_once TEYUTO_ROOT_URL . 'includes/teyuto_page_library.php'; 
require_once TEYUTO_ROOT_URL . 'includes/teyuto_page_settings.php'; 
?>
