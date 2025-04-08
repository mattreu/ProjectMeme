<?php

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function register_user($data)
    {
        return $this->db->insert('users', $data);
    }

    public function is_username_taken($username)
    {
        return $this->db->where('username', $username)->count_all_results('users') > 0;
    }

    public function is_email_taken($email)
    {
        return $this->db->where('email', $email)->count_all_results('users') > 0;
    }

    public function get_user_by_email($email)
    {
        $query = $this->db->get_where('users', ['email' => $email]);
        return $query->row();
    }

    public function get_user_id_by_username($username)
    {
        $query = $this->db->select('id')->from('users')->where('username', $username)->get();
        $result = $query->row_array();
        return $result ? $result['id'] : null;
    }
}
