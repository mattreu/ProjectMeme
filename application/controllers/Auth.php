<?php

class Auth extends CI_Controller {
	public function index()
	{
        redirect(base_url('index.php/auth/login'), 'location');
    }
    public function register(){
        $this->load->database();
		$this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation', 'session'));

		if($this->session->is_logged){
			redirect(base_url('index.php/content'));
		}

		$form_rules = array(
			array(
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|min_length[5]|max_length[12]|is_unique[users.username]',
				'errors' => array(
					'required' => '%s field is required.',
					'min_length' => '%s must be at least 5 characters long.',
					'max_length' => '%s must not exceed 20 characters.',
					'is_unique' => '%s already in use.'
				)
			),
			array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|valid_email|is_unique[users.email]',
				'errors' => array(
					'required' => '%s field is required.',
					'valid_email' => '%s must be in a proper format.',
					'is_unique' => '%s already in use.'
				)
			),
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required|min_length[8]|max_length[20]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/]',
				'errors' => array(
					'required' => '%s field is required.',
					'min_length' => '%s must be at least 8 characters long.',
					'max_length' => '%s must not exceed 20 characters.',
					'regex_match' => '%s must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.'
				)
			),
			array(
				'field' => 'passconf',
				'label' => 'Password Confirmation',
				'rules' => 'required|matches[password]',
				'errors' => array(
					'required' => '%s field is required.',
					'matches' => '%s does not match password.'
				)
			),
		);
		$this->form_validation->set_rules($form_rules);

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('register_form');
        }
        else
        {
			$password = $_POST['password'];
			$hashed_password = password_hash($password, PASSWORD_BCRYPT);
			$insertdata = array(
				'username' => $_POST['username'],
				'email' => $_POST['email'],
				'password' => $hashed_password
			);
			if($this->db->insert('users', $insertdata)){
				$this->session->is_logged = TRUE;
				$this->session->username = $_POST['username'];
            	redirect(base_url('index.php/content'), 'location');
			}
			else{
				$this->session->set_flashdata('error', 'Internal error. Try again later.');
				$this->load->view('register_form');
			}
        }
	}

    public function login(){
        $this->load->database();
		$this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation', 'session'));

		if($this->session->is_logged){
			redirect(base_url('index.php/content'));
		}

		$form_rules = array(
			array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|valid_email',
				'errors' => array(
					'required' => '%s field is required.',
					'valid_email' => '%s must be in a proper format.'
				)
			),
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required',
				'errors' => array(
					'required' => '%s field is required.'
				)
			),
		);
		$this->form_validation->set_rules($form_rules);

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('login_form');
        }
        else
        {
			$email = $_POST['email'];
            $password = $_POST['password'];
			$result = $this->db->get_where('users', array('email' => $email));
			if($result->num_rows() == 1){
                $user = $result->row();
                if (password_verify($password, $user->password)) {
                    $this->session->is_logged = TRUE;
                    $this->session->username = $user->username;
                    redirect(base_url('index.php/content'), 'location');
                } else {
                    $this->session->set_flashdata('error', 'Invalid email or password.');
					$this->load->view('login_form');
                }
			}
			else{
				$this->session->set_flashdata('error', 'Invalid email or password.');
				$this->load->view('login_form');
			}
        }
    }
    public function logout(){
        $this->load->helper('url');
		$this->load->library('session');

        $this->session->sess_destroy();
        redirect(base_url('index.php/main'), 'location');
    }
}
