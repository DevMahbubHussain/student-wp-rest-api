<?php

namespace Student\Manager\Admin;

/**
 * Menu Handelar class
 */
class Menu
{
    private $mh_menus;
    private $students_object;
    public $student;
    public function __construct($student)
    {
        $this->student = $student;
        add_action('admin_menu', [$this, 'admin_menu']);
    }


    public function admin_menu()
    {
        $parent_slug = 'student-manager';
        $capability = 'manage_options';
        $this->mh_menus = add_menu_page(
            __('Studdent Manager', 'student-info'),
            __('Student Info', 'student-info'),
            $capability,
            $parent_slug,
            [$this->student, 'plugin_page_data'],
            'dashicons-welcome-learn-more'
        );
        add_submenu_page($parent_slug, 'student-info', __('Student Info', 'student-info'), $capability, $parent_slug, [$this->student, 'plugin_page_data']);
        add_submenu_page($parent_slug, 'student-manager-students', __('Settings', 'student-info'), $capability, 'student-settings', [$this, 'settings_page']);
        add_action("load-$this->mh_menus", [$this, 'screen_option']);
    }

    /**
     * Screen options
     *
     * @return void
     */
    public function screen_option()
    {
        $option = 'per_page';
        $args   = [
            'label'   => 'Students',
            'default' => 3,
            'option'  => 'students_per_page'
        ];
        add_screen_option($option, $args);
        $this->students_object = new Student_List();
    }


    public function plugin_page()
    {
        $student_info = new Student();
        $student_info->plugin_page_data();
    }

    public function settings_page()
    {
        echo "Plugin Settings Page";
    }
}
