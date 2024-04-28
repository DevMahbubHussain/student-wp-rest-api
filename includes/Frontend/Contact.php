<?php

namespace Student\Manager\Frontend;

class Contact
{
    /**
     * Initializes the class
     */
    function __construct()
    {
        add_shortcode('contact-form', [$this, 'render_shortcode']);
    }

    public function render_shortcode($atts, $content = '')
    {
        wp_enqueue_script('student-contact-script');
        wp_enqueue_style('student-contact-style');
        ob_start();
        include __DIR__ . '/views/contact-form.php';
        return ob_get_clean();
    }
}
