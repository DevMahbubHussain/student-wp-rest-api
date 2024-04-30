<?php

namespace Student\Manager;

class API
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_api']);
    }

    public function register_api()
    {
        $student = new API\Student();
        $student->register_routes();
    }
}
