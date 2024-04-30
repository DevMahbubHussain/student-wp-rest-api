<?php

namespace Student\Manager\API;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

class Student extends WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = 'students/v1';
        $this->rest_base = 'students';
    }

    // Register our routes.
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args' => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'permission_callback' => [$this, 'create_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
                ),
                // Register our schema callback.
                'schema' => array($this, 'get_item_schema'),

            )

        );

        // fetch single item in rest api as well as delete
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the object.'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_item'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                    'args'                => array('context' => $this->get_context_param(['default' => 'view'])),

                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),

                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'delete_item'],
                    'permission_callback' => [$this, 'delete_item_permissions_check'],
                    'args'                => array('context' => $this->get_context_param(['default' => 'view'])),

                ),
                // Register our schema callback.
                'schema' => array($this, 'get_item_schema'),

            )
        );
        // end single item in rest api
    }

    public function get_items_permissions_check($request)
    {
        if (current_user_can('manage_options')) {
            return true;
        }
        return false;
    }

    public function get_items($request)
    {
        // return collects means all students list need to return
        $args = array();
        $params = $this->get_collection_params();
        foreach ($params as $key => $value) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }
        // change `per_page` to `number`
        $args['number'] = $args['per_page'];
        $args['offset'] = $args['number'] * ($args['page'] - 1);

        // unset others
        unset($args['per_page']);
        unset(
            $args['page']
        );

        $students = mh_get_students($args);
        $data = array();

        if (empty($students)) {
            return rest_ensure_response($data);
        }

        foreach ($students as $student) {
            $response = $this->prepare_item_for_response($student, $request);
            $data[] = $this->prepare_response_for_collection($response);
        }
        $total     = mh_count_student_info();
        $max_pages = ceil(
            $total / (int) $args['number']
        );

        $response = rest_ensure_response($data);

        $response->header('X-WP-Total', (int) $total);
        $response->header('X-WP-TotalPages', (int) $max_pages);

        return $response;

        // Return all of our students response data.
        // return rest_ensure_response($data);
    }

    /**
     * Prepares the item for the REST response.
     *
     * @param mixed           $item    WordPress representation of the item.
     * @param \WP_REST_Request $request Request object.
     *
     * @return \WP_Error|WP_REST_Response
     */
    public function prepare_item_for_response($item, $request)
    {
        $data   = [];
        $fields = $this->get_fields_for_response($request);

        if (in_array('id', $fields, true)) {
            $data['id'] = (int) $item->id;
        }

        if (in_array('name', $fields, true)) {
            $data['name'] = $item->name;
        }

        if (in_array('address', $fields, true)) {
            $data['address'] = $item->address;
        }

        if (in_array('phone', $fields, true)) {
            $data['phone'] = $item->phone;
        }

        if (in_array('date', $fields, true)) {
            $data['date'] = mysql_to_rfc3339($item->created_at);
        }

        $context = !empty($request['context']) ? $request['context'] : 'view';
        $data    = $this->filter_response_by_context($data, $context);

        $response = rest_ensure_response($data);
        $response->add_links($this->prepare_links($item));

        return $response;
    }

    /**
     * Prepares links for the request.
     *
     * @param \WP_Post $post Post object.
     *
     * @return array Links for the given post.
     */
    protected function prepare_links($item)
    {
        $base = sprintf('%s/%s', $this->namespace, $this->rest_base);

        $links = [
            'self' => [
                'href' => rest_url(trailingslashit($base) . $item->id),
            ],
            'collection' => [
                'href' => rest_url($base),
            ],
        ];

        return $links;
    }

    public function get_item_schema()
    {
        if ($this->schema) {

            // return $this->add_additional_fields_schema($this->schema);
            return $this->schema;
        }

        $this->schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title'                => 'student',
            'type'                 => 'object',
            'properties'           => array(
                'id' => array(
                    'description'  => esc_html__('Unique identifier for the object.', 'student-manager'),
                    'type'         => 'integer',
                    'context'      => array('view', 'edit', 'embed'),
                    'readonly'     => true,
                ),
                'name' => array(
                    'description'  => esc_html__('Name of the student.', 'student-manager'),
                    'type'         => 'string',
                    'context'      => array('view', 'edit'),
                    'required'     => true,
                    'arg_options' => array('sanitize_callback' => 'sanitize_text_field')
                ),

                'address' => array(
                    'description'  => esc_html__('Address of the student.', 'student-manager'),
                    'type'         => 'string',
                    'context'      => array('view', 'edit'),
                    'arg_options' => array('sanitize_callback' => 'sanitize_textarea_field')
                ),

                'phone' => array(
                    'description'  => esc_html__('Phone of the student.', 'student-manager'),
                    'type'         => 'string',
                    'context'      => array('view', 'edit'),
                    'required'     => true,
                    'arg_options' => array('sanitize_callback' => 'sanitize_textarea_field')
                ),

                'date' => array(
                    'description'  => esc_html__('The Date of the object is publish.', 'student-manager'),
                    'type'         => 'string',
                    'format'       => 'date-time',
                    'context'      => array('view'),
                    'readonly'     => true,
                ),


            )
        );

        return $this->schema;
    }


    public function get_collection_params()
    {
        $params = parent::get_collection_params();
        unset($params['search']);
        return $params;
    }


    // fetch single item in rest api all callback functionality 
    protected function get_students($id)
    {
        $student = mh_get_student_info($id);
        if (!$student) {
            return new WP_Error(
                'rest_contact_invalid_id',
                __('Invalid student ID.'),
                ['status' => 404]
            );
        }
        return $student;
    }
    public function get_item($request)
    {
        $student = $this->get_students($request['id']);
        $response = $this->prepare_item_for_response($student, $request);
        $response = rest_ensure_response($response);
        return $response;
    }
    public function get_item_permissions_check($request)
    {
        if (!current_user_can('manage_options')) {
            return false;
        }

        $contact = $this->get_students($request['id']);

        if (is_wp_error($contact)) {
            return $contact;
        }

        return true;
    }

    // delete single item in rest api all callback functionality 
    public function delete_item($request)
    {
        $student = $this->get_students($request['id']);
        $previous = $this->prepare_item_for_response($student, $request);

        $deleted = mh_delete_student_info($request['id']);

        if (!$deleted) {
            return new WP_Error(
                'rest_not_deleted',
                __('Sorry, the student information could not be deleted.'),
                ['status' => 400]
            );
        }

        $data = [
            'deleted'  => true,
            'previous' => $previous->get_data()
        ];

        $response = rest_ensure_response($data);

        return $response;
    }
    public function delete_item_permissions_check($request)
    {
        return $this->get_item_permissions_check($request);
    }


    // create 
    /**
     * Prepares one item for create or update operation.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|object
     */
    protected function prepare_item_for_database($request)
    {
        $prepared = [];

        if (isset($request['name'])) {
            $prepared['name'] = $request['name'];
        }

        if (isset($request['address'])) {
            $prepared['address'] = $request['address'];
        }

        if (isset($request['phone'])) {
            $prepared['phone'] = $request['phone'];
        }

        return $prepared;
    }

    /**
     * Creates one item from the collection.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|WP_REST_Response
     */
    public function create_item($request)
    {
        $student = $this->prepare_item_for_database($request);

        if (is_wp_error($student)) {
            return $student;
        }

        $student_id = mh_insert_student_infos($student);

        if (is_wp_error($student_id)) {
            $student_id->add_data(['status' => 400]);

            return $student_id;
        }

        $student = $this->get_students($student_id);
        $response = $this->prepare_item_for_response($student, $request);

        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $student_id)));

        return rest_ensure_response($response);
    }

    public function create_item_permissions_check($request)
    {
        return $this->get_items_permissions_check($request);
    }


    // update 
    /**
     * Updates one item from the collection.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function update_item($request)
    {
        $student  = $this->get_students($request['id']);
        $prepared = $this->prepare_item_for_database($request);

        $prepared = array_merge((array) $student, $prepared);

        $updated = mh_insert_student_infos($prepared);

        if (!$updated) {
            return new WP_Error(
                'rest_not_updated',
                __('Sorry, the student information could not be updated.'),
                ['status' => 400]
            );
        }

        $student  = $this->get_students($request['id']);
        $response = $this->prepare_item_for_response($student, $request);

        return rest_ensure_response($response);
    }

    public function update_item_permissions_check($request)
    {
        return $this->get_item_permissions_check($request);
    }
}
