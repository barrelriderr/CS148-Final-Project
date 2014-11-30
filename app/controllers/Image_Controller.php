<?php

class Image_Controller extends Controller{

	public static $input = array();
	public static $computer_name = null;
	public static $finished_computer;
	public static $image_list;

	public function __construct() {
		require("../app/models/Image_Model.php");
		$this->model = new Image_Model();
	}

	public function edit() {
		
		static::$image_list = $this->make_image_list();

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {
			$computer_id = intval($_GET['id']);

			// Check if image file is a actual image or fake image
		    $check = getimagesize($_FILES["image_upload"]["tmp_name"]);

		    
		    if($check !== false) {

				// Check file size
				if ($_FILES["image_upload"]["size"] > 200000000) {
					static::$error_messages['upload'] = "Image must be less than 2MB";
				}
	
				$img_extension = pathinfo($_FILES["image_upload"]["name"],PATHINFO_EXTENSION);

				// Allow certain file formats
				if($img_extension != "jpg" && $img_extension != "png" && $img_extension != "jpeg" && $img_extension != "gif" ) {
					static::$error_messages['upload'] = "Only JPEG, PNG and GIF file types allowed.";
				} 

		    } else {
		        static::$error_messages['upload'] = "Not an Image.";
		    }

		    if (count(static::$error_messages) == 0) {

		    	$target_dir = "../lib/user_uploads/";
		    	$target_file = $this->model->new_image($computer_id, $img_extension);
		    	
				move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_dir.$target_file.".".$img_extension);
				View::redirect("editImages", "id=$computer_id");
		    }
		}

		View::make("computer/edit_images");
	}

	private function make_image_list() {
		$computer_id = intval($_GET['id']);

		$images = $this->model->get_images($computer_id);

		$path = "../lib/user_uploads/";
		$img_style="style='height: 100px; margin: 5px;'";

		$html = "";

		foreach ($images as $key => $image) {
			$image_file = $path.$image['image'];
			$image_id = $image['image_id'];

			if (file_exists($image_file)) {
				$html .= "<li><img alt='computer image' $img_style src='".$image_file."'><a href='deleteImage.php?id=$computer_id&iid=$image_id' class='danger button'>DELETE</a></li>\n";
			}else {
				// Auto-clean up bad reference.
				$this->model->delete_image_override($image_id);
			}
		}

		return $html;
	}

	public function delete() {
		$computer_id = intval($_GET['id']);
		$image_id = intval($_GET['iid']);

		$results = $this->model->get_image($image_id);

		$image = $results[0]['image'];

		if ($image) {
			if (Controller::is_admin()) {
				if ($this->model->delete_image_override($image_id))
					unlink("../lib/user_uploads/$image");
			}else if ($this->model->delete_image($image_id)) {
				unlink("../lib/user_uploads/$image");
			}
		}

		View::redirect("editImages", "id=$computer_id");
	}
}