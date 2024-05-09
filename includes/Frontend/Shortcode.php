<?php

namespace Student\Manager\Frontend;

use WP_Query;

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

        // parse attribute 
        $attr = shortcode_atts([
            'post_type' => 'student',
            'posts_per_page' => 10,
            'order' => 'DESC',
            'orderby' => 'date'

        ], 'student-manager');

        // sanitize attributes before proceeding 
        // make sure post type is exits 
        $attr['post_type'] = post_type_exists($attr['post_type']) ? $attr['post_type'] : 'post';
        // posts per page should be integer
        $attr['posts_per_page']  = intval($attr['posts_per_page']);
        // only asc and desc allow 
        $attr['order'] = in_array($attr['order'], ['ASC', 'DESC']) ? $attr['order'] : 'DESC';
        // strip tags from orderby 
        $attr['orderby'] = strip_tags($attr['orderby']);

        $html = '';
        // query the last 10 student information 
        $args = array(
            'post_type' => $attr['post_type'],
            'posts_per_page' => $attr['posts_per_page'],
            'orderby' => $attr['orderby'],
            'order' => $attr['order']
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            $html .= '<ul>';

            while ($query->have_posts()) {
                $query->the_post();
                $student_name = esc_attr(carbon_get_the_post_meta("crb_name"));
                $student_email = esc_attr(carbon_get_the_post_meta("crb_email"));
                $feature_image = has_post_thumbnail() ? get_the_post_thumbnail() : '';
                $departments = wp_get_post_terms(get_the_ID(), 'department');
                $department_names = array();
                foreach ($departments as $department) {
                    $department_names[] = $department->name;
                }
                $department_list = implode(', ', $department_names);
                // var_dump($departments);
                // var_dump($feature_image);
                $html .= '<li><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a> - ' . $student_name . ' ' . $student_email . $feature_image . $department_list . '</li>';
            }
            $html .= '</ul>';
        }
        wp_reset_postdata();

        return $html;
    }
    // shortcode will be like this 
    // [student-manager post_type="posts|page|cpt" posts_per_page="5" order="ASC" orderby="title"]
}
