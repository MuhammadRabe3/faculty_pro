<?php
require_once "db.php";
require_once "functions.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
Head("تعديل موضوع");
?>

<body dir="rtl">
<?php Headers(); ?>
<?php if (is_admin()): ?>
    <?php Nav(); ?>
    <main class="add-member-page update-subject-page">
        <div class="container">
            <!-- عنوان الصفحة -->
            <div class="title">
                <h1>تعديل الموضوع</h1>
            </div>
            <form class="box" method="post" action="update_code.php" enctype="multipart/form-data">
                <div class="col">
                    <?php
                    $subject_decision_stmt = $conn->prepare("SELECT decision_id FROM p39_decision WHERE subject_id = ?");
                    $subject_decision_stmt->bind_param("i", $_POST["subject_id"]);
                    $subject_decision_stmt->execute();
                    $subject_decision_result = $subject_decision_stmt->get_result();
                    $subject_decision_exists = $subject_decision_result->num_rows > 0;
                    $subject_decision_stmt->close();

                    $subject_att_stmt = $conn->prepare("SELECT attachment_id FROM p39_subject_attachment WHERE subject_id = ?");
                    $subject_att_stmt->bind_param("i", $_POST["subject_id"]);
                    $subject_att_stmt->execute();
                    $subject_att_result = $subject_att_stmt->get_result();
                    $subject_att_exist = $subject_att_result->num_rows > 0;
                    $subject_att_stmt->close();

                    $subject_pic_stmt = $conn->prepare("SELECT picture_id FROM p39_subject_picture WHERE subject_id = ?");
                    $subject_pic_stmt->bind_param("i", $_POST["subject_id"]);
                    $subject_pic_stmt->execute();
                    $subject_pic_result = $subject_pic_stmt->get_result();
                    $subject_pic_exist = $subject_pic_result->num_rows > 0;
                    $subject_pic_stmt->close();

                    $subject_stmt = $conn->prepare("SELECT 
                                                                *
                                                            FROM 
                                                                p39_subject 
                                                            WHERE 
                                                                subject_id = ?");
                    $subject_stmt->bind_param("i", $_POST["subject_id"]);
                    $subject_stmt->execute();
                    $subject_result = $subject_stmt->get_result();
                    $subject_row = $subject_result->fetch_assoc();
                    ?>

                    <div class="row">
                        <h4>تحديد الاولوية</h4>
                        <div class="select-basic">
                            <select name="subject_order" required>
                                <option>اختر</option>
	                            <?php
	                            $order_stmt = $conn->prepare("SELECT order_id FROM p39_subject WHERE order_id > 0 
                                                                    and meeting_id = ?");
	                            $order_stmt->bind_param("i", $subject_row["meeting_id"]);
	                            $order_stmt->execute();
	                            $order_result = $order_stmt->get_result();
	                            $order_array = array();
	                            while($order_row = $order_result->fetch_assoc())
	                            {
		                            $order_array[] = $order_row["order_id"];
	                            }
	                            $stmt1 = $conn->prepare("SELECT * FROM p39_subject WHERE meeting_id = ?");
	                            $stmt1->bind_param("i", $subject_row["meeting_id"]);
	                            $stmt1->execute();
	                            $result1 = $stmt1->get_result();
	                            $rows_num = $result1->num_rows;
	                            for($i = 1; $i <= $rows_num; $i++)
	                            {
		                            if(!in_array($i, $order_array))
		                            {
			                            echo "<option value='$i'>$i</option>";
		                            }
                                    elseif($subject_row["order_id"] == $i)
		                            {
			                            echo"<option value='$i' selected>$i</option>";
		                            }
	                            }
	                            ?>
                                <option value="NULL">إلغاء</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h4>عنوان الموضوع</h4><input type="text" name="subject_name" placeholder="عنوان الموضوع"
                                                     required value="<?= $subject_row['subject_name'] ?>"/>
                    </div>

                    <div class="row">
                        <h4>تصنيف الموضوع</h4>
                        <div class="select-basic">
                            <select name="subject_type" required>
                                <option>اختر</option>
		                        <?php
		                        // Get All Subject Types
		                        $subject_types_stmt = $conn->prepare("SELECT * FROM p39_subject_type");
		                        $subject_types_stmt->execute();
		                        $subject_types_result = $subject_types_stmt->get_result();
		                        while ($subject_types_row = $subject_types_result->fetch_assoc())
		                        { ?>
                                    <?php if($subject_types_row["subject_type_id"] == $subject_row["subject_type_id"]) { ?>
                                        <option value="<?= $subject_types_row['subject_type_id'] ?>" selected>
                                            <?= $subject_types_row["subject_type_name"] ?>
                                        </option>
                                    <?php } else { ?>
                                        <option value="<?= $subject_types_row['subject_type_id'] ?>">
                                            <?= $subject_types_row["subject_type_name"] ?>
                                        </option>
			                        <?php }
		                        }
		                        $subject_types_stmt->close(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <h4>تفاصيل الموضوع</h4>
                        <textarea name="subject_details"><?= $subject_row["subject_details"] ?></textarea>
                    </div>

                    <div class="row">
                        <h4>ملاحظات</h4>
                        <textarea name="subject_comments"><?= $subject_row["comments"] ?></textarea>
                    </div>
                    <div class="row sp2-row">
                        <form method="post" action="update_code.php">
                            <input type="hidden" name="subject_id" value="<?= $subject_row['subject_id'] ?>">
                            <input type="hidden" name="meeting_id" value="<?= $subject_row['meeting_id'] ?>">
                            <button type="submit" class="btn-basic" name="update_subject_btn">تعديل موضوع</button>
                        </form>
                        <?php if (!$subject_decision_exists && !$subject_att_exist && !$subject_pic_exist) { ?>
                            <!--<button type="button" class="btn-basic" data-open-modal>حذف الموضوع</button>
                            <dialog data-modal>
                                <form method="dialog">
                                    <button formmethod="dialog" type="submit" class="btn-basic">إلغاء</button>
                                    <button type="submit" class="btn-basic" name="delete_subject_btn">حذف</button>
                                </form>
                            </dialog>-->
                            <form method="post" action="deletion_code.php">
                                <input type="hidden" name="subject_id" value="<?= $subject_row['subject_id'] ?>">
                                <input type="hidden" name="meeting_id" value="<?= $subject_row['meeting_id'] ?>">
                                <button type="submit" class="btn-basic" name="delete_subject_btn">حذف الموضوع</button>
                            </form>
                        <?php } else { ?>
                            <button type="button" class="btn-basic disabled" disabled
                                    title="لا يمكن حذف موضوع له قرار أو مرفقات">حذف الموضوع</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </main>

<?php
endif;
footer();
?>

<!-- Js Scripts and Plugins -->
<script type="module" src="./js/main.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>