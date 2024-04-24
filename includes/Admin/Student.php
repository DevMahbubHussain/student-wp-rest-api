<?php

namespace Student\Manager\Admin;


class Student
{
    public $errors = [];

    public function plugin_page_data()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        switch ($action) {
            case 'new':
                $template = __DIR__ . '/views/student.php';
                break;

            case 'edit':
                $template = __DIR__ . '/views/student-edit.php';
                break;

            case 'view':
                $template = __DIR__ . '/views/student-view.php';
                break;

            default:
                $template = __DIR__ . '/views/student-list.php';
                break;
        }

        if (file_exists($template)) {
            include $template;
        }
    }

    public function form_handler_new_student_info()
    {
        if (!isset($_POST['submit_student_info'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'new-student-info')) {
            wp_die('Are you cheating?');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Are you cheating?');
        }

        // form validation 
        $name    = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $address = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
        $phone   = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

        if (empty($name)) {
            $this->errors['name'] = __('Please provide a name', 'student-info');
        }

        if (empty($phone)) {
            $this->errors['phone'] = __('Please provide a phone number.', 'student-info');
        }

        if (!empty($this->errors)) {
            return;
        }

        //than insert 
        $insert_id = mh_insert_student_infos([
            'name' => $name,
            'address' => $address,
            'phone' => $phone

        ]);

        if (is_wp_error($insert_id)) {
            wp_die($insert_id->get_error_message());
        }

        $redirected_to = admin_url('admin.php?page=student-manager&inserted=true');
        wp_redirect($redirected_to);
        exit;
    }
}
