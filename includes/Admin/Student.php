<?php

namespace Student\Manager\Admin;

use Student\Manager\Traits\Form_Error;

class Student
{

    use Form_Error;

    public function plugin_page_data()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        switch ($action) {
            case 'new':
                $template = __DIR__ . '/views/student.php';
                break;

            case 'edit':
                $student = mh_get_student_info($id);
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

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
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

        $args =
            [
                'name' => $name,
                'address' => $address,
                'phone' => $phone

            ];

        if ($id)
            $args['id'] = $id;

        //than insert 
        $insert_id = mh_insert_student_infos($args);

        if (is_wp_error($insert_id)) {
            wp_die($insert_id->get_error_message());
        }
        if ($id) {
            $redirected_to = admin_url('admin.php?page=student-manager&action=edit&student-updated=true&id=' . $id);
        } else {
            $redirected_to = admin_url('admin.php?page=student-manager&inserted=true');
        }
        wp_redirect($redirected_to);
        exit;
    }

    public function mh_student_delete_info()
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'mh-delete-student-info')) {
            wp_die('Are you cheating?');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Are you cheating?');
        }

        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;


        if (mh_delete_student_info($id)) {
            $redirected_to = admin_url('admin.php?page=student-manager&student-deleted=true');
        } else {
            $redirected_to = admin_url('admin.php?page=student-manager&student-deleted=false');
        }

        wp_redirect($redirected_to);
        exit;
    }
}
