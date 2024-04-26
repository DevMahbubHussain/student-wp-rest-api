<?php

/**
 * Insert New Student.
 *
 * @param array $args
 * @return int|WP_Error
 */
function mh_insert_student_infos($args = [])
{
    global $wpdb;

    if (empty($args['name'])) {
        return new \WP_Error('no-name', __('You must provide a name.', 'student-info'));
    }

    $table = $wpdb->prefix . 'student_infos';
    $defaults = [
        'name'       => '',
        'address'    => '',
        'phone'      => '',
        'created_by' => get_current_user_id(),
        'created_at' => current_time('mysql'),
    ];
    $data = wp_parse_args($args, $defaults);
    if (isset($data['id'])) {
        $id = $data['id'];
        unset($data['id']);
        $updated = $wpdb->update(
            $table,
            $data,
            ['id' => $id],
            [
                '%s',
                '%s',
                '%s',
                '%d',
                '%s'
            ],
            ['%d']
        );
        return $updated;
    } else {
        $format = [
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
        ];

        $inserted = $wpdb->insert($table, $data, $format);

        if (!$inserted) {
            return new \WP_Error('failed-to-insert', __('Failed to insert data', 'student-info'));
        }

        return $wpdb->insert_id;
    }
}

// fetch student data using WP_List class 
function mh_get_students($args = [])
{
    global $wpdb;
    $defaults = [
        'number' => 20,
        'offset' => 0,
        'orderby' => 'id',
        'order' => 'ASC'
    ];
    // $table = $wpdb->prefix . 'student_infos';
    $args = wp_parse_args($args, $defaults);

    // Fetch all students data
    // $students = $wpdb->get_results($wpdb->prepare(
    //     "SELECT * FROM $table 
    // ORDER BY {$args['orderby']} {$args['order']}
    // LIMIT %d,%d",
    //     $args['offset'],
    //     $args['number']
    // ));


    // return $students;

    $sql = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}student_infos
            ORDER BY {$args['orderby']} {$args['order']}
            LIMIT %d, %d",
        $args['offset'],
        $args['number']
    );

    $items = $wpdb->get_results($sql);

    return $items;
}


// count how many student records 

function mh_count_student_info()
{
    global $wpdb;
    $table = $wpdb->prefix . 'student_infos';
    return (int) $wpdb->get_var("SELECT count(id) from $table");
}




function mh_get_student_info($id)
{
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}student_infos WHERE id = %d", $id));
}

// delete student single entry 
function mh_delete_student_info($id)
{
    global $wpdb;
    return $wpdb->delete(
        $wpdb->prefix . 'student_infos',
        ['id' => $id],
        ['%d']
    );
}
