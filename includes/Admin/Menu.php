<?php

namespace Student\Manager\Admin;

/**
 * Menu Handelar class
 */
class Menu
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }


    public function admin_menu()
    {
        add_menu_page(
            __('Studdent Manager', 'student-info'),
            __('Student Manager', 'student-info'),
            'manage_options',
            'student-manager',
            [$this, 'plugin_page'],
            'dashicons-welcome-learn-more'
        );
    }

    public function plugin_page()
    {
        echo "I am from Menu callback functions";
    }
}
