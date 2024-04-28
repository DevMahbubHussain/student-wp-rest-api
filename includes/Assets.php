<?php

namespace Student\Manager;

class Assets
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'register_assets']); //for backend 
        add_action('wp_enqueue_scripts', [$this, 'register_assets']); //for frontend 
    }

    public function mh_get_scripts()
    {
        return [
            'student-manager-script' => [
                'src' => MH_STUDENT_MANAGER_ASSETS . '/js/frontend.js',
                'version' => filemtime(MH_STUDENT_MANAGER_PATH . '/assets/js/frontend.js'),
                'deps' => ['jquery']
            ],
            'student-admin-script' => [
                'src'     => MH_STUDENT_MANAGER_ASSETS . '/js/admin.js',
                'version' => filemtime(MH_STUDENT_MANAGER_PATH . '/assets/js/admin.js')
            ],
            'student-contact-script' => [
                'src'     => MH_STUDENT_MANAGER_ASSETS . '/js/contact.js',
                'version' => filemtime(MH_STUDENT_MANAGER_PATH . '/assets/js/contact.js'),
                'deps' => ['jquery']
            ]
        ];
    }

    public function mh_get_styles()
    {
        return [
            'student-manager-styles' => [
                'src' => MH_STUDENT_MANAGER_ASSETS . '/css/frontend.css',
                'version' => filemtime(MH_STUDENT_MANAGER_PATH . '/assets/css/frontend.css'),
            ],
            'student-admin-style' => [
                'src'     => MH_STUDENT_MANAGER_ASSETS . '/css/admin.css',
                'version' => filemtime(MH_STUDENT_MANAGER_PATH . '/assets/css/admin.css')
            ],
            'student-contact-style' => [
                'src'     => MH_STUDENT_MANAGER_ASSETS . '/css/contact-style.css',
                'version' => filemtime(MH_STUDENT_MANAGER_PATH . '/assets/css/contact-style.css')
            ]
        ];
    }

    public function register_assets()
    {
        $scripts = $this->mh_get_scripts();
        $styles = $this->mh_get_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            wp_register_script($handle, $script['src'], $deps, $script['version'], true);
        }
        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            wp_register_style($handle, $style['src'], $deps, $style['version']);
        }

        wp_localize_script('student-contact-script', 'StudentManager', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'error'   => __('Something went wrong', 'student-manager'),
        ]);
    }
}
