<?php

namespace Student\Manager\Admin;

use WP_List_Table;

// Loading WP_List_Table class file
// We need to load it as it's not automatically loaded by WordPress
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
// inheritance WP_List class 
class Student_List extends WP_List_Table
{
    private $table_data;

    public function __construct()
    {
        parent::__construct([
            'singular' => __('Student', 'student-manager'),
            'plural'   => __('Students', 'student-manager')
        ]);
    }


    // Define table columns
    function get_columns()
    {
        $columns = array(
            'cb'               => '<input type="checkbox" />',
            'name'             => __('Name', 'student-manager'),
            'address'          => __('Address', 'student-manager'),
            'phone'            => __('Phone', 'student-manager'),
            'created_by'       => __('Created by', 'student-manager'),
            'created_at'       => __('Created at', 'student-manager')
        );
        return $columns;
    }


    // get the table data 

    private function get_student_data()
    {
        global $wpdb;

        // If no sort, default to user_login
        $orderby = $_GET['orderby'] ?? 'name';
        // If no order, default to asc
        $order = strtoupper($_GET['order'] ?? 'asc');

        // Sanitize order and orderby values to prevent SQL injection
        $orderby = in_array($orderby, ['name', 'phone']) ? $orderby : 'name';
        $order = in_array($order, ['ASC', 'DESC']) ? $order : 'ASC';

        $sql = "SELECT * FROM {$wpdb->prefix}student_infos ORDER BY $orderby $order";
        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }



    // private function get_student_data()
    // {
    //     global $wpdb;
    //     $sql = "SELECT * FROM {$wpdb->prefix}student_infos";
    //     $result = $wpdb->get_results($sql, 'ARRAY_A');

    //     return $result;
    // }

    // Bind table with columns, data and all

    public function prepare_items()
    {
        $this->table_data = $this->get_student_data();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        usort($this->table_data, array(&$this, 'usort_reorder'));
        $this->mh_pagination();

        $this->items = $this->table_data;
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'name':
            case 'address':
            case 'phone':
            case 'created_by':
            case 'created_at':
            default:
                return $item[$column_name];
        }
    }

    // Now we can see the table, but the checkboxes in the first column are missing:
    // To fix it we need to add a new method to our class like this:

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item['id']
        );
    }

    // Ordering

    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'name'  => array('name', false),
            'created_at' => array('created_at', false),
            'phone'   => array('phone', true)
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'name';
        // If no order, default to asc
        $order = isset($_GET['order']) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }



    // Add pagination to WP_List_Table

    public function mh_pagination()
    {
        /* pagination */
        $per_page = 3;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
            'total_pages' => ceil($total_items / $per_page) // use ceil to round up
        ));
    }


    // public function column_name($item)
    // {
    //     $actions = [];

    //     $actions['edit'] = sprintf(
    //         '<a href="%1$s" title="%2$s">%3$s</a>',
    //         admin_url('admin.php?page=student-manager&action=edit&id=' . $item['id']),
    //         __('Edit', 'student-manager'),
    //         __('Edit', 'student-manager')
    //     );

    //     $actions['delete'] = sprintf(
    //         '<a href="%1$s" class="submitdelete" onclick="return confirm(\'%2$s\');" title="%3$s">%4$s</a>',
    //         wp_nonce_url(admin_url('admin-post.php?action=mh-delete-student-info&id=' . $item['id']), 'mh-delete-student-info'),
    //         esc_attr__('Are you sure?', 'student-manager'),
    //         __('Delete', 'student-manager'),
    //         __('Delete', 'student-manager')
    //     );

    //     return sprintf(
    //         '<a href="%1$s"><strong>%2$s</strong></a> %3$s',
    //         admin_url('admin.php?page=student-manager&action=view&id=' . $item['id']),
    //         $item['name'],
    //         $this->row_actions($actions)
    //     );
    // }


    // Action links
    public function column_name($item)
    {
        $actions = [
            'edit' => sprintf(
                '<a href="%1$s" title="%2$s">%3$s</a>',
                admin_url('admin.php?page=student-manager&action=edit&id=' . $item['id']),
                __('Edit', 'student-manager'),
                __('Edit', 'student-manager')
            ),
            'quick_edit' => sprintf(
                '<a href="%1$s" title="%2$s" class="inline-edit" data-id="%3$s">%4$s</a>',
                admin_url('admin.php?page=student-manager&action=quick_edit&id=' . $item['id']),
                __('Quick Edit', 'student-manager'),
                $item['id'],
                __('Quick Edit', 'student-manager')
            ),
            'delete' => sprintf(
                '<a href="%1$s" class="submitdelete" onclick="return confirm(\'%2$s\');" title="%3$s">%4$s</a>',
                wp_nonce_url(admin_url('admin-post.php?action=mh-delete-student-info&id=' . $item['id']), 'mh-delete-student-info'),
                esc_attr__('Are you sure?', 'student-manager'),
                __('Delete', 'student-manager'),
                __('Delete', 'student-manager')
            ),
            'view' => sprintf(
                '<a href="%1$s" title="%2$s">%3$s</a>',
                admin_url('admin.php?page=student-manager&action=view&id=' . $item['id']),
                __('View', 'student-manager'),
                __('View', 'student-manager')
            )
        ];

        return sprintf(
            '<a href="%1$s"><strong>%2$s</strong></a> %3$s',
            admin_url('admin.php?page=student-manager&action=view&id=' . $item['id']),
            $item['name'],
            $this->row_actions($actions)
        );
    }

    // Add bulk actions
    // To show bulk action dropdown
    function get_bulk_actions()
    {
        $actions = array(
            'delete_all'    => __('Delete', 'student-manager'),
            'draft_all' => __('Move to Draft', 'student-manager'),
            'trash_all' => __('Move to Trash', 'student-manager')
        );
        return $actions;
    }
}
