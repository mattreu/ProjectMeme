<?php

class Auth extends CI_Controller {
	public function index()
	{
        redirect(base_url('index.php/auth/login'), 'location');
    }
    public function register()
	{
		$this->load->helper(['form', 'url']);
		$this->load->library(['form_validation', 'session']);
		$this->load->model('User_model');

		if ($this->session->is_logged) {
			redirect(base_url('index.php/content'));
		}

		$form_rules = [
			[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|min_length[5]|max_length[12]|is_unique[users.username]',
				'errors' => [
					'required' => '%s field is required.',
					'min_length' => '%s must be at least 5 characters long.',
					'max_length' => '%s must not exceed 12 characters.',
					'is_unique' => '%s is already in use.',
				],
			],
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|valid_email|is_unique[users.email]',
				'errors' => [
					'required' => '%s field is required.',
					'valid_email' => '%s must be in a proper format.',
					'is_unique' => '%s is already in use.',
				],
			],
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required|min_length[8]|max_length[20]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/]',
				'errors' => [
					'required' => '%s field is required.',
					'min_length' => '%s must be at least 8 characters long.',
					'max_length' => '%s must not exceed 20 characters.',
					'regex_match' => '%s must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
				],
			],
			[
				'field' => 'passconf',
				'label' => 'Password Confirmation',
				'rules' => 'required|matches[password]',
				'errors' => [
					'required' => '%s field is required.',
					'matches' => '%s does not match the password.',
				],
			],
		];
		$this->form_validation->set_rules($form_rules);

		if ($this->form_validation->run() == false) {
			$this->load->view('register_form');
		} else {
			$hashed_password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

			$data = [
				'username' => $this->input->post('username'),
				'email' => $this->input->post('email'),
				'password' => $hashed_password,
			];

			if ($this->User_model->register_user($data)) {
				$this->session->set_userdata([
					'is_logged' => true,
					'username' => $this->input->post('username'),
				]);
				redirect(base_url('index.php/content'));
			} else {
				$this->session->set_flashdata('error', 'Internal error. Please try again later.');
				$this->load->view('register_form');
			}
		}
	}

    public function login()
	{
		$this->load->helper(['form', 'url']);
		$this->load->library(['form_validation', 'session']);
		$this->load->model('User_model');

		if ($this->session->is_logged) {
			redirect(base_url('index.php/content'));
		}

		$form_rules = [
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|valid_email',
				'errors' => [
					'required' => '%s field is required.',
					'valid_email' => '%s must be in a proper format.',
				],
			],
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required',
				'errors' => [
					'required' => '%s field is required.',
				],
			],
		];
		$this->form_validation->set_rules($form_rules);

		if ($this->form_validation->run() == false) {
			$this->load->view('login_form');
		} else {
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$user = $this->User_model->get_user_by_email($email);

			if ($user && password_verify($password, $user->password)) {
				$this->session->set_userdata([
					'is_logged' => true,
					'username' => $user->username,
				]);
				redirect(base_url('index.php/content'));
			} else {
				$this->session->set_flashdata('error', 'Invalid email or password.');
				$this->load->view('login_form');
			}
		}
	}

	public function logout(){
        $this->load->helper('url');
		$this->load->library('session');

        $this->session->sess_destroy();
        redirect(base_url('index.php/main'));
    }
}
