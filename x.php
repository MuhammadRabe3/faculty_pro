<?php
require_once "functions.php";
if (session_status() === PHP_SESSION_NONE)
{
	session_start();
}

$job_title = array();
foreach ($_POST as $key => $value)
{
	if ($value != "0" AND $value != "1" AND !empty($value))
	{
		$user_array = explode("-", $key);
		if (!empty($user_array[1]))
		{
			$job_title[$user_array[1]] = $value;
		}
	}
}

echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<pre>";
print_r($job_title);
echo "</pre>";

$formation_users = array(2);
foreach ($_POST as $key => $value)
{
	if (in_array($key, $formation_users))
	{
		switch ($value)
		{
			# User should be in the formation
			case "1":
				$if1 = array($key => $value);
				break;

			case "0":
				$if0 = array($key=>$value);
				break;
		}
	}
	elseif ($value = 1)
	{
		$key = (int)$key;
		$elseif = array($key=>$value);

	}
}
$attachment_allowed_types = array("pdf", "png", "gif", "jpeg", "jpg");
$uploaded_attachments = Upload("decision_attachment", "images/", $attachment_allowed_types);

echo "<pre>";
print_r($if1);
echo "</pre>";

echo "<pre>";
print_r($if0);
echo "</pre>";

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<pre>";
print_r($_FILES);
echo "</pre>";

echo "<pre>";
print_r($uploaded_attachments);
echo "</pre>";