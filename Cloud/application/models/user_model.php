<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model
{
    protected $user_table = 'users';

    /**
     * Use Registration
     * @param: {array} User Data
     */

    public function fetch_all_users()
    {
        $query = $this->db->get('users');
        foreach($query->result() as $row)
        {
            $user_data[] = [
                'username' => $row->username,
                'email' => $row->email,
                'full_name' => $row->full_name,
                'insert' => $row->update,
                'update' => $row->update,

            ];
        }
        return $user_data;
    }

    public function insert_user(array $data) {
        $this->db->insert($this->user_table, $data);
        return $this->db->insert_id();
    }

    /**
     * User Login
     * ----------------------------------
     * @param: username or email address
     * @param: password
     */
    public function user_login($username, $password)
    {
        $this->db->where('email', $username);
        $this->db->or_where('username', $username);
        $q = $this->db->get($this->user_table);

        if( $q->num_rows() ) 
        {
            $user_pass = $q->row('password');
            if(md5($password) === $user_pass) {
                return $q->row();
            }
            return FALSE;
        }else{
            return FALSE;
        }
    }
    
//1coba api lengkapi data user dan update//
    public function complete_user($id, $data)
{
    // Implement your logic to complete user data in the database
    // Replace this with your own database update query or ORM operations
    $completed = false;

    // Fetch the user from the database
    $user = $this->db->get_where('users', ['id' => $id])->row();

    if ($user) {
        // Update the necessary fields based on the provided data
        $user_data = [
            'field1' => $data['field1'],
            'field2' => $data['field2'],
            // Update other fields as required
        ];

        // Perform the update operation
        $this->db->where('id', $id);
        $this->db->update('users', $user_data);

        // Check if the update was successful
        $completed = $this->db->affected_rows() > 0;
    }

    return $completed;
}

public function update_user($id, $data)
{
    // Implement your logic to update user data in the database
    // Replace this with your own database update query or ORM operations
    $updated = false;

    // Fetch the user from the database
    $user = $this->db->get_where('users', ['id' => $id])->row();

    if ($user) {
        // Update the relevant fields based on the provided data
        if (isset($data['field1'])) {
            $user->field1 = $data['field1'];
        }
        if (isset($data['field2'])) {
            $user->field2 = $data['field2'];
        }
        // Update other fields as required

        // Perform the update operation
        $this->db->where('id', $id);
        $this->db->update('users', $user);

        // Check if the update was successful
        $updated = $this->db->affected_rows() > 0;
    }

    return $updated;
    //1coba api lengkapi data user dan update api//
}

}