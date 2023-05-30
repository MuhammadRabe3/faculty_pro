<?php
require_once ("db.php");
require_once ("functions.php");

// start session if not started
if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}

// Make sure the user came here by pressing the button
if (isset($_POST["sign_in_btn"])):
    // check the email entered
    if (is_valid_email($_POST["email"]))
    {
        $user_email = $_POST["email"];
        $user_stmt = $conn->prepare("SELECT user_id, name, password, is_enabled, is_admin 
                                        FROM p39_users WHERE email = ?");
        $user_stmt->bind_param("s",$user_email);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        // check if the user's email exists in database
        if ($user_result->num_rows === 1)
        {
            // User's email exists in the database
            // Save user's record in an associative array
            $user_row = $user_result->fetch_assoc();
            $user_password = $_POST["password"];
            // Check if user is allowed to log into the system
            if ($user_row["is_enabled"] === 1)
            {
                // User is allowed to log into the system
                // Check user's password
                if (password_verify($user_password, $user_row["password"]))
                {
                    // Change user's session id in an attempt to prevent session fixation attacks
                    session_regenerate_id();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["name"] = $user_row["name"];
                    $_SESSION["user_id"] = $user_row["user_id"];
                    // Check if the hashed password needs to be rehashed
                    if (password_needs_rehash($user_row['password'], PASSWORD_DEFAULT))
                    {
                        $new_hash = password_hash($user_password, PASSWORD_DEFAULT);
                        $new_hash_stmt = $conn->prepare("UPDATE p39_users SET password = ? WHERE email = ?");
                        $new_hash_stmt->bind_param("ss", $new_hash, $user_email);
                        $new_hash_stmt->execute();
                    }
                    // Check if user is an admin
                    $_SESSION["admin"] = ($user_row["is_admin"] == 1 ?? true);
	                # Retrieve Formation IDs of the user currently signed in
	                $user_formation_ids_stmt = $conn->prepare("SELECT
                                                                        formation_id
                                                                    FROM
                                                                        p39_formation_user
                                                                    WHERE
                                                                        user_id = ?");
	                $user_formation_ids_stmt->bind_param("i", $_SESSION["user_id"]);
	                $user_formation_ids_stmt->execute();
	                $user_formation_ids_result = $user_formation_ids_stmt->get_result();
	                $user_formation_ids = array();
	                while ($user_formation_ids_row = $user_formation_ids_result->fetch_assoc())
	                {
		                 $user_formation_ids[] = $user_formation_ids_row["formation_id"];
	                }
					$_SESSION["formation_ids"] = $user_formation_ids;
	                $user_formation_ids_stmt->close();

                    // Unset errors regarding login -if any exists-, since user successfully signed in
                    if (!empty($_SESSION["error"]["login"]))
                    {
                        unset($_SESSION["error"]["login"]);
                    }
                    // Redirect user to the next page (Index page)
                    header("location:index.php");
                }
                else
                {
                    // Password doesn't match database, but I won't let the user know user exists for security concerns
                    $_SESSION["error"]["login"]["password"] = "Incorrect credentials, please try again";
                    header("location:login.php");
                }
            }
            else
            {
                // User is not allowed to log into the system
                $_SESSION["error"]["login"]["not_allowed"] = "You're not allowed to log into the system";
                header("location:login.php");
            }
        }
        else
        {
            // Email doesn't exist in database
            $_SESSION["error"]["login"]["user_not_found"] = "User not found, please contact system administrator";
            header("location:login.php");
        }
    }
    else
    {
        // Email doesn't meet the standards
        $_SESSION["error"]["login"]["email"] = "Incorrect email format";
        header("location:login.php");
    }
else:
    echo"You Need to use POST to view this page";
    if(@$_SESSION["loggedin"])
    {
        header("refresh:5; url=index.php");
    }
    else
    {
        header("refresh:5; url=login.php");
    }
endif;