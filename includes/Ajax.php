<?php

namespace Student\Manager;

class Ajax
{

    public function __construct()
    {
        add_action('wp_ajax_mh_contact_form', [$this, 'submit_enquiry']);
        add_action('wp_ajax_nopriv_mh_contact_form', [$this, 'submit_enquiry']);
    }

    public function submit_enquiry()
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'mh-contact-form')) {
            wp_send_json_error([
                'message' => 'Nonce verification failed!'
            ]);
        }
        // do other stufs as like send mail and other activities
        // wp_mail()
        wp_send_json_success([
            'message' => 'Enquiry has been sent successfully!'
        ]);
    }
}
