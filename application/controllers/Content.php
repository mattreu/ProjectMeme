<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends CI_Controller {
	public function index()
	{
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('session');

		if(!$this->session->is_logged){
			redirect(base_url('index.php/auth/login'));
		}

		$this->load->view('header', array(
			'is_logged' => $this->session->is_logged,
			'username' => $this->session->username
		));

		$this->db->select('images.id as img_id, username, location, images.created_at');
		$this->db->from('images');
		$this->db->join('users', 'images.user_id = users.id');
		$this->db->order_by('images.created_at', 'DESC');
		$images = $this->db->get();
		if($images->num_rows() > 0){
			$data['images'] = $images->result_array();
		}
		else{
			$data['images'] = [];
		}
		$this->load->view('content', $data);
	}
	public function add(){
		$this->load->database();
		$this->load->helper(array('form', 'url'));
		$this->load->library('session');

		$config['upload_path'] = './assets/images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 100;
        $config['max_width'] = 1024;
        $config['max_height'] = 768;
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('image')){
            $error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_form', $error);
        }
        else{
            $uploaddata = $this->upload->data();
			$filename = $uploaddata['file_name'];
			$user = $this->db->select('id')->from('users')->where('username', $this->session->username)->get();
			$userid = $user->result_array()[0]['id'];
			$insertdata = array(
				'user_id' => $userid,
				'location' => $filename
			);
			$this->db->insert('images', $insertdata);
            var_dump($this->upload->data());
        }
	}
}
