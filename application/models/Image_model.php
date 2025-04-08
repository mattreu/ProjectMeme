<?php

class Image_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_images_with_users()
    {
        $this->db->select('images.id as img_id, username, location, images.created_at');
        $this->db->from('images');
        $this->db->join('users', 'images.user_id = users.id');
        $this->db->order_by('images.created_at', 'DESC');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_image_by_id($id)
    {
        $query = $this->db->get_where('images', ['id' => $id]);
        return $query->num_rows() === 1 ? $query->row() : null;
    }

    public function save_image($user_id, $filename)
    {
        $data = [
            'user_id' => $user_id,
            'location' => $filename,
        ];

        return $this->db->insert('images', $data);
    }
}
