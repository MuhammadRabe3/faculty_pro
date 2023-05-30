<?php
require_once "db.php";
require_once "functions.php";

if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php Head("تسجيل الأعضاء"); ?>
<body dir="rtl">
    <?php Headers(); ?>
    <?php if (isset($_POST["attendance_btn"])) : ?>
        <?php Nav();
	    $attendance_users_stmt = $conn->prepare("SELECT 
                                                            user_id 
                                                        FROM 
                                                            p39_attendance 
                                                        WHERE 
                                                            meeting_id = ?");
	    $attendance_users_stmt->bind_param("i", $_POST["meeting_id"]);
	    $attendance_users_stmt->execute();
	    $attendance_users_result = $attendance_users_stmt->get_result();
	    $attendance_users = array();
	    while ($attendance_users_row = $attendance_users_result->fetch_assoc())
	    {
		    $attendance_users[] = $attendance_users_row["user_id"];
	    }
	    $attendance_users_stmt->close();

        $users_stmt = $conn->prepare("SELECT
                                                fu.user_id as uid,
                                                u.name as n
                                            FROM
                                                p39_formation_user AS fu
                                            JOIN p39_formation AS f
                                            ON
                                                f.formation_id = fu.formation_id 
                                                    AND 
                                                f.is_current = 1
                                            JOIN p39_users AS u
                                            ON
                                                fu.user_id = u.user_id");
        $users_stmt->execute();
        $users_result = $users_stmt->get_result(); ?>
        <main class="meeting-attendance-page">
            <div class="container">
                <div class="title">
                    <h1>تسجيل الحضور</h1>
                </div>
                <form action="addition_code.php" method="post">
                    <table class="attend-table">
                        <thead>
                        <th>اسم العضو</th>
                        <th>الحضور</th>
                        </thead>
                        <tbody>
                        <?php while ($users_row = $users_result->fetch_assoc()) { ?>
                            <?php if (!in_array($users_row["uid"], $attendance_users)) { ?>
                                <tr>
                                    <td><?= $users_row["n"] ?></td>
                                    <td>
                                        <input type="hidden" name="<?= $users_row['uid'] ?>" value="0">
                                        <input type="checkbox" class="check"
                                               name="<?= $users_row['uid'] ?>" value="1">
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td><?= $users_row["n"] ?></td>
                                    <td>
                                        <input type="hidden" name="<?= $users_row['uid'] ?>" value="0">
                                        <input type="checkbox" class="check" checked
                                               name="<?= $users_row['uid'] ?>" value="1">
                                    </td>
                                </tr>
                                <?php } ?>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>اختيار الكل</td>
                            <td><input type="checkbox" class="select-all"/></td>
                        </tr>
                        </tfoot>
                    </table>
                    <input type="hidden" value="<?=$_POST['meeting_id']?>" name="meeting_id">
                    <button type="submit" name="attendance_btn" class="btn-basic">تسجيل الحضور</button>
                </form>
            </div>
        </main>
    <?php else :
        header("location: index.php", true, 303);
    endif; ?>
    <?php footer(); ?>

    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>
    <script src="./js/select_all_button.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>