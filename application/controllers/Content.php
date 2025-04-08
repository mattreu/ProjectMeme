<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends CI_Controller {
	public function index()
	{
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('Image_model');

		if (!$this->session->is_logged) {
			redirect(base_url('index.php/auth/login'));
		}

		$data['images'] = $this->Image_model->get_all_images_with_users();

		$this->load->view('header', [
			'is_logged' => $this->session->is_logged,
			'username' => $this->session->username,
		]);
		$this->load->view('content', $data);
	}

	public function add()
	{
		$this->load->helper(['form', 'url']);
		$this->load->library(['session', 'upload']);
		$this->load->model(['Image_model', 'User_model']);

		if (!$this->session->is_logged) {
			redirect(base_url('index.php/auth/login'));
		}

		$config['upload_path'] = './assets/images/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = 100;
		$config['max_width'] = 1024;
		$config['max_height'] = 768;

		$this->upload->initialize($config);

		if (!$this->upload->do_upload('image')) {
			$error = ['error' => $this->upload->display_errors()];
			$this->load->view('header', [
				'is_logged' => $this->session->is_logged,
				'username' => $this->session->username,
			]);
			$this->load->view('upload_form', $error);
		} else {
			$upload_data = $this->upload->data();
			$filename = $upload_data['file_name'];

			$user_id = $this->User_model->get_user_id_by_username($this->session->username);

			if ($this->Image_model->save_image($user_id, $filename)) {
				redirect(base_url('index.php/content'));
			} else {
				$this->session->set_flashdata('error', 'Failed to save image data. Please try again.');
				$this->load->view('header', [
					'is_logged' => $this->session->is_logged,
					'username' => $this->session->username,
				]);
				$this->load->view('upload_form');
			}
		}
	}

	public function add_caption($id)
	{
		$this->load->helper(['form', 'url']);
		$this->load->library(['session', 'form_validation']);
		$this->load->model('Image_model');

		if (!$this->session->is_logged) {
			redirect(base_url('index.php/auth/login'));
		}

		$image = $this->Image_model->get_image_by_id($id);

		if (!$image) {
			redirect(base_url('index.php/content'));
		}

		$this->form_validation->set_rules([
			[
				'field' => 'text',
				'label' => 'Caption',
				'rules' => 'required',
				'errors' => ['required' => '%s field is required.'],
			],
		]);

		if ($this->form_validation->run() == false) {
			$this->load->view('header', [
				'is_logged' => $this->session->is_logged,
				'username' => $this->session->username,
			]);
			$this->load->view('caption_form', [
				'location' => $image->location,
				'id' => $id,
			]);
		} else {
			$this->caption(
				$image->location,
				$this->input->post('text'),
				$this->input->post('black_area_height', TRUE) ?? 50,
				$this->input->post('text_x', TRUE) ?? 4,
				$this->input->post('text_y', TRUE) ?? 20,
				true
			);

			redirect(base_url('index.php/content'));
		}
	}

	public function caption($location, $text, $black_area_height = 50, $text_x = 4, $text_y = 20, $save = false)
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library(array('session'));
		$this->load->model(['Image_model', 'User_model']);

		if (!$this->session->is_logged) {
			exit();
		}

		$source_image = 'assets/images/' . $location;
		$image_info = getimagesize($source_image);
		$mime = $image_info['mime'];
		switch ($mime) {
			case 'image/jpeg':
				$original = imagecreatefromjpeg($source_image);
				break;
			case 'image/png':
				$original = imagecreatefrompng($source_image);
				break;
			case 'image/gif':
				return $this->gif_caption($location, $text, $black_area_height, $text_x, $text_y, $save); // requires imagemagick
			default:
				return;
		}
		list($width, $height) = $image_info;
		$new_height = $height + $black_area_height;
		
		// build image
		$new_image = imagecreatetruecolor($width, $new_height);
		if ($mime == 'image/png') {
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
			imagefilledrectangle($new_image, 0, 0, $width, $new_height, $transparent);
		}
		$black = imagecolorallocate($new_image, 0, 0, 0);
		$white = imagecolorallocate($new_image, 255, 255, 255);
		imagefilledrectangle($new_image, 0, $height, $width, $new_height, $black); // black area rectangle from (0, height) to (width, new height)
		imagecopy($new_image, $original, 0, 0, 0, 0, $width, $height); // put original image into new one
		$font_path = realpath('system/fonts/texb.ttf');
		$text = empty($text) ? 'Your caption' : $text;
		$text_x = min(max($text_x, 0), 100);
		$text_y = min(max($text_y, 0), 100);
		$font_size = 16;
		imagettftext($new_image, $font_size, 0, 
			intval($width * ($text_x / 100)), 
			intval($height + $font_size + (($black_area_height - $font_size) * $text_y / 100)), 
			$white, $font_path, urldecode($text));

		// save or display new image
		$random_part = substr(md5(uniqid(mt_rand(), true)), 0, 8);
		$filename_part = substr(pathinfo($location, PATHINFO_FILENAME), 0, 10);
		$extension_part = pathinfo($location, PATHINFO_EXTENSION);
		$new_location = $random_part . '_' .$filename_part . '.' . $extension_part;
		$new_path = 'assets/images/' . $new_location;
		if($save){
			$user_id = $this->User_model->get_user_id_by_username($this->session->username);
			$this->Image_model->save_image($user_id, $new_location);
		}
		else{
			header('Content-Type: ' . $mime);
		}
		switch ($mime) {
			case 'image/jpeg':
				imagejpeg($new_image, $save ? $new_path : null, 100);
				break;
			case 'image/png':
				imagepng($new_image, $save ? $new_path : null, 0);
				break;
			default:
				break;
		}
		imagedestroy($original);
		imagedestroy($new_image);
	}

	// alternative caption function for gifs to preserve animation using imagemagick
	private function gif_caption($location, $text = null, $black_area_height = 50, $text_x = 4, $text_y = 20, $save = false)
	{
		$image_data = file_get_contents('assets/images/' .$location);
		$original_image = new Imagick();
		$original_image->readImageBlob($image_data);
		$original_image->coalesceImages();
		$width = $original_image->getImageWidth();
        $height = $original_image->getImageHeight();
		$new_height = $height + $black_area_height;

		$new_image = new Imagick();
		$new_image->setFormat('gif');

		// black area and caption on each frame
		foreach ($original_image as $frame) {
			$frame = clone $frame;
			$frame->setImageDispose(Imagick::DISPOSE_NONE); // stacking background
			$frame->setImageBackgroundColor(new ImagickPixel('transparent'));
			$frame->setImageExtent($width, $new_height);

			$draw_black_area = new ImagickDraw();
			$draw_black_area->setFillColor('black');
            $draw_black_area->rectangle(0, $height, $width, $new_height);
            $frame->drawImage($draw_black_area);

			$draw_text = new ImagickDraw();
			$draw_text->setFillColor('white');
			$font_size = 16;
			$draw_text->setFontSize($font_size);
			$frame->annotateImage(
				$draw_text,
				intval($width * ($text_x / 100)), 
				intval($height + $font_size + (($black_area_height - $font_size) * $text_y / 100)), 
				0, urldecode($text));
			
			$new_image->addImage($frame);
            $new_image->setImageDelay($frame->getImageDelay());
		}
		$new_image->setImageIterations($original_image->getImageIterations());

		if ($save) {
			$random_part = substr(md5(uniqid(mt_rand(), true)), 0, 8);
			$filename_part = substr(pathinfo($location, PATHINFO_FILENAME), 0, 10);
			$extension_part = pathinfo($location, PATHINFO_EXTENSION);
			$new_location = $random_part . '_' .$filename_part . '.' . $extension_part;
			$new_path = 'assets/images/' . $new_location;

			$user_id = $this->User_model->get_user_id_by_username($this->session->username);
			$this->Image_model->save_image($user_id, $new_location);

			$new_image_data = $new_image->getImagesBlob();
			file_put_contents($new_path, $new_image_data);
			$new_image->clear();
			$new_image->destroy();
		} else {
			header('Content-Type: image/gif');
			echo $new_image->getImagesBlob();
			$new_image->clear();
			$new_image->destroy();
			exit;
		}
	}
}
