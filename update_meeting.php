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
Head("تعديل المجلس");
?>

<body dir="rtl">
<?php
Headers();
Nav();
if (is_admin()):
	$meeting_subjects_stmt = $conn->prepare("SELECT * FROM p39_subject WHERE meeting_id = ?");
	$meeting_subjects_stmt->bind_param("i", $_POST["meeting_id"]);
	$meeting_subjects_stmt->execute();
	$meeting_subjects_result = $meeting_subjects_stmt->get_result();
	$meeting_subjects_exist = $meeting_subjects_result->num_rows > 0;
	$meeting_stmt = $conn->prepare("SELECT * FROM p39_meeting WHERE meeting_id = ?");
	$meeting_stmt->bind_param("i", $_POST["meeting_id"]);
	$meeting_stmt->execute();
	$meeting_result = $meeting_stmt->get_result();
	$meeting_row = $meeting_result->fetch_assoc();
	?>
    <main class="add-member-page">
        <div class="container">
            <!-- عنوان الصفحة -->
            <div class="title">
                <h1>تعديل بيانات المجلس</h1>
            </div>
            <form class="box" method="post" action="update_code.php" enctype="multipart/form-data">
                <div class="col">
                    <div class="row">
                        <h4>رقم المجلس</h4><input type="number" name="meeting_number" placeholder="رقم المجلس" required
                                                  value="<?= $meeting_row['meeting_number'] ?>"/>
                    </div>
                    <div class="row">
                        <h4>تاريخ انعقاد المجلس</h4><input type="date" name="meeting_date" required
                                                           value="<?= $meeting_row['meeting_date'] ?>"/>
                    </div>

                    <div class="row">
                        <h4>الشهر</h4>
                        <div class="select-basic">
                            <select name="meeting_month" required>
                                <option>اختر</option>
								<?php for ($i = 9; $i <= 12; $i++) {
									if ($i == $meeting_row["meeting_month"]) {
										echo "<option value='$i' selected>$i</option>";
									} else {
										echo "<option value='$i'>$i</option>";
									}
								}
                                for ($i = 1; $i <= 8; $i++) {
                                    if ($i == $meeting_row["meeting_month"]) {
                                        echo "<option value='$i' selected>$i</option>";
                                    } else {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h4>السنة</h4>
                        <div class="select-basic">
                            <select name="meeting_year" required>
                                <option value="">اختر</option>
								<?php
								$year = date("Y");
								for ($i = $year - 4; $i <= $year + 4; $i++) {
									if ($i == $meeting_row["meeting_year"]) {
										echo "<option value='$i' selected>$i</option>";
									} else {
										echo "<option value='$i'>$i</option>";
									}
								}
								?>
                            </select>
                        </div>
                    </div>
                    <div class="row sp2-row">
                        <div class="col">
                            <form method="post" action="update_code.php">
                                <input type="hidden" value="<?= $_POST['meeting_id'] ?>" name="meeting_id">
                                <button type="submit" class="btn-basic" name="update_meeting_btn">تعديل بيانات المجلس
                                </button>
                            </form>
                        </div>
                        <div class="col">
                            <form method="post" action="deletion_code.php">
                                <input type="hidden" value="<?= $_POST['meeting_id'] ?>" name="meeting_id">
								<?php if (!$meeting_subjects_exist) { ?>
                                    <button type="submit" class="btn-basic" name="delete_meeting_btn">
                                        حذف المجلس
                                    </button>
								<?php } else { ?>
                                    <button type="button" class="btn-basic disabled" disabled
                                            title="لا يمكن حذف المجلس عند وجود موضوعات بداخله">
                                        حذف المجلس
                                    </button>
								<?php } ?>
                            </form>
                        </div>
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