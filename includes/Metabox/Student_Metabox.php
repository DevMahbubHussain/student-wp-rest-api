<?php

namespace Student\Manager\Metabox;

use Carbon_Fields\Container;
use Carbon_Fields\Field\Field;

class Student_Metabox
{
    public function __construct()
    {
        // add_action('plugin_loaded', [$this, 'crb_load']);
        add_action('carbon_fields_register_fields', [$this, 'student_manager_metabox']);
    }

    // public function crb_load()
    // {
    //     \Carbon_Fields\Carbon_Fields::boot();
    // }

    public function student_manager_metabox()
    {
        Container::make('post_meta', __('Student Meta Data'))
            ->where('post_type', '=', 'student')
            ->set_context('normal')
            ->set_priority('high')
            ->add_fields(array(
                Field::make('text', 'crb_name'),
                Field::make('text', 'crb_email'),
            ));

        // Container::make('post_meta', 'Custom Data')
        //     ->where('post_type', '=', 'page')
        //     ->add_fields(array(
        //         Field::make('map', 'crb_location')
        //             ->set_position(37.423156, -122.084917, 14),
        //         Field::make('sidebar', 'crb_custom_sidebar'),
        //         Field::make('image', 'crb_photo'),
        //     ));
    }
}
