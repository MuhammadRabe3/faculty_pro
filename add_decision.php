<?php
require_once "db.php";
require_once "functions.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php Head("اضافة قرار"); ?>

<body dir="rtl">
    <?php Headers(); ?>
    <?php if (is_admin()): ?>
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST") { ?>
            <?php Nav(); ?>
            <main class="add-member-page add-decision-page">
                <div class="container">
                    <!-- عنوان الصفحة -->
                    <div class="title">
                        <h1>إضافة قرار</h1>
                    </div>
                    <form class="box" method="post" action="addition_code.php" enctype="multipart/form-data">
	                    <?php if (@$_SESSION["error"]["add"]) { ?>
                            <p class="login-error">يرجى إدخال جميع الحقول</p>
		                    <?php unset($_SESSION["error"]["add"]);
	                    } ?>
                        <div class="col">
                            <div class="row sp-row">
                                <h4>نوع القرار</h4>
                                <div class="row ">
                                    <?php
                                    $decision_types_stmt = $conn->prepare("SELECT * FROM p39_decision_type");
                                    $decision_types_stmt->execute();
                                    $decision_types_result = $decision_types_stmt->get_result();
                                    while ($decision_types_row = $decision_types_result->fetch_assoc()) { ?>
                                        <div class="col">
                                            <h5><?= $decision_types_row["decision_type_name"] ?></h5>
                                            <input type="radio" name="decision_type"
                                                   value="<?= $decision_types_row['decision_type_id'] ?>">
                                        </div>
                                    <?php } ?>
                                    <?php $decision_types_stmt->close(); ?>
                                </div>
                            </div>
                            <div class="row">
                                <h4>تفاصيل القرار</h4>
                                <textarea name="decision_details"></textarea>
                            </div>
                            <div class="row sp-row">
                                <h4>هل له جواب تنفيذي؟</h4>
                                <div class="row ">
                                    <div class="col">
                                        <h5>نعم</h5><input class="resp-yes" type="radio" name="needs_action" value="1"
                                                           required aria-required="true">
                                    </div>
                                    <div class="col">
                                        <h5>لا</h5><input class="resp-no" type="radio" name="needs_action" value="0"
                                                          required aria-required="true">
                                    </div>
                                </div>
                            </div>
                            <div class="row resp-to deactive">
                                <h4>الجواب التنفيذي موجه إلى</h4>
                                <input type="text" name="action_to" placeholder="الجهة الموجه لها الجواب التنفيذي"/>
                            </div>
                            <div class="row">
                                <h4>ملاحظات</h4>
                                <textarea name="decision_comments"></textarea>
                            </div>

                            <!--                        <div class="row sp-row">-->
                            <!--                            <h4>هل تم تنفيذ القرار؟</h4>-->
                            <!--                            <div class="row ">-->
                            <!--                                <h5>نعم</h5><input type="radio" name="is-done">-->
                            <!--                                <h5>لا</h5><input type="radio" name="is-done">-->
                            <!--                            </div>-->
                            <!--                        </div>-->

                            <div class="row">
                                <input type="hidden" name="subject_id" value="<?= $_POST['subject_id'] ?>">
                                <input type="hidden" name="meeting_id" value="<?= $_POST['meeting_id'] ?>">
                                <button type="submit" class="btn-basic" name="add_decision_btn">اضافة قرار للموضوع</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        <?php } else { ?>
            <div class="error">
                <img src="./images/icons/error.svg" alt="">
                <p>
                    يجب الدخول للصفحة من خلال صفحة المجالس. سيتم تحويلك إلى صفحة المجالس بعد 5 ثواني.
                </p>
            </div>

            <?php header("refresh: 5; url= meetings.php"); ?>
        <?php } ?>
    <?php endif; ?>
    <?php footer(); ?>

    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>
    <script src="./js/add_update_decision.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>