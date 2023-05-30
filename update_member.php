<?php
require_once "db.php";
require_once "functions.php";

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
Head("الأعضاء");
?>

<body dir="rtl">
<?php
Headers();
if(is_admin()):
    Nav();
    $user_stmt = $conn->prepare("SELECT * FROM p39_users WHERE user_id = ?");
    $user_stmt->bind_param("i", $_POST["user_id"]);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    ?>
    <main class="add-member-page">
        <div class="container">
            <!-- عنوان الصفحة -->
            <div class="title">
                <h1>تعديل بيانات العضو</h1>
            </div>
            <form class="box" method="post" action="update_code.php" enctype="multipart/form-data">
                <div class="col">

                    <div class="row">
                        <h4>الاسم بالكامل</h4><input type="text" name="name" placeholder="الاسم بالكامل"
                                                     value="<?=$user_row['name']?>"/>
                    </div>
                    <div class="row sp-row">
                                    <h4>النوع</h4>
                    <div class="row">
                        <?=$user_row["gender"] == "M"
                            ? '<h4>ذكر</h4><input type="radio" value="M" name="gender" required checked>
                                <h4>أنثى</h4><input type="radio" value="F" name="gender" required>'
                            : '<h4>ذكر</h4><input type="radio" value="M" name="gender" required>
                                <h4>أنثى</h4><input type="radio" value="F" name="gender" required checked>'?>
                    </div>
                    </div>
                    <div class="row">
                        <h4>البريد الالكتروني</h4><input type="email" name="email" placeholder="البريد الالكتروني"
                                                         value="<?= $user_row['email'] ?>" />
                    </div>
                    <div class="row">
                        <h4>كلمة السر</h4><input type="password" name="password" placeholder="تعديل كلمة السر" />
                    </div>

                    <div class="row">
                        <h4>المسمى الوظيفي</h4><input type="text" name="job_title" placeholder="المسمى الوظيفي"
                        value="<?=$user_row['job_title']?>"/>
                    </div>
                    <div class="row">
                        <h4>الفئة الوظيفية</h4>
                        <div class="select-basic">
                            <select name="job_type" required>
                                <option value="">اختر</option>
                                <?php
                                // Get All Job Types
                                $job_types_stmt = $conn->prepare("SELECT * FROM p39_job_type");
                                $job_types_stmt->execute();
                                $job_types_result = $job_types_stmt->get_result();
                                while ($job_types_row = $job_types_result->fetch_assoc())
                                {
                                    if ($job_types_row["job_type_id"] == $user_row["job_type_id"])
                                    {
                                        ?>
                                        <option value='<?=$job_types_row["job_type_id"]?>' selected>
                                                <?=$job_types_row['job_type_name']?>
                                        </option>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <option value="<?=$job_types_row['job_type_id']?>">
                                            <?=$job_types_row["job_type_name"]?>
                                        </option>
                                        <?php
                                    }
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
                                while ($job_ranks_row = $job_ranks_result->fetch_assoc())
                                {
                                    if ($job_ranks_row["job_rank_id"] == $user_row["job_rank_id"])
                                    {
                                        echo "<option value='{$job_ranks_row["job_rank_id"]}' selected>
                                                {$job_ranks_row['job_rank_name']}</option>";
                                    }
                                    else
                                    {
                                        ?>
                                        <option value="<?=$job_ranks_row['job_rank_id']?>">
                                            <?=$job_ranks_row["job_rank_name"]?>
                                        </option>
                                        <?php
                                    }
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
                                while ($departments_row = $departments_result->fetch_assoc())
                                {
                                    if ($departments_row["department_id"] == $user_row["department_id"])
                                    {
                                        echo "<option value='{$departments_row["department_id"]}' selected>
                                                {$departments_row['department_name']}</option>";
                                    }
                                    else
                                    {
                                        ?>
                                        <option value="<?=$departments_row['department_id']?>">
                                            <?=$departments_row["department_name"]?>
                                        </option>
                                        <?php
                                    }
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
                                <?=$user_row["is_admin"] == "0"
                                    ? '<option value="">اختر</option>
                                        <option value="0" selected>عضو مجلس</option>
                                        <option value="1">أمين مجلس (ادمن)</option>'
                                    : '<option value="">اختر</option>
                                        <option value="0">عضو مجلس</option>
                                        <option value="1" selected>أمين مجلس (ادمن)</option>'?>
                            </select>
                        </div>
                    </div>
                    <div class="row sp-row">
                        <h4>حالة العضو</h4>
                        <div class="row">
                            <?=$user_row["is_enabled"] == "1"
                                ? '<h4>مفعل</h4><input type="radio" name="is_enabled" value="1" checked />
                                    <h4>موقوف</h4><input type="radio" name="is_enabled" value="0" />'
                                : '<h4>مفعل</h4><input type="radio" name="is_enabled" value="1" />
                                    <h4>موقوف</h4><input type="radio" name="is_enabled" value="0" checked />'?>
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
                                           accept="image/png, image/gif, image/jpeg" />
                                </label>
                            </div>
                            <div class="file-list"></div>
                        </div>
                    </div>
                    <div class="row">
                        <h4>ملاحظات</h4>
                        <textarea name=""></textarea>
                    </div>
                    <div class="row">
                        <input type="hidden" value="<?=$_POST['user_id']?>" name="user_id">
                        <button type="submit" class="btn-basic" name="update_member_btn">تعديل</button>
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
