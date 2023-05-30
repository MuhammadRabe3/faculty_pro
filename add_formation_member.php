<?php
require_once "db.php";
require_once "functions.php";
?>
<!DOCTYPE html>
<html lang="en">
<?php
Head("أعضاء التشكيل الحالي");
?>

<body dir="rtl">
    <?php Headers(); ?>
    <?php if (is_admin()) { ?>
        <?php Nav();
        $formation_number_stmt = $conn->prepare("SELECT 
                                                            formation_number, 
                                                            formation_id
                                                        FROM 
                                                            p39_formation 
                                                        WHERE 
                                                            is_current = 1");
        $formation_number_stmt->execute();
        $formation_number_result = $formation_number_stmt->get_result();
        $formation_number_row = $formation_number_result->fetch_assoc();
        $formation_id = $formation_number_row["formation_id"];
        $formation_number = $formation_number_row["formation_number"];
        $formation_number_stmt->close();
        ?>
        <main class="add-formation-member-page ">
            <div class="container">
                <div class="title">
                    <h1>اضافة و تعديل الاعضاء للتشكيل السنوي الحالي رقم: <?= $formation_number ?></h1>
                </div>
	            <?php
	            $formation_user_id_stmt = $conn->prepare("SELECT 
                                                                    user_id 
                                                                FROM 
                                                                    p39_formation_user 
                                                                WHERE 
                                                                    formation_id = (
                                                                        SELECT 
                                                                            formation_id 
                                                                        FROM 
                                                                            p39_formation 
                                                                        WHERE
                                                                            is_current = 1
                                                                    ) ");
	            $formation_user_id_stmt->execute();
	            $formation_user_id_result = $formation_user_id_stmt->get_result();
	            $formation_users = array();
	            while ($formation_user_id_row = $formation_user_id_result->fetch_assoc())
	            {
		            $formation_users[] = $formation_user_id_row["user_id"];
	            }
	            $formation_user_id_stmt->close();

	            $formation_users_stmt = $conn->prepare("SELECT 
                                                                    user_id, 
                                                                    name,
                                                                    job_title
                                                                FROM 
                                                                    p39_users 
                                                                WHERE 
                                                                    is_admin = 0 
                                                                  AND
                                                                    is_enabled = 1");
	            $formation_users_stmt->execute();
	            $formation_users_result = $formation_users_stmt->get_result();
	            if ($formation_users_result->num_rows > 0) : ?>
                    <form action="addition_code.php" method="post">
                        <h4>اختر من أعضاء النظام الحاليين للإضافة للتشكيل</h4>
                        <table>
                            <thead>
                                <th>اسم العضو</th>
                                <th>اختيار</th>
                            </thead>
                            <tbody>
                                <?php while ($formation_users_row = $formation_users_result->fetch_assoc()) { ?>
                                    <?php if (in_array($formation_users_row["user_id"], $formation_users)) { ?>
                                        <tr>
                                            <td>
                                                 <?= $formation_users_row["name"] ?>
                                            </td>
                                            <td>
                                                <input type="hidden" name="<?=$formation_users_row['user_id']?>" value="0">
                                                <input type="checkbox" name="<?=$formation_users_row['user_id']?>" class="check"
                                                       value="1" checked>
                                                <input type="hidden"
                                                       name="job_title-<?=$formation_users_row['user_id']?>"
                                                       value="<?=$formation_users_row['job_title']?>"
                                            </td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr>
                                            <td>
                                                 <?= $formation_users_row["name"] ?>
                                            </td>
                                            <td>
                                                <input type="hidden" name="<?=$formation_users_row['user_id']?>" value="0">
                                                <input type="checkbox" name="<?=$formation_users_row['user_id']?>" class="check"
                                                       value="1">
                                                <input type="hidden"
                                                       name="job_title-<?=$formation_users_row['user_id']?>"
                                                       value="<?=$formation_users_row['job_title']?>"
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>اختيار الكل</td>
                                    <td><input type="checkbox" class="select-all"/></td>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="formation_id" value="<?= $formation_id ?>">
                        <button type="submit" class="btn-basic" name="add_formation_member_btn">تسجيل</button>
                    </form>
                <?php else: ?>
                    <div class="current-formation">
                        <main id="empty" class="empty-formation">
                            <h4>لا يوجد أعضاء مسجلين</h4>
                        </main>
                    </div>
                <?php endif;?>
            </div>
        </main>
    <?php } ?>

    <?php footer(); ?>

    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>
    <script src="./js/select_all_button.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>
