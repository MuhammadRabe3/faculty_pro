<?php
require_once "db.php";
require_once "functions.php";

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

foreach ($_POST as $key=>$value)
{
	switch ($key)
	{
		case "confirm_btn":
			$confirm_stmt = $conn->prepare("UPDATE p39_meeting SET status = ? WHERE meeting_id = ?");
			$status = "confirmed";
			$confirm_stmt->bind_param("si", $status, $_POST["meeting_id"]);
			$confirm_stmt->execute();
			header("location: meetings.php", true, 303);
			break;

		case "pending_btn":
			$pending_stmt = $conn->prepare("UPDATE p39_meeting SET status = ? WHERE meeting_id = ?");
			$status = "pending";
			$pending_stmt->bind_param("si", $status, $_POST["meeting_id"]);
			$pending_stmt->execute();
			header("location: meetings.php", true, 303);
			break;

		case "finished_btn":
			$finished_stmt = $conn->prepare("UPDATE p39_meeting SET status = ? WHERE meeting_id = ?");
			$status = "finished";
			$finished_stmt->bind_param("si", $status, $_POST["meeting_id"]);
			$finished_stmt->execute();
			header("location: meetings.php", true, 303);
			break;

		case "past_btn":
			$finished_stmt = $conn->prepare("UPDATE p39_meeting SET is_current = ? WHERE meeting_id = ?");
			$is_current = 0;
			$finished_stmt->bind_param("si", $is_current, $_POST["meeting_id"]);
			$finished_stmt->execute();
			header("location: meetings.php", true, 303);
			break;

		case "past_formation_btn":
			$is_current = 0;
			$past_formation_stmt = $conn->prepare("UPDATE p39_formation SET is_current = ? WHERE formation_id = ?");
			$past_formation_stmt->bind_param("ii", $is_current, $_POST["formation_id"]);
			$past_formation_stmt->execute();
			header("location: formation.php", true, 303);
			break;

		case "show_btn":
			$is_showed = 1;
			$show_meeting_stmt = $conn->prepare("UPDATE p39_meeting SET is_showed = ? WHERE meeting_id = ?");
			$show_meeting_stmt->bind_param("ii", $is_showed, $_POST["meeting_id"]);
			$show_meeting_stmt->execute();
			header("location: meetings.php", true, 303);
			break;

		case "hide_btn":
			$is_showed = 0;
			$hide_meeting_stmt = $conn->prepare("UPDATE p39_meeting SET is_showed = ? WHERE meeting_id = ?");
			$hide_meeting_stmt->bind_param("ii", $is_showed, $_POST["meeting_id"]);
			$hide_meeting_stmt->execute();
			header("location: meetings.php", true, 303);
			break;

	}
}