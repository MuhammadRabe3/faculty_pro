<?php
require_once "db.php";
require_once "functions.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
} ?>

<!DOCTYPE html>
<html lang="en">
<?php Head("موضوعات المجلس الحالي"); ?>

<body dir="rtl">
<?php Headers();
if (is_logged_in()):
	Nav();
	$search = clean_data($_GET["mid"]);
	$is_current_stmt = $conn->prepare("SELECT 
                                                    is_current, 
                                                    meeting_number, 
                                                    status, 
                                                    formation_id as fid
                                                FROM 
                                                    p39_meeting 
                                                WHERE 
                                                    meeting_id = ?");
	$is_current_stmt->bind_param("i", $search);
	$is_current_stmt->execute();
	$is_current_result = $is_current_stmt->get_result();
	$is_current_row = $is_current_result->fetch_assoc();
    if (in_array($is_current_row["fid"], $_SESSION["formation_ids"]) || $_SESSION["admin"]) {
        @$is_current = $is_current_row["is_current"] == 1;
        @$status = $is_current_row["status"];?>
        <main class="current-subject-content">
            <div class="container">
                <!-- عنوان الصفحة -->
                <?php if ($is_current) { ?>
                    <div class="title">
                        <h1>موضوعات المجلس الحالي</h1>
                    </div>
                <?php } else { ?>
                    <div class="title">
                        <h1>موضوعات المجلس رقم <?= $is_current_row["meeting_number"] ?></h1>
                    </div>
                <?php } ?>
                <?php
                $formation_id_stmt = $conn->prepare("SELECT formation_id FROM p39_meeting WHERE meeting_id = ?");
                $formation_id_stmt->bind_param("i", $_GET["mid"]);
                $formation_id_stmt->execute();
                $formation_id_result = $formation_id_stmt->get_result();
                $formation_id_row = $formation_id_result->fetch_assoc();
    //            if (@in_array($formation_id_row["formation_id"], $_SESSION["formation_ids"]) OR $_SESSION["admin"]) {

                    $subject_types_stmt = $conn->prepare("SELECT * FROM p39_subject_type");
                    $subject_types_stmt->execute();
                    $subject_types_result = $subject_types_stmt->get_result();
                    $subject_types = array();
                    while ($subject_types_row = $subject_types_result->fetch_assoc()) {
                        $subject_types[$subject_types_row["subject_type_id"]] = $subject_types_row["subject_type_name"];
                    }
                    $subject_types_stmt->close();

                    if (!isset($_GET["search"])) {
                        $current_subjects_stmt = $conn->prepare("SELECT * FROM p39_subject WHERE meeting_id = ? ORDER BY -order_id DESC");
                        $current_subjects_stmt->bind_param("i", $search);
                        $current_subjects_stmt->execute();
                        $current_subjects_result = $current_subjects_stmt->get_result();
                        if ($current_subjects_result->num_rows > 0) {
                            while ($current_subjects_row = $current_subjects_result->fetch_assoc()) { ?>
                                <div class="current-subject-foradmin">
                                    <div class="box">
                                        <div class="row">
                                            <div class="col">
                                                <h4>
                                                    ترتيب عرض الموضوع:
                                                    <span>
                                                        <?= $current_subjects_row["order_id"] == NULL ? "لا يوجد" : $current_subjects_row["order_id"] ?>
                                                    </span>
                                                </h4>
                                                <h4>
                                                    رقم إدخال الموضوع:
                                                    <span>
                                                        <?= $current_subjects_row["subject_number"] ?>
                                                    </span>
                                                </h4>
                                                <h4>
                                                    تصنيف الموضوع:
                                                    <span>
                                                        <?= $subject_types[$current_subjects_row["subject_type_id"]] ?>
                                                    </span>
                                                </h4>
                                                <h4>
                                                    عنوان الموضوع:
                                                    <span>
                                                        <?= $current_subjects_row["subject_name"] ?>
                                                    </span>
                                                </h4>
                                            </div>

                                            <?php
                                            if ($is_current)
                                            {
                                                if (!$_SESSION["admin"]) { ?>
                                                    <div class="col col-subject-vote">
                                                        <?php $subject_vote_stmt = $conn->prepare("SELECT 
                                                                                                            subject_id 
                                                                                                        FROM 
                                                                                                            p39_vote 
                                                                                                        WHERE 
                                                                                                            user_id = ? 
                                                                                                          AND 
                                                                                                            subject_id = ?");
                                                        $subject_vote_stmt->bind_param("ii", $_SESSION["user_id"], $current_subjects_row["subject_id"]);
                                                        $subject_vote_stmt->execute();
                                                        $subject_vote_result = $subject_vote_stmt->get_result();
                                                        $subject_vote_exists = $subject_vote_result->num_rows > 0;
                                                        switch ($status) {
	                                                        case "finished":
		                                                        if (!$subject_vote_exists) { ?>
                                                                    <button type="button" class="btn-basic disabled"
                                                                            disabled title="لا يمكن التصويت على موضوع في مجلس نهائي">تصويت</button>
		                                                        <?php } else { ?>
                                                                    <button type="button" class="btn-basic disabled"
                                                                            disabled title="لا يمكن تعديل التصويت على موضوع في مجلس نهائي">
                                                                        تعديل التصويت
                                                                    </button>
		                                                        <?php }
		                                                        break;
                                                            default: ?>
                                                                <form method="post" action="voting.php">
                                                                    <input type="hidden" name="subject_id"
                                                                           value="<?= $current_subjects_row['subject_id'] ?>">
                                                                    <?php if (!$subject_vote_exists) { ?>
                                                                        <button name="voting_btn" class="btn-basic">
                                                                            تصويت
                                                                        </button>
                                                                    <?php } else { ?>
                                                                        <button name="voting_btn" class="btn-basic">
                                                                            تعديل التصويت
                                                                        </button>
                                                                    <?php } ?>
                                                                </form>
                                                                <?php break;
                                                        } ?>
                                                        <button class="btn-basic subject-details-btn">
                                                            تفاصيل الموضوع
                                                        </button>
                                                    </div>
                                                    <!-- Closing tag of row -->
                                                    </div>
                                                <?php } else {
                                                    $subject_decision_stmt = $conn->prepare("SELECT 
                                                                                                        decision_details
                                                                                                    FROM 
                                                                                                        p39_decision 
                                                                                                    WHERE 
                                                                                                        subject_id = ?");
                                                    $subject_decision_stmt->bind_param("i", $current_subjects_row["subject_id"]);
                                                    $subject_decision_stmt->execute();
                                                    $subject_decision_result = $subject_decision_stmt->get_result();
                                                    $subject_decision_exists = $subject_decision_result->num_rows > 0;
                                                    $subject_decision_stmt->close(); ?>
                                                    </div>
                                                    <div class="row">
                                                        <?php if ($status == "confirmed") {
                                                            if ($subject_decision_exists) { ?>
                                                                <div class="col">
                                                                    <button class="btn-basic disabled" disabled>إضافة قرار</button>
                                                                </div>
                                                                <div class="col">
                                                                    <form method="post" action="update_decision.php">
                                                                        <input type="hidden" name="meeting_id" value="<?= $_GET['mid'] ?>">
                                                                        <input type="hidden" name="subject_id"
                                                                               value="<?= $current_subjects_row['subject_id'] ?>">
                                                                        <button class="btn-basic" name="add_decision">تعديل قرار</button>
                                                                    </form>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="col">
                                                                    <form method="post" action="add_decision.php">
                                                                        <input type="hidden" name="subject_id"
                                                                               value="<?= $current_subjects_row['subject_id'] ?>">
                                                                        <input type="hidden" name="meeting_id" value="<?= $_GET['mid'] ?>">

                                                                        <button class="btn-basic" name="add_decision">اضافة قرار</button>
                                                                    </form>
                                                                </div>
                                                                <div class="col">
                                                                    <button class="btn-basic disabled" disabled>تعديل قرار</button>
                                                                </div>
                                                            <?php }
                                                        } else { ?>
                                                            <div class="col">
                                                                <button title="لا يمكن إضافة قرار إلا في مجلس مؤكد" disabled
                                                                        class="btn-basic disabled">اضافة قرار</button>
                                                            </div>
                                                            <div class="col">
                                                                <button title="لا يمكن تعديل قرار إلا في مجلس مؤكد" disabled
                                                                        class="btn-basic disabled">تعديل قرار</button>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col">
                                                            <button class="btn-basic subject-details-btn">
                                                                تفاصيل الموضوع
                                                            </button>
                                                        </div>
                                                        <?php if ($status != "pending") { ?>
                                                            <div class="col">
                                                                <button type="button" title="لا يمكن تعديل موضوع إلا في مجلس غير مؤكد"
                                                                        class="btn-basic disabled" disabled>تعديل الموضوع</button>
                                                            </div>
                                                        <?php } else { ?>
                                                            <div class="col">
                                                                <form method="post" action="update_subject.php">
                                                                    <input type="hidden" name="subject_id"
                                                                           value="<?= $current_subjects_row['subject_id'] ?>">
                                                                    <button class="btn-basic" name="update_subject_btn">تعديل الموضوع</button>
                                                                </form>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col">
                                                            <a class="btn-basic" href="subject_attachment.php?sid=<?=$current_subjects_row['subject_id']?>">عرض المرفقات</a>
                                                        </div>
                                                        <div class="col">
                                                            <a class="btn-basic" href="votes.php?sid=<?=$current_subjects_row['subject_id']?>">عرض الأصوات</a>
                                                        </div>

                                                    </div>
                                                <?php }
                                            } else { ?>
                                                </div>
                                                <div class="row">
                                                    <div class="col col-subject-vote">
                                                        <button class="btn-basic subject-details-btn">
                                                            تفاصيل الموضوع
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                    <div class="current-subject-details deactive">
                                        <div class="table-container">
                                            <table class="subjects-table">
                                                <tbody>
                                                <tr class="subject-row">
                                                    <td>الموضوع (<?= $current_subjects_row["subject_number"] ?>)</td>
                                                    <td>
                                                        <strong><?= $current_subjects_row["subject_name"] ?></strong>
                                                        <p><?= $current_subjects_row["subject_details"] ?></p>
                                                        <?php
                                                        $subject_pic_stmt = $conn->prepare("SELECT 
                                                                                                        picture_name
                                                                                                    FROM 
                                                                                                        p39_subject_picture 
                                                                                                    WHERE 
                                                                                                        subject_id = ?");
                                                        $subject_pic_stmt->bind_param("i", $current_subjects_row["subject_id"]);
                                                        $subject_pic_stmt->execute();
                                                        $subject_pic_result = $subject_pic_stmt->get_result();
                                                        $subject_pic_exists = $subject_pic_result->num_rows > 0; ?>
                                                        <?php if ($subject_pic_exists) { ?>
                                                            <?php while ($subject_pic_row = $subject_pic_result->fetch_assoc()) { ?>
                                                                <img src="<?= $subject_pic_row['picture_name'] ?>" alt="صورة تفاصيل الموضوع">
                                                            <?php }
                                                        } ?>
                                                    </td>
                                                </tr>
                                                <tr class="decision-row">
                                                    <td>القرار</td>
                                                    <td>
                                                        <?php
                                                        $subject_decision_stmt = $conn->prepare("SELECT 
                                                                                                            decision_details
                                                                                                        FROM 
                                                                                                            p39_decision 
                                                                                                        WHERE 
                                                                                                            subject_id = ?");
                                                        $subject_decision_stmt->bind_param("i", $current_subjects_row["subject_id"]);
                                                        $subject_decision_stmt->execute();
                                                        $subject_decision_result = $subject_decision_stmt->get_result();
                                                        $subject_decision_row = $subject_decision_result->fetch_assoc(); ?>
                                                        <p><?= @$subject_decision_row["decision_details"] == NULL
                                                                ? "لا يوجد"
                                                                : $subject_decision_row["decision_details"]  ?></p>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <main id="empty" class="empty-current-subject">
                                <h4>لا يوجد موضوعات الان</h4>
                            </main>
                                    <?php
                        }
                        if ($_SESSION["admin"] AND @$is_current)
                        {
                            ?>
                            </div>
                            <div class="add-current-subject">
                                <?php if (@$status == "pending") { ?>
                                    <form method="post" action="add_subject.php">
                                        <input type="hidden" name="meeting_id" value="<?= $search ?>">
                                        <button name="add_subject_btn" class="btn-basic">إضافة موضوع</button>
                                    </form>
                                <?php } else { ?>
                                    <button title="لا يمكن إضافة موضوع إلا في مجلس غير مؤكد" type="button" class="btn-basic disabled" disabled>إضافة موضوع</button>
                                <?php } ?>
                            </div>
                            <?php
                        }
                    }
    //            } else {
    //                ?>
    <!--                <div class="container">-->
    <!--                    <main id="empty" class="empty-current-subject">-->
    <!--                        <h4>لا يوجد موضوعات الان</h4>-->
    <!--                    </main>-->
    <!--                </div>-->
    <!--                --><?php
    //            }
            ?>
            </main>
    <?php } else {
        header("location: index.php", true, 303);
    }
endif;
footer(); ?>

<!-- Js Scripts and Plugins -->
<script type="module" src="./js/main.js"></script>
<script src="./js/subject_details.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>