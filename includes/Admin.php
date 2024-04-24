<?php

namespace Student\Manager;

class Admin
{
    public function __construct()
    {
        new Admin\Menu();
        $this->action_dispatch();
    }

    public function action_dispatch()
    {
        $student_info = new Admin\Student();
        add_action('admin_init', [$student_info, 'form_handler_new_student_info']);
    }
}
