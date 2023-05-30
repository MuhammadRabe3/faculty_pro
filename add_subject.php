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
Head("اضافة موضوع");
?>

<body dir="rtl">
<?php
Headers();
if (is_admin()):
	Nav(); ?>
    <main class="add-member-page">
        <div class="container">
            <!-- عنوان الصفحة -->
            <div class="title">
                <h1>إضافة موضوع جديد</h1>
            </div>
            <form class="box" method="post" action="addition_code.php" enctype="multipart/form-data">
                <div class="col">
                    <div class="row">
                        <h4>عنوان الموضوع</h4>
                        <input type="text" name="subject_name" placeholder="عنوان الموضوع" required/>
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
                                {
		                            ?>
                                    <option value="<?= $subject_types_row['subject_type_id'] ?>">
                                        <?= $subject_types_row["subject_type_name"] ?>
                                    </option>
		                            <?php
	                            }
	                            $subject_types_stmt->close(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h4>المرفقات الخاصة بالموضوع</h4>
                        <div class="upload">
                            <div class="btn-basic">
                                <label for="up1">
                                    رفع مرفق
                                    <i class="fa-solid fa-upload"></i>
                                    <input id="up1" type="file" name="subject_attachment[]" class="upload-button"
                                           accept="application/pdf, image/png, image/gif, image/jpeg" multiple/>
                                </label>
                            </div>
                            <div class="file-list"></div>
                        </div>
                    </div>

                    <div class="row">
                        <h4>تفاصيل الموضوع</h4>
                        <textarea name="subject_details"></textarea>
                    </div>
                    <div class="row">
                        <h4>رفع صورة داخل التفاصيل</h4>
                        <div class="upload">
                            <div class="btn-basic">
                                <label for="up2">
                                    رفع صورة
                                    <i class="fa-solid fa-upload"></i>
                                    <input id="up2" type="file" class="upload-button" name="subject_picture[]"
                                           accept="image/png, image/gif, image/jpeg" multiple/>
                                </label>
                            </div>
                            <div class="file-list"></div>
                        </div>
                    </div>
                    <div class="row">
                        <h4>ملاحظات</h4>
                        <textarea name="subject_comments"></textarea>
                    </div>
                    <!-- محاولة الدايالوج الفاشلة -->
                    <!-- أو لأ طلعت ناجحة، بعتذر لصاحب الكود -->
                    <!--<div class="row">
                        <button type="button" class="btn-basic" data-open-modal>اضافة موضوع جديد</button>
                        <dialog data-modal>
                            <form method="dialog">
                                <button formmethod="dialog" type="submit" class="btn-basic">Cancel</button>
                                <button type="submit" class="btn-basic" name="add_subject_btn">Submit</button>
                            </form>
                        </dialog>
                    </div>-->
                    <div class="row">
                        <input type="hidden" name="meeting_id" value="<?= $_POST['meeting_id'] ?>"/>
                        <button name="add_subject_btn" class="btn-basic" type="submit">إضافة موضوع جديد</button>
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
