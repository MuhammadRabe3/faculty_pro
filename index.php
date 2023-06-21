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
<?php Head("الصفحة الرئيسية"); ?>
<body dir="rtl">
    <?php Headers(); ?>
    <?php if (is_logged_in()) { ?>
        <?php Nav(); ?>
        <!-- main content of the page -->
        <main class="main-page reports-content">
            <div class="container">
                <!-- عنوان الصفحة -->
                <div class="title">
                    <h1> الصفحة الرئيسية</h1>
                </div>
                <?php if ($_SESSION["admin"]) { ?>
                    <div class="box">

                        <div class="row">
                            <div class="col">
                                <a href="meetings.php" class="btn-basic">المجالس</a>
                            </div>
                            <div class="col">
                            <a href="formation.php" class="btn-basic">التشكيلات السنوية</a>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                
                                <a href="all_subjects.php?f=all" class="btn-basic">جميع الموضوعات</a>
                            </div>
                            <div class="col">
                                <a href="members.php" class="btn-basic">الأعضاء</a>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col">
                            <a href="subjects_table.php" class="btn-basic">جدول الأعمال</a>
                            </div>
                            <div class="col">
                                <a href="executive_decisions.php" class="btn-basic">القرارات التنفيذية</a>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="box">
                        <div class="row">
                            <div class="col">
                                <a href="meetings.php" class="btn-basic">المجالس</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="subjects_table.php" class="btn-basic">جدول الأعمال</a>
                            </div>

                            <div class="col">
                                <a href="formation.php" class="btn-basic">التشكيلات السنوية</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </main>
    <?php } ?>
    <?php footer(); ?>

    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>
</html>