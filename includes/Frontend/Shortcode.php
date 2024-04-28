<?php

namespace Student\Manager\Frontend;


class Shortcode
{
    /**
     * Initializes the class
     */
    function __construct()
    {
        add_shortcode('student-manager', [$this, 'render_shortcode']);
    }

    /**
     * Shortcode handler class
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function render_shortcode($atts, $content = '')
    {
        wp_enqueue_script('student-manager-script');
        wp_enqueue_style('student-manager-styles');
        return '<h2>Hello from Student Manager Shortcode</h2>';
    }
}
