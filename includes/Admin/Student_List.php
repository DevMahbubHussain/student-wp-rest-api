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






















    // public static function get_students($per_page = 5, $page_number = 1)
    // {
    //     global $wpdb;
    //     $sql = "SELECT * FROM {$wpdb->prefix}student_infos";
    //     if (!empty($_REQUEST['orderby'])) {
    //         $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
    //         $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
    //     }

    //     $sql .= " LIMIT $per_page";
    //     $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


    //     $result = $wpdb->get_results($sql, 'ARRAY_A');

    //     return $result;
    // }

    // /**
    //  * Returns the count of records in the database.
    //  *
    //  * @return null|string
    //  */
    // public static function record_count()
    // {
    //     global $wpdb;

    //     $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}student_infos";

    //     return $wpdb->get_var($sql);
    // }

    // public function no_items()
    // {
    //     _e('No student information found', 'student-manager');
    // }

    // /**
    //  * Render a column when no column specific method exist.
    //  *
    //  * @param array $item
    //  * @param string $column_name
    //  *
    //  * @return mixed
    //  */
    // public function column_default($item, $column_name)
    // {
    //     switch ($column_name) {
    //         case 'name':
    //         case 'address':
    //         case 'phone':
    //         case 'created_by':
    //         case 'created_at':
    //             return $item[$column_name];
    //         default:
    //             return print_r($item, true); //Show the whole array for troubleshooting purposes
    //     }
    // }

    // /**
    //  * Render the bulk edit checkbox
    //  *
    //  * @param array $item
    //  *
    //  * @return string
    //  */
    // function column_cb($item)
    // {
    //     return sprintf(
    //         '<input type="checkbox" name="bulk-delete[]" value="%s" />',
    //         $item['ID']
    //     );
    // }

    // public function column_name($item)
    // {
    //     $actions = [];

    //     $actions['edit']   = sprintf('<a href="%s" title="%s">%s</a>', admin_url('admin.php?page=student-manager&action=edit&id=' . $item->id), $item->id, __('Edit', 'student-manager'), __('Edit', 'student-manager'));
    //     $actions['delete'] = sprintf('<a href="%s" class="submitdelete" onclick="return confirm(\'Are you sure?\');" title="%s">%s</a>', wp_nonce_url(admin_url('admin-post.php?action=mh-delete-student-info&id=' . $item->id), 'mh-delete-student-info'), $item->id, __('Delete', 'student-manager'), __('Delete', 'student-manager'));

    //     return sprintf(
    //         '<a href="%1$s"><strong>%2$s</strong></a> %3$s',
    //         admin_url('admin.php?page=student-manager&action=view&id' . $item->id),
    //         $item->name,
    //         $this->row_actions($actions)
    //     );
    // }

    // /**
    //  *  Associative array of columns
    //  *
    //  * @return array
    //  */
    // function get_columns()
    // {
    //     $columns = [
    //         'cb'      => '<input type="checkbox" />',
    //         'name'    => __('Name', 'student-manager'),
    //         'address' => __('Address', 'student-manager'),
    //         'phone'    => __('Phone', 'student-manager'),
    //         'created_by'    => __('Created By', 'student-manager'),
    //         'created_at'    => __('Created At', 'student-manager')
    //     ];

    //     return $columns;
    // }

    // /**
    //  * Columns to make sortable.
    //  *
    //  * @return array
    //  */
    // public function get_sortable_columns()
    // {
    //     $sortable_columns = array(
    //         'name' => array('name', true),
    //         'phone' => array('phone', false)
    //     );

    //     return $sortable_columns;
    // }
    // /**
    //  * Returns an associative array containing the bulk action
    //  *
    //  * @return array
    //  */
    // public function get_bulk_actions()
    // {
    //     $actions = [
    //         'bulk-delete' => 'Delete'
    //     ];

    //     return $actions;
    // }
    // /**
    //  * Handles data query and filter, sorting, and pagination.
    //  */
    // public function prepare_items()
    // {

    //     $this->_column_headers = $this->get_column_info();

    //     /** Process bulk action */
    //     $this->process_bulk_action();

    //     $per_page     = $this->get_items_per_page('customers_per_page', 5);
    //     $current_page = $this->get_pagenum();
    //     $total_items  = self::record_count();

    //     $this->set_pagination_args([
    //         'total_items' => $total_items, //WE have to calculate the total number of items
    //         'per_page'    => $per_page //WE have to determine how many items to show on a page
    //     ]);

    //     $this->items = self::get_students($per_page, $current_page);
    // }





    // // Define table columns

    // public function get_columns()
    // {
    //     $columns = [
    //         'cb'         => '<input type="checkbox" />',
    //         'name'       => __('Name', 'student-manager'),
    //         'address'    => __('Address', 'student-manager'),
    //         'phone'      => __('Phone', 'student-manager'),
    //         'created_by'   => __('Created By', 'student-manager'),
    //         'created_at' => __('Date', 'student-manager'),
    //     ];

    //     return $columns;
    // }

    // // Bind table with columns, data and all

    // public function prepare_items()
    // {
    //     $columns = $this->get_columns();
    //     $hidden = array();
    //     $sortable = $this->get_sortable_columns();
    //     $primary  = 'name';
    //     $this->_column_headers = array($columns, $hidden, $sortable, $primary);
    //     /**
    //      * Pagination
    //      */
    //     $per_page     = 1;
    //     $current_page = $this->get_pagenum();
    //     $offset       = ($current_page - 1) * $per_page;

    //     $args = [
    //         'number' => $per_page,
    //         'offset' => $offset,
    //     ];

    //     if (isset($_REQUEST['orderby']) && isset($_REQUEST['order'])) {
    //         $args['orderby'] = $_REQUEST['orderby'];
    //         $args['order']   = $_REQUEST['order'];
    //     }

    //     $this->items = mh_count_student_info($args);

    //     $this->set_pagination_args([
    //         'total_items' => mh_count_student_info(),
    //         'per_page'    => $per_page
    //     ]);
    //     $this->items = mh_get_students();
    // }


    // // function column_default($item, $column_name)
    // // {
    // //     switch ($column_name) {
    // //         case 'id':
    // //         case 'name':
    // //         case 'address':
    // //         case 'phone':
    // //         case 'created_by':
    // //         case 'created_at':
    // //         default:
    // //             return $item[$column_name];
    // //     }
    // // }



    // protected function column_default($item, $column_name)
    // {

    //     switch ($column_name) {

    //         case 'created_at':
    //             return wp_date(get_option('date_format'), strtotime($item->created_at));

    //         default:
    //             return isset($item->$column_name) ? $item->$column_name : '';
    //     }
    // }

    // function column_cb($item)
    // {
    //     return sprintf(
    //         '<input type="checkbox" name="student_id[]" value="%d" />',
    //         $item->id
    //     );
    // }


    // public function get_sortable_columns()
    // {
    //     $sortable_columns = array(
    //         'name'  => array('name', true),
    //         'phone'   => array('phone', true)
    //     );
    //     return $sortable_columns;
    // }


    // // Sorting function
    // public function usort_reorder($a, $b)
    // {
    //     // If no sort, default to user_login
    //     $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'user_login';

    //     // If no order, default to asc
    //     $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

    //     // Determine sort order
    //     $result = strcmp($a[$orderby], $b[$orderby]);

    //     // Send final sort direction to usort
    //     return ($order === 'asc') ? $result : -$result;
    // }


    // public function column_name($item)
    // {
    //     $actions = [];

    //     $actions['edit']   = sprintf('<a href="%s" title="%s">%s</a>', admin_url('admin.php?page=student-manager&action=edit&id=' . $item->id), $item->id, __('Edit', 'student-manager'), __('Edit', 'student-manager'));
    //     $actions['delete'] = sprintf('<a href="%s" class="submitdelete" onclick="return confirm(\'Are you sure?\');" title="%s">%s</a>', wp_nonce_url(admin_url('admin-post.php?action=mh-delete-student-info&id=' . $item->id), 'mh-delete-student-info'), $item->id, __('Delete', 'student-manager'), __('Delete', 'student-manager'));

    //     return sprintf(
    //         '<a href="%1$s"><strong>%2$s</strong></a> %3$s',
    //         admin_url('admin.php?page=student-manager&action=view&id' . $item->id),
    //         $item->name,
    //         $this->row_actions($actions)
    //     );
    // }
}
