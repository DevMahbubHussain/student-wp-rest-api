<?php

namespace Student\Manager\CPT;

class CPT
{

    public function __construct()
    {
        add_action('init', [$this, 'student_manager_cpt']);
        add_action('init', [$this, 'student_manager_tax']);
    }


    public function student_manager_cpt()
    {
        register_post_type(
            'student',
            array(
                'labels'      => array(
                    'name'          => __('Students', 'student-manager'),
                    'singular_name' => __('Student', 'student-manager'),
                    'add_new_item'        => __('Add New Student', 'student-manager'),
                    'add_new'             => __('Add New Student', 'student-manager'),
                ),
                'public'      => true,
                'has_archive' => true,
                'rewrite'     => array('slug' => 'students'),
                'has_archive' => true,
                'show_in_rest' => true,
                'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
                'menu_icon' => 'dashicons-welcome-learn-more'
            )
        );
    }


    public function student_manager_tax()
    {
        $labels = array(
            'name'              => _x('Departments', 'student-manager'),
            'singular_name'     => _x('Department', 'student-manager'),
            'search_items'      => __('Search Departments'),
            'all_items'         => __('All Departments'),
            'parent_item'       => __('Parent Department'),
            'parent_item_colon' => __('Parent Department:'),
            'edit_item'         => __('Edit Department'),
            'update_item'       => __('Update Department'),
            'add_new_item'      => __('Add New Department'),
            'new_item_name'     => __('New Department Name'),
            'menu_name'         => __('Department'),
        );
        $args   = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'department'],
        );
        register_taxonomy('department', ['student'], $args);
    }
}
