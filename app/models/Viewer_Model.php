<?php

class Viewer_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	public function new_comment($computer_id, $comment) {
		$query = " 	INSERT INTO
						computer_comments
						(commenter, computer, content)
					VALUES
						(?, ?, ?)";

		$user_id = Controller::get_user_id();

		return $this->binary_query($query, array($user_id, $computer_id, $comment));
	}

	public function new_reply($comment_id, $computer_id, $reply) {
		$query = "	INSERT INTO
						comment_replies
						(comment_id, computer, replier, reply)
					VALUES
						(?, ?, ?, ?)";

		$replier_id = Controller::get_user_id();

		return $this->binary_query($query, array($comment_id, $computer_id, $replier_id, $reply));
	}

	public function get_computer($computer_id) {
			$query = "	SELECT 					
							comps.computer_id,
							comps.name,
							comps.description,
							CONCAT(cpu_makers.name, ' ', cpus.model) AS cpu_model,
							CONCAT(gpu_makers.name, ' ', gpus.model, ' x', comps.gpu_count) AS gpu_model,
	                        CONCAT(ram_sizes.size_name, ' @ ', ram_speeds.speed,'MHz') AS ram,
							COUNT(likes.computer_id) AS count,
							users.username,
							users.user_id
						FROM
							users,
							cpus,
							cpu_makers,
	                        ram_sizes,
	                        ram_speeds,
							computers AS comps
							LEFT JOIN 
								computer_likes AS likes 
								ON comps.computer_id = likes.computer_id 
							LEFT JOIN
								gpus
								ON comps.gpu_id = gpus.gpu_id
							LEFT JOIN
								gpu_makers
								ON gpu_makers.maker_id = gpus.maker_id
						WHERE
							users.user_id = comps.builder_id
	                        AND comps.ram_speed = ram_speeds.ram_id
	                        AND comps.ram_size = ram_sizes.ram_id
							AND comps.cpu_id = cpus.cpu_id
							AND cpus.maker_id = cpu_makers.maker_id
							AND comps.computer_id = ?";

			return $this->return_query($query, array($computer_id));
	}

	public function get_images($computer_id) {
		$query = " 	SELECT
						image_id,
						CONCAT(computer_images.image_id,'.',computer_images.extension) AS image
					FROM 
						computer_images
					WHERE
						computer_id=?";

		return $this->return_query($query, array($computer_id));
	}

	public function get_computer_like($computer_id) {
		$query = "	SELECT 
						*
					FROM
						computer_likes
					WHERE
						computer_id=?
						AND liker_id=?";

		$has_liked = $this->return_query($query, array($computer_id, Controller::get_user_id()));

		if ($has_liked) {
			return true;
		}
		return false;
	}

	public function delete_comment_override($id) {
		$query = "	DELETE FROM 
						computer_comments
					WHERE
						comment_id=?";

		return $this->binary_query($query, array($id));
	}

	public function delete_comment($id) {
		$query = "	DELETE FROM 
						computer_comments
					WHERE
						comment_id=?
						AND commenter=?";

		$commenter = Controller::get_user_id();

		return $this->binary_query($query, array($id, $commenter));
	}

	public function delete_reply_override($id) {
		$query = "	DELETE FROM 
						comment_replies
					WHERE
						reply_id=?";

		return $this->binary_query($query, array($id));
	}

	public function delete_reply($id) {
		$query = "	DELETE FROM 
						comment_replies
					WHERE
						reply_id=?
						AND replier=?";

		$replier = Controller::get_user_id();

		return $this->binary_query($query, array($id, $replier));
	}

	public function get_comments($computer_id) {
		$query = "	SELECT
						user_id,
						username,
						comment_id,
						content,
						post_date
					FROM
						computer_comments,
						users
					WHERE
						commenter=users.user_id
						AND computer=?";

		return $this->return_query($query, array($computer_id));
	}

	public function get_replies($computer_id) {
		$query = "	SELECT
						user_id,
						username,
						comment_id,
						reply_id,
						reply,
						post_date
					FROM
						comment_replies,
						users
					WHERE
						replier=users.user_id
						AND computer=?
					ORDER BY 
						comment_id, reply_id";

		return $this->return_query($query, array($computer_id));
	}
}