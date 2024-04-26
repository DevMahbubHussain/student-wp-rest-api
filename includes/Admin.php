<?php

namespace Student\Manager;

class Admin
{
    public function __construct()
    {

        $student = new Admin\Student();
        $this->action_dispatch($student);
        new Admin\Menu($student);
    }

    public function action_dispatch($student)
    {
        // $student_info = new Admin\Student();
        add_action('admin_init', [$student, 'form_handler_new_student_info']);
        add_action('admin_post_mh-delete-student-info', [$student, 'mh_student_delete_info']);
    }
}
