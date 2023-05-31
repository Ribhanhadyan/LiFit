<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Api extends RestController
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function users_get()
    {
        // Users from a data store e.g. database
        $users = [
            ['id' => 0, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 1, 'name' => 'Jim', 'email' => 'jim@example.com'],
        ];

        $id = $this->get('id');

        if ($id === null) {
            // Check if the users data store contains users
            if ($users) {
                // Set the response and exit
                $this->response($users, 200);
            } else {
                // Set the response and exit
                $this->response([
                    'status' => false,
                    'message' => 'No users were found'
                ], 404);
            }
        } else {
            if (array_key_exists($id, $users)) {
                $this->response($users[$id], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No such user found'
                ], 404);
            }
        }
    }

    //user complete and update data
        // POST /users/{id}/complete
        public function users_complete_post($id)
        {
            $data = $this->post();
            $updated = $this->User_model->complete_user($id, $data);
            if ($updated) {
                $this->response(['message' => 'User data completed successfully'], 200);
            } else {
                $this->response(['error' => 'Failed to complete user data'], 404);
            }
        }
    
        // PUT /users/{id}
        public function users_put($id)
        {
            $data = $this->put();
            $updated = $this->User_model->update_user($id, $data);
            if ($updated) {
                $this->response(['message' => 'User data updated successfully'], 200);
            } else {
                $this->response(['error' => 'Failed to update user data'], 404);
            }
        }
        //user complete and update data
}
