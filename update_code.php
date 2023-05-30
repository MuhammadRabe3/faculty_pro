<?php
require_once "db.php";
require_once "functions.php";

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

foreach ($_POST as $btn => $value)
{
	switch ($btn)
	{
		case "update_meeting_btn":
			$meeting_id = $_POST["meeting_id"];
			$select_old_row_stmt = $conn->prepare("SELECT * FROM p39_meeting WHERE meeting_id = ?");
			$select_old_row_stmt->bind_param("i", $meeting_id);
			if ($select_old_row_stmt->execute())
			{
				$select_old_row_result = $select_old_row_stmt->get_result();
				$old_row = "";
				$select_old_row = $select_old_row_result->fetch_assoc();
				foreach ($select_old_row as $key => $value)
				{
					$value = empty($value) ? "Null" : $value;
					$old_row .= empty($old_row) ? $value : ", " . $value;
				}
				$old_row = "(" . $old_row . ")";

				$meeting_number = clean_data($_POST["meeting_number"]);
				$meeting_date = empty($_POST["meeting_date"]) ? NULL : clean_data($_POST["meeting_date"]);
				$meeting_month = clean_data($_POST["meeting_month"]);
				$meeting_year = clean_data($_POST["meeting_year"]);
//			$meeting_status = $_POST["meeting_status"];
				$meeting_update_stmt = $conn->prepare("UPDATE
                                              `p39_meeting`
                                            SET
                                              `meeting_number` = ?,
                                              `meeting_date` = ?,
                                              `meeting_month` = ?,
                                              `meeting_year` = ?
                                            WHERE
                                                meeting_id = ?");
				$meeting_update_stmt->bind_param("isiii",
					$meeting_number,
					$meeting_date,
					$meeting_month,
					$meeting_year,
					$meeting_id);
				$meeting_update_stmt->execute();

				$select_old_row_stmt->execute();
				$select_new_row_result = $select_old_row_stmt->get_result();
				$new_row = NULL;
				$select_new_row = $select_new_row_result->fetch_assoc();
				foreach ($select_new_row as $key => $value)
				{
					$value = empty($value) ? "Null" : $value;
					$new_row .= empty($new_row) ? $value : ", " . $value;
				}
				$new_row = "(" . $new_row . ")";
				$transaction_type = "Edit";
				$insert_transaction = $conn->prepare("INSERT INTO `p39_meeting_transaction`
                                                            (`meeting_id`, `old_row`, `new_row`, `made_by`, `transaction_type`)
                                                        VALUES
                                                            (?, ?, ?, ?, ?)");
				$insert_transaction->bind_param("issis",
												$_POST["meeting_id"],
												$old_row,
													$new_row,
													$_SESSION["user_id"],
													$transaction_type);
				$insert_transaction->execute();
			}
			header("location: meetings.php", true, 303);
			break;

		case "update_formation_btn":
			$formation_number = clean_data($_POST["formation_number"]);
			$formation_update_stmt = $conn->prepare("UPDATE 
    															p39_formation 
															SET 
															    formation_number = ?, 
															    start_year = ? 
															WHERE 
															    formation_id = ?");
			$formation_update_stmt->bind_param("iii",
												$formation_number,
												$_POST["start_year"],
													$_POST["formation_id"]);
			$formation_update_stmt->execute();
			header("location: formation.php", true, 303);
			break;

		case "update_member_btn":
			$name = clean_data($_POST["name"]);
			$email = clean_data($_POST["email"]);
			$password = clean_data($_POST["password"]);
			$gender = clean_data($_POST["gender"]);
			$job_title = clean_data($_POST["job_title"]);
			$job_type = clean_data($_POST["job_type"]);
			$job_rank = clean_data($_POST["job_rank"]);
			$department = clean_data($_POST["department"]);
			$is_admin = clean_data($_POST["is_admin"]);
			$is_enabled = clean_data($_POST["is_enabled"]);
			$select_old_row_stmt = $conn->prepare("SELECT * FROM p39_users WHERE user_id = ?");
			$select_old_row_stmt->bind_param("i", $_POST["user_id"]);
			if ($select_old_row_stmt->execute())
			{
				$select_old_row_result = $select_old_row_stmt->get_result();
				$old_row = "";
				$select_old_row = $select_old_row_result->fetch_assoc();
				foreach ($select_old_row as $key => $value)
				{
					$value = empty($value) ? "Null" : $value;
					$old_row .= empty($old_row) ? $value : ", " . $value;
				}
				$old_row = "(" . $old_row . ")";
				if (empty($password)) {
					$member_update_stmt = $conn->prepare("UPDATE
                                                            `p39_users`
                                                        SET
                                                            `name` = ?,
                                                            `email` = ?,
                                                            `job_title` = ?,
                                                            `job_type_id` = ?,
                                                            `job_rank_id` = ?,
                                                            `department_id` = ?,
                                                            `gender` = ?,
                                                            `is_admin` = ?,
                                                            `is_enabled` = ?
                                                        WHERE
                                                            user_id = ?");
					$member_update_stmt->bind_param("sssiiisiii",
						$name,
						$email,
						$job_title,
						$job_type,
						$job_rank,
						$department,
						$gender,
						$is_admin,
						$is_enabled,
						$_POST["user_id"]);
				} else {
					$hash = password_hash($password, PASSWORD_DEFAULT);
					$member_update_stmt = $conn->prepare("UPDATE
                                                            `p39_users`
                                                        SET
                                                            `name` = ?,
                                                            `email` = ?,
                                                            `password` = ?,
                                                            `job_title` = ?,
                                                            `job_type_id` = ?,
                                                            `job_rank_id` = ?,
                                                            `department_id` = ?,
                                                            `gender` = ?,
                                                            `is_admin` = ?,
                                                            `is_enabled` = ?
                                                        WHERE
                                                            user_id = ?");
					$member_update_stmt->bind_param("ssssiiisiii",
						$name,
						$email,
						$hash,
						$job_title,
						$job_type,
						$job_rank,
						$department,
						$gender,
						$is_admin,
						$is_enabled,
						$_POST["user_id"]);
				}
				if ($member_update_stmt->execute())
				{
					// Adding picture attachment
					$pic_allowed_formats = array("png", "gif", "jpeg", "jpg");
					$uploaded_pictures = Upload("member_picture", "images/members/", $pic_allowed_formats);
					if (!empty($uploaded_pictures)) {
						foreach ($uploaded_pictures as $key => $value) {
							$picture_stmt = $conn->prepare("UPDATE 
                                                                    	p39_users 
	                                                                SET 
	                                                                    image = ? 
	                                                                WHERE 
	                                                                    user_id = ?");
							$picture_stmt->bind_param("ss", $value, $_POST["user_id"]);
							$picture_stmt->execute();
						}
					}
				}
			}
			$select_old_row_stmt->execute();
			$select_new_row_result = $select_old_row_stmt->get_result();
			$new_row = NULL;
			$select_new_row = $select_new_row_result->fetch_assoc();
			foreach ($select_new_row as $key => $value)
			{
				$value = empty($value) ? "Null" : $value;
				$new_row .= empty($new_row) ? $value : ", " . $value;
			}
			$new_row = "(" . $new_row . ")";
			$transaction_type = "Edit";
			$insert_transaction = $conn->prepare("INSERT INTO `p39_user_transaction`
                                                            (`user_id`, `old_row`, `new_row`, `made_by`, `transaction_type`)
                                                        VALUES
                                                            (?, ?, ?, ?, ?)");
			$insert_transaction->bind_param("issis",
				$_POST["user_id"],
				$old_row,
				$new_row,
				$_SESSION["user_id"],
				$transaction_type);
			$insert_transaction->execute();
			header("location: members.php", true, 303);
			break;

		case "update_subject_btn":
			$order_id = (empty($_POST["subject_order"]) ? NULL : clean_data($_POST["subject_order"]));
			$subject_name = clean_data($_POST["subject_name"]);
			$subject_details = (empty($_POST["subject_details"]) ? null : clean_data($_POST["subject_details"]));
			$subject_type = clean_data($_POST["subject_type"]);
			$subject_comments = (empty($_POST["subject_comments"]) ? null : clean_data($_POST["subject_comments"]));

			$select_old_row_stmt = $conn->prepare("SELECT * FROM p39_subject WHERE subject_id = ?");
			$select_old_row_stmt->bind_param("i", $_POST["subject_id"]);
			if ($select_old_row_stmt->execute())
			{
				$select_old_row_result = $select_old_row_stmt->get_result();
				$old_row = "";
				$select_old_row = $select_old_row_result->fetch_assoc();
				foreach ($select_old_row as $key => $value)
				{
					$value = empty($value) ? "Null" : $value;
					$old_row .= empty($old_row) ? $value : ", " . $value;
				}
				$old_row = "(" . $old_row . ")";
				$subject_update_stmt = $conn->prepare("UPDATE 
    																`p39_subject` 
																SET 
																    `order_id`= ?,
																    `subject_name`= ?,
																    `subject_details`= ?,
																    `subject_type_id`= ?,
																    `comments`= ? 
																WHERE subject_id = ?");
				$subject_update_stmt->bind_param("issisi",
													$order_id,
													$subject_name,
													$subject_details,
													$subject_type,
													$subject_comments,
													$_POST["subject_id"]);
				$subject_update_stmt->execute();
			}
			$select_old_row_stmt->execute();
			$select_new_row_result = $select_old_row_stmt->get_result();
			$new_row = NULL;
			$select_new_row = $select_new_row_result->fetch_assoc();
			foreach ($select_new_row as $key => $value)
			{
				$value = empty($value) ? "Null" : $value;
				$new_row .= empty($new_row) ? $value : ", " . $value;
			}
			$new_row = "(" . $new_row . ")";
			$transaction_type = "Edit";
			$insert_transaction = $conn->prepare("INSERT INTO `p39_subject_transaction`
                                                            (`subject_id`, `old_row`, `new_row`, `made_by`, `transaction_type`)
                                                        VALUES
                                                            (?, ?, ?, ?, ?)");
			$insert_transaction->bind_param("issis",
				$_POST["subject_id"],
				$old_row,
				$new_row,
				$_SESSION["user_id"],
				$transaction_type);
			$insert_transaction->execute();
			header("location: current_meeting_subject.php?mid={$_POST['meeting_id']}", true, 303);
			break;

		case "update_decision_btn":
			$decision_type = clean_data($_POST["decision_type"]);
			$needs_action = clean_data($_POST["needs_action"]);
			$action_to = (empty($_POST["is_action_done"]))
				? NULL
				: clean_data($_POST["action_to"]);
			$is_action_done = clean_data($_POST["is_action_done"]);
			$decision_details = (empty($_POST["decision_details"])
				? null
				: clean_data($_POST["decision_details"]));
			$decision_comments = (empty($_POST["decision_comments"])
				? null
				: clean_data($_POST["decision_comments"]));
			switch ($_POST["needs_action"])
			{
				case "0":
					$needs_action = 0;
					$action_to = NULL;
					$is_action_done = NULL;
					break;

				case "1":
					$needs_action = 1;
					$action_to = $_POST["action_to"];
					$is_action_done = 0;
					break;
			}
			$decision_update_stmt = $conn->prepare("UPDATE 
    															p39_decision 
															SET 
															    decision_details = ?,
															    decision_type_id = ?, 
															    needs_action = ?, 
															    action_to = ?, 
															    is_action_done = ?,
															    comments = ?
															WHERE 
															    decision_id = ?");
			$decision_update_stmt->bind_param("siisisi",
												$decision_details,
												$decision_type,
													$needs_action,
													$action_to,
													$is_action_done,
													$decision_comments,
													$_POST["decision_id"]);
			$decision_update_stmt->execute();
			header("location: current_meeting_subject.php?mid={$_POST['meeting_id']}", true, 303);
			break;

		case "update_vote_btn":
			$vote = clean_data($_POST["vote"]);
			$update_vote_stmt = $conn->prepare("UPDATE 
    														p39_vote 
														SET 
														    vote_type_id = ? 
														WHERE 
														    subject_id = ? 
														  AND 
														    user_id = ?");
			$update_vote_stmt->bind_param("iii", $vote, $_POST["subject_id"], $_SESSION["user_id"]);
			$update_vote_stmt->execute();
			$current_meeting_stmt = $conn->prepare("SELECT meeting_id FROM p39_meeting WHERE is_current = 1");
			$current_meeting_stmt->execute();
			$current_meeting_result = $current_meeting_stmt->get_result();
			$current_meeting_row = $current_meeting_result->fetch_assoc();
			$current_meeting = $current_meeting_row["meeting_id"];
			header("location: current_meeting_subject.php?mid=$current_meeting", true, 303);
			break;

		case "update_decision_action_btn":
			$is_action_done = clean_data($_POST["is_action_done"]);
			$decision_action_update_stmt = $conn->prepare("UPDATE 
    																	p39_decision 
																	SET 
																	    is_action_done = ? 
																	WHERE 
																	    subject_id = ?");
			$decision_action_update_stmt->bind_param("ii", $is_action_done, $_POST["subject_id"]);
			$decision_action_update_stmt->execute();
			header("location: executive_decisions.php", true, 303);
			break;
	}
}