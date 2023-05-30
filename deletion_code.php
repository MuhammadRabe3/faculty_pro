<?php
require_once "db.php";
require_once "functions.php";

if (session_status() === PHP_SESSION_NONE)
{
	session_start();
}

foreach ($_POST as $key=>$value)
{

	switch ($key)
	{
		case "delete_decision_btn":
			$delete_decision_stmt = Delete($conn, "p39_decision", "decision_id = {$_POST['decision_id']}");
			$delete_decision_stmt->execute();
			header("location: current_meeting_subject.php?mid={$_POST['meeting_id']}",
				true, 303);
			break;

		case "delete_subject_btn":
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
				$delete_subject_stmt = Delete($conn, "p39_subject", "subject_id = {$_POST['subject_id']}");
				if ($delete_subject_stmt->execute())
				{
					$transaction_type = "Delete";
					$new_row = NULL;
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
				}
			}
			header("location: current_meeting_subject.php?mid={$_POST['meeting_id']}",
				true, 303);
			break;

		case "delete_subject_attachment_btn":
			$delete_att_stmt = Delete($conn, "p39_subject_attachment",
				"attachment_id = {$_POST['attachment_id']}");
			$delete_att_stmt->execute();
			header("location: subject_attachment.php?sid={$_POST['subject_id']}",
				true, 303);
			break;

		case "delete_subject_picture_btn":
			$delete_pic_stmt = Delete($conn, "p39_subject_picture", "picture_id = {$_POST['picture_id']}");
			$delete_pic_stmt->execute();
			header("location: subject_attachment.php?sid={$_POST['subject_id']}",
				true, 303);
			break;

		case "delete_meeting_attachment_btn":
			$delete_att_stmt = Delete($conn, "p39_meeting_attachment",
				"attachment_id = {$_POST['attachment_id']}");
			$delete_att_stmt->execute();
			header("location: meeting_attachment.php?mid={$_POST['meeting_id']}",
				true, 303);
			break;

		case "delete_meeting_btn":
			$select_old_row_stmt = $conn->prepare("SELECT * FROM p39_meeting WHERE meeting_id = ?");
			$select_old_row_stmt->bind_param("i", $_POST["meeting_id"]);
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
				$delete_meeting_stmt = Delete($conn, "p39_meeting", "meeting_id = {$_POST['meeting_id']}");
				if ($delete_meeting_stmt->execute())
				{
					$transaction_type = "Delete";
					$new_row = NULL;
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
			}
			header("location: meetings.php", true, 303);
			break;

		case "delete_decision_attachment_btn":
			$delete_att_stmt = Delete($conn, "p39_decision_attachment",
				"attachment_id = {$_POST['attachment_id']}");
			$delete_att_stmt->execute();
			header("location: executive_decisions.php?f=1", true, 303);
			break;
	}
}
