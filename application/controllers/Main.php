<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
	public function index()
	{
		$this->load->helper('url');
		$this->load->library('session');
		
		$this->load->view('header', array(
			'is_logged' => $this->session->is_logged,
			'username' => $this->session->username
		));
		$this->load->view('main_page', array(
			'is_logged' => $this->session->is_logged
		));
	}
}
