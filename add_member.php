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
Head("اضافة عضو");
?>

<body dir="rtl">
<?php
Headers();
Nav();
if (is_admin()):
	?>
    <main class="add-member-page">
        <div class="container">
            <!-- عنوان الصفحة -->
            <div class="title">
                <h1>إضافة عضو جديد</h1>
            </div>
            <form class="box" method="post" action="addition_code.php" enctype="multipart/form-data">
	            <?php if (@$_SESSION["error"]["add"]) { ?>
                    <p class="login-error">يرجى إدخال جميع الحقول</p>
		            <?php unset($_SESSION["error"]["add"]);
	            }
                if (!empty($_SESSION["error"]["login"]["email"])) { ?>
                    <p class="login-error">
                        <?php echo $_SESSION["error"]["login"]["email"];
                        unset($_SESSION["error"]["login"]["email"]); ?>
                    </p>
                <?php } ?>
                <div class="col">
                    <div class="row">
                        <h4>الاسم بالكامل</h4><input type="text" name="name" placeholder="الاسم بالكامل" required/>
                    </div>
                    <div class="row sp-row">
                        <h4>النوع</h4>
                        <div class="row ">
                            <h4>ذكر</h4><input type="radio" value="M" name="gender" required>
                            <h4>أنثى</h4><input type="radio" value="F" name="gender" required>
                        </div>
                    </div>
                    <div class="row">
                        <h4>البريد الالكتروني</h4><input type="email" name="email" placeholder="البريد الالكتروني"
                                                         required/>
                    </div>
                    <div class="row">
                        <h4>كلمة السر</h4>
                        <div class="password-box">
                            <input type="password" name="password" placeholder="كلمة السر" required/> <i
                                    class="fa-solid fa-eye-slash"></i>
                        </div>
                    </div>
                    <!--          <div class="row">-->
                    <!--            <h4>رقم التليفون</h4><input type="text" placeholder="رقم التليفون" />-->
                    <!--          </div>-->
                    <div class="row">
                        <h4>المسمى الوظيفي</h4><input type="text" name="job_title" placeholder="المسمى الوظيفي"
                                                      required/>
                    </div>
                    <div class="row">
                        <h4>الفئة الوظيفية</h4>
                        <div class="select-basic">
                            <select name="job_type" >
                                <option value="">اختر</option>
								<?php
								// Get All Job Types
								$job_types_stmt = $conn->prepare("SELECT * FROM p39_job_type");
								$job_types_stmt->execute();
								$job_types_result = $job_types_stmt->get_result();
								while ($job_types_row = $job_types_result->fetch_assoc()) {
									?>
                                    <option value="<?= $job_types_row['job_type_id'] ?>"><?= $job_types_row["job_type_name"] ?></option>
									<?php
								}
								$job_types_stmt->close();
								?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h4>الدرجة الوظيفية</h4>
                        <div class="select-basic">
                            <select name="job_rank" required>
                                <option value="">اختر</option>
								<?php
								// Get All Job Types
								$job_ranks_stmt = $conn->prepare("SELECT * FROM p39_job_rank");
								$job_ranks_stmt->execute();
								$job_ranks_result = $job_ranks_stmt->get_result();
								while ($job_ranks_row = $job_ranks_result->fetch_assoc()) {
									?>
                                    <option value="<?= $job_ranks_row['job_rank_id'] ?>"><?= $job_ranks_row["job_rank_name"] ?></option>
									<?php
								}
								$job_ranks_stmt->close();
								?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h4>القسم العلمي</h4>
                        <div class="select-basic">
                            <select name="department" required>
                                <option value="">اختر</option>
								<?php
								// Get All Job Types
								$departments_stmt = $conn->prepare("SELECT * FROM p39_department");
								$departments_stmt->execute();
								$departments_result = $departments_stmt->get_result();
								while ($departments_row = $departments_result->fetch_assoc()) {
									?>
                                    <option value="<?= $departments_row['department_id'] ?>"><?= $departments_row["department_name"] ?>
                                    </option>
									<?php
								}
								$departments_stmt->close();
								?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h4>الصلاحية</h4>
                        <div class="select-basic">
                            <select name="is_admin" required>
                                <option value="">اختر</option>
                                <option value="0">عضو مجلس</option>
                                <option value="1">أمين مجلس (ادمن)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h4>صورة العضو</h4>
                        <div class="upload">
                            <div class="btn-basic">
                                <label for="up1">
                                    رفع صورة
                                    <i class="fa-solid fa-upload"></i>
                                    <input id="up1" type="file" class="upload-button" name="member_picture[]"
                                           accept="image/png, image/gif, image/jpeg"/>
                                </label>
                            </div>
                            <div class="file-list"></div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn-basic" name="add_member_btn">اضافة عضو جديد</button>
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
<script src="./js/showpassword.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>