<?php

/**
 * Plugin Name: Student Manager
 * Description: Student Manager WP Rest API
 * Plugin URI: https://mahbub.co
 * Author: Mahbub Hussain
 * Author URI: https://mahbub.co
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Main Plugin class
 */
final class Student_Info
{
    const version = '1.0';
    private function __construct()
    {
        $this->define_constants();
        register_activation_hook(__FILE__, array($this, 'activate'));
        add_action('plugins_loaded', array($this, 'init_plugin'));
    }
    /**
     * Initializes a singleton instance
     *
     * @return \Student_Info
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Defined the required plugin constants
     * 
     * @return void 
     * 
     */

    public function define_constants()
    {
        define('MH_STUDENT_MANAGER_VERSION', self::version);
        define('MH_STUDENT_MANAGER_FILE', __FILE__);
        define('MH_STUDENT_MANAGER__PATH', __DIR__);
        define('MH_STUDENT_MANAGER_URL', plugins_url('', MH_STUDENT_MANAGER_FILE));
        define('MH_STUDENT_MANAGER_ASSETS', MH_STUDENT_MANAGER_URL . '/assets');
    }
    /**
     * Initialize the plugin classes
     *
     * @return void
     */
    public function init_plugin()
    {

        if (is_admin()) {
            new Student\Manager\Admin();
        } else {
            new \Student\Manager\Frontend();
        }
    }


    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate()
    {
        $installer =  new \Student\Manager\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 *
 * @return \Student_Info
 */
function student_manager()
{
    return Student_Info::init();
}

// kick-off the plugin
student_manager();
