<?php

class Viewer_Controller extends Controller{

	public static $input = array();
	private static $tag_list = array();
	private static $color_list = array();
	public static $computer_list;
	// Used for computer view
	public static $computer_info;
	public static $is_current_users_computer = false;
	public static $thread;

	public function __construct() { }

	public function view() {

		require("../app/models/Viewer_Model.php");
		$this->model = new Viewer_Model();

		$computer_id = intval($_GET['id']);

		if ($computer_id > 0) {
			$results = $this->model->get_computer($computer_id);
			$user_likes = $this->model->get_computer_like($computer_id);
			
			if ($results[0]['cpu_model'] == null) {
				
				// Unfinished computer
				if ($results[0]['user_id'] == Controller::get_user_id()) {
					View::redirect("browse");
				}else {
					View::redirect("addHardware");
				}
			}else {

				if ($_SERVER["REQUEST_METHOD"] == 'POST' && Controller::is_signed_in()) {
					// look for comment input and add to db
					if ($_POST['comment-submit'] == true) {
   						
						$comment = htmlentities(trim($_POST['comment-text']));

						if (strlen($comment) > 0 && strlen($comment) < 10000) {
							$this->model->new_comment($computer_id, $comment);
						}

					}else if (!empty($_POST['reply-submit'])) {
						$comment_id = intval($_POST['reply-id-value']);
						$reply = htmlentities(trim($_POST['reply-text-'.$comment_id]));

						if (strlen($reply) > 0 && strlen($reply) < 10000) {
							$this->model->new_reply($comment_id, $computer_id, $reply);
						}
					}

					View::redirect("viewComputer", "id=$comment_id");
				}

				static::$computer_info = $results[0];

				if (!file_exists("../lib/user_uploads/".$results[0]['image'])) {
					static::$computer_info['image'] = "noimage.jpg";
				}

				if ($results[0]['user_id'] == Controller::get_user_id()) {
					static::$is_current_users_computer = true;
				}

				static::$thread = $this->get_thread($computer_id);

				View::make("view_computer", "id=$computer_id");
			}
		}
	}

	private function get_thread($computer_id) {
		$comments = $this->model->get_comments($computer_id);
		$replies = $this->model->get_replies($computer_id);

		$thread = array();

		$start_index = 0;

		foreach ($comments as $key => $comment) {

			$comment_user_id = $comment['user_id'];
			$username = $comment['username'];
			$content = $comment['content'];
			$comment_id = $comment['comment_id'];
			$post_date = $comment['post_date'];
			
			$computer_id = static::$computer_info['computer_id'];

			$post_date = $this->time_difference($post_date) . " ago";

			$thread_string = "<li class='comment'><div class='author_head'><span class='comment_author'>$username</span><span class='comment_date'>$post_date</span></div>$content<br><span id='$comment_id' class='reply-link'>reply</span>";

			if ($comment_user_id == Controller::get_user_id() || Controller::is_admin()) {
				$thread_string .= "<a href='deleteComment.php?id=$computer_id&cid=$comment_id' class='delete-link'>Delete</a>";
			}

			$thread_string .= "</li>";

			$thread[] = $thread_string;

			for($i = $start_index; $i < count($replies); $i++) {
				$reply = $replies[$i];
				
				$this_comment_id = $reply['comment_id'];

				if ($comment_id < $this_comment_id) {
					break;

				}else if ($comment_id == $this_comment_id) {

					$reply_id = $reply['reply_id'];
					$reply_user_id = $reply['user_id'];
					$username = $reply['username'];
					$content = $reply['reply'];
					$post_date = $reply['post_date'];

					$post_date = $this->time_difference($post_date) . " ago";

					$thread_string = "<li class='reply'><div class='author_head'><span class='comment_author'>$username</span><span class='comment_date'>$post_date</span></div>$content";

					if ($reply_user_id == Controller::get_user_id() || Controller::is_admin()) {
						$thread_string .= "<br><a href='deleteComment.php?id=$computer_id&rid=$reply_id' class='delete-link' style='margin-left:0;'>Delete</a>";
					}

					$thread_string .= "</li>";

					$thread[] = $thread_string;

					$start_index++;
				}
			}
			$thread[] = "<div id='reply-$comment_id' class='reply-box'><textarea placeholder='Reply...' name='reply-text-$comment_id'></textarea><input type='submit' name='reply-submit' value='Reply' class='good button reply-button' style='float:none;'/></div>";

		}

		static::$computer_info['comment_count'] = count($replies) + count($comments);

		return implode(" \n", $thread);
	}

	public function delete_comment() {

		if (!empty($_GET['cid'])) {
			require("../app/models/Viewer_Model.php");
			$this->model = new Viewer_Model();

			$id = intval($_GET['cid']);

			if(Controller::is_admin()) {
				$this->model->delete_comment_override($id);
			}else {
				$this->model->delete_comment($id);
			}

		}else if (!empty($_GET['rid'])) {
			require("../app/models/Viewer_Model.php");
			$this->model = new Viewer_Model();

			$id = intval($_GET['rid']);

			if(Controller::is_admin()) {
				$this->model->delete_reply_override($id);
			}else {
				$this->model->delete_reply($id);
			}

		}

		$computer_id = intval($_GET['id']);

		if($computer_id){
			View::redirect("viewComputer", "id=$computer_id");
		}else {
			View::redirect('browse');
		}
	}


	private function time_difference($time) {

		date_default_timezone_set("America/New_York");

		$now = date_create("now"); 
		$time = date_create($time);

		$interval = date_diff($time, $now);

		$years = $interval->format('%y');

		if ($years > 0) {
			if ($years > 200) {
				return 'a long time';
			}

			return $years . ($time_period ? (' year' . ($years > 1 ? 's' : '')) : '');
		}

		$months = $interval->format('%m');

		if ($months > 0) {
			return $months . ' month' . ($months > 1 ? 's' : '');
		}

		$days = $interval->format('%d');

		if ($days > 0) {
			if ($days < 7)
				return $days . ' day' . ($days > 1 ? 's' : '');
			else {
				return $weeks . ' week' . ($weeks > 1 ? 's' : '');
			}
		}

		$hours = $interval->format('%h');

		if ($hours > 0) {
			return $hours .  ' hour' . ($hours > 1 ? 's' : '');
		}

		$minutes = $interval->format('%i');

		if ($minutes > 0) {
			return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
		}else {
			return 'moments';
		}
	}
}