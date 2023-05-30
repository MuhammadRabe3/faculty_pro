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
<?php Head("المجالس"); ?>

<body dir="rtl">
	<?php Headers(); ?>
	<?php if (is_logged_in()) : ?>
		<?php Nav();?>
        <main id="admin" class="meetings-content">
            <div class="container">
                <div class="search-container">
                    <?php SearchBar(); ?>
                </div>
                <div class='meetings-title'>
                    <h1>المجالس</h1>
                </div>
                
                <?php
                    ### If user hasn't pressed on search (Default Case)
                    if (!isset($_GET["search"])):
                        $current_meeting_stmt = $conn->prepare("SELECT 
                                                                            m.*, 
                                                                            f.formation_number 
                                                                        FROM 
                                                                            p39_meeting as m
                                                                                JOIN p39_formation as f 
                                                                                    ON m.formation_id = f.formation_id 
                                                                                           AND m.is_current = 1");
                        $current_meeting_stmt->execute();
                        $current_meeting_result = $current_meeting_stmt->get_result();
                        ?>
                        <div class="current-meeting">
                            <h3>المجلس الحالي</h3>
                        </div>
                        <?php
                        # Check if there are current meetings
                        $current_meeting_exists = $current_meeting_result->num_rows > 0;
                        if ($current_meeting_exists)
                        {
                            while ($current_meeting_row = $current_meeting_result->fetch_assoc())
                            {
                                # If user doesn't exist in this formation & not an admin, he shouldn't see the meeting
                                if (!in_array($current_meeting_row["formation_id"], $_SESSION["formation_ids"])
                                    and !$_SESSION["admin"])
                                { ?>
                                    <div class="current-meeting">
                                        <main id="empty" class="empty-meeting">
                                            <h4>لا يوجد مجلس حالي الان</h4>
                                        </main>
                                    </div>
                                    <!-- Break While loop if no current meeting -->
                                    <?php break;
                                }
                                if ($current_meeting_row["is_showed"] == "0" AND !$_SESSION["admin"])
                                { ?>
                                    <div class="current-meeting">
                                        <main id="empty" class="empty-meeting">
                                            <h4>لا يوجد مجلس حالي الان</h4>
                                        </main>
                                    </div>
                                    <!-- Break While loop if no current meeting -->
	                                <?php break;
                                }
                                switch ($current_meeting_row["is_showed"])
                                {
                                    ### If meeting is not showed, then only admins may see it
                                    case "0":
                                    case "1": ?>
                                        <div class="meeting-box">
                                            <div class="row">
                                                <div class="col">
                                                    <h4>
                                                        رقم المجلس:
                                                        <span class="meeting-number">
                                                            <?=$current_meeting_row["meeting_number"]?>
                                                        </span>
                                                    </h4>
                                                    <h4>
                                                        رقم تشكيل المجلس:
                                                        <span class="meeting-formation-number">
                                                          <?=$current_meeting_row["formation_number"]?>
                                                        </span>
                                                    </h4>
                                                    <h4>
                                                        بتاريخ:
                                                        <span class="meeting-month">
                                                            <?=$current_meeting_row["meeting_month"]?>
                                                        </span>
                                                        /
                                                        <span class="meeting-year">
                                                            <?=$current_meeting_row["meeting_year"]?>
                                                        </span>
                                                    </h4>
                                                    <h4>
                                                        حالة المجلس:
                                                        <?php
                                                        switch ($current_meeting_row["status"])
                                                        {
                                                            case "pending":
                                                                echo "غير مؤكد";
                                                                break;
                                                            case "confirmed":
                                                                echo "مؤكد";
                                                                break;
                                                            case "finished":
                                                                echo "نهائي";
                                                                break;
                                                            default:
                                                                echo $current_meeting_row["status"];
                                                                break;
                                                        }
                                                        ?>
                                                    </h4>
                                                    <h4>
                                                        تاريخ الانعقاد:
                                                        <span class="meetings-title">
                                                            <?=$current_meeting_row["meeting_date"] == NULL ? "غير محدد" : $current_meeting_row["meeting_date"]?>
                                                        </span>
                                                    </h4>
                                                </div>
                                                <?php if ($_SESSION["admin"]) {
                                                    $subject_count_stmt = $conn->prepare("SELECT 
                                                                                                    COUNT(subject_id) as s
                                                                                                FROM 
                                                                                                    p39_subject 
                                                                                                WHERE 
                                                                                                    meeting_id = ?");
                                                    $subject_count_stmt->bind_param("i", $current_meeting_row["meeting_id"]);
                                                    $subject_count_stmt->execute();
                                                    $subject_count_result = $subject_count_stmt->get_result();
                                                    $subject_count_row = $subject_count_result->fetch_assoc();

                                                    $attendance_count_stmt = $conn->prepare("SELECT 
                                                                                                        COUNT(meeting_id) as ma
                                                                                                    FROM 
                                                                                                        p39_attendance 
                                                                                                    WHERE 
                                                                                                        meeting_id = ?");
                                                    $attendance_count_stmt->bind_param("i", $current_meeting_row["meeting_id"]);
                                                    $attendance_count_stmt->execute();
                                                    $attendance_count_result = $attendance_count_stmt->get_result();
                                                    $attendance_count_row = $attendance_count_result->fetch_assoc();

                                                    $subject_decision_stmt = $conn->prepare("SELECT
                                                                                                        SUM(d.decision_type_id = 1) AS d1,
                                                                                                        SUM(d.decision_type_id = 2) AS d2
                                                                                                    FROM
                                                                                                        p39_decision as d
                                                                                                    JOIN p39_subject AS s
                                                                                                    ON
                                                                                                        s.subject_id = d.subject_id 
                                                                                                            AND 
                                                                                                        s.meeting_id = ?");
                                                    $subject_decision_stmt->bind_param("i", $current_meeting_row["meeting_id"]);
                                                    $subject_decision_stmt->execute();
                                                    $subject_decision_result = $subject_decision_stmt->get_result();
                                                    $subject_decision_row = $subject_decision_result->fetch_assoc();

                                                    $exec_stmt = $conn->prepare("SELECT
                                                                                            SUM(d.needs_action = 1 AND d.is_action_done = 1) AS d1,
                                                                                            SUM(d.needs_action = 1 AND d.is_action_done = 0) AS d0
                                                                                        FROM
                                                                                            p39_decision as d
                                                                                        JOIN p39_subject AS s
                                                                                        ON
                                                                                            s.subject_id = d.subject_id AND s.meeting_id = ?");
                                                    $exec_stmt->bind_param("i", $current_meeting_row["meeting_id"]);
                                                    $exec_stmt->execute();
                                                    $exec_result = $exec_stmt->get_result();
                                                    $exec_row = $exec_result->fetch_assoc();
                                                    ?>
                                                    <div class="col">
                                                        <div class="row">
                                                            <div class="card">
                                                                <div class="card-box">
                                                                    <?= $subject_count_row["s"] == NULL
                                                                        ? 0
                                                                        : $subject_count_row["s"] ?>
                                                                </div>
                                                                <div class="card-text">عدد الموضوعات</div>
                                                            </div>
                                                            <div class="card">
                                                                <div class="card-box">
                                                                    <?= $attendance_count_row["ma"] == NULL
                                                                        ? 0
                                                                        : $attendance_count_row["ma"] ?>
                                                                </div>
                                                                <div class="card-text">الاعضاء الحاضرين</div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="card">
                                                                <div class="card-box">
                                                                    <?= $subject_decision_row["d1"] == NULL
                                                                        ? 0
                                                                        : $subject_decision_row["d1"] ?>
                                                                </div>
                                                                <div class="card-text">الموضوعات المقبولة </div>
                                                            </div>
                                                            <div class="card">
                                                                <div class="card-box">
	                                                                <?= $subject_decision_row["d2"] == NULL
                                                                        ? 0
                                                                        : $subject_decision_row["d2"] ?>
                                                                </div>
                                                                <div class="card-text">الموضوعات المرفوضة</div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="card">
                                                                <div class="card-box">
                                                                    <?= $exec_row["d1"] == NULL
                                                                        ? 0
                                                                        : $exec_row["d1"] ?>
                                                                </div>
                                                                <div class="card-text">قرارات منفذة</div>
                                                            </div>
                                                            <div class="card">
                                                                <div class="card-box">
	                                                                <?= $exec_row["d0"] == NULL
                                                                        ? 0
                                                                        : $exec_row["d0"]?>
                                                                </div>
                                                                <div class="card-text">قرارات غير منفذة</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }
                                                switch ($current_meeting_row["status"])
                                                {
                                                    case "pending":
                                                        $order_stmt = $conn->prepare("SELECT
                                                                                                s.order_id
                                                                                            FROM
                                                                                                `p39_subject` AS s
                                                                                            JOIN 
                                                                                                    `p39_meeting` AS m
                                                                                            ON s.meeting_id = m.meeting_id 
                                                                                                   AND 
                                                                                               m.is_current = 1
                                                                                            WHERE
                                                                                                s.order_id IS NULL");
                                                        $order_stmt->execute();
                                                        $order_result = $order_stmt->get_result();
                                                        $empty_order_exists = $order_result->num_rows > 0;
                                                        if ($_SESSION["admin"]) { ?>
                                                            <div class="col">
                                                                <?php if (!$empty_order_exists) { ?>
                                                                    <button class="btn-basic" data-open-modal>
                                                                        تأكيد
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button class="btn-basic disabled" type="button"
                                                                            disabled title=
                                                                            "يجب ترتيب الموضوعات قبل تأكيد المجلس">
                                                                        تأكيد
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                <?php } ?>
                                                                <dialog data-modal>
                                                                    <h4>
                                                                        هل تريد تأكيد المجلس؟
                                                                    </h4>
                                                                    <form method="post" action="meeting_status.php">
                                                                        <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                        <button class="btn-basic" name="confirm_btn">
                                                                            تأكيد
                                                                        </button>
                                                                        <button class="btn-basic" type="submit" formmethod="dialog">
                                                                            إلغاء
                                                                        </button>
                                                                    </form>
                                                                </dialog>
	                                                            <?php if ($current_meeting_row["is_showed"] == "0") { ?>
                                                                    <button class="btn-basic" data-open-modal>
                                                                        إظهار المجلس
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                    <dialog data-modal>
                                                                        <h4>
                                                                            هل تريد إظهار المجلس لباقي الأعضاء؟
                                                                        </h4>
                                                                        <form method="post" action="meeting_status.php">
                                                                            <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                            <button class="btn-basic" name="show_btn">
                                                                                تأكيد
                                                                            </button>
                                                                            <button class="btn-basic" type="submit" formmethod="dialog">
                                                                                إلغاء
                                                                            </button>
                                                                        </form>
                                                                    </dialog>
	                                                            <?php } else { ?>
                                                                    <button class="btn-basic" data-open-modal>
                                                                        إخفاء المجلس
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                    <dialog data-modal>
                                                                        <h4>
                                                                            هل تريد إخفاء المجلس عن باقي الأعضاء؟
                                                                        </h4>
                                                                        <form method="post" action="meeting_status.php">
                                                                            <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                            <button class="btn-basic" name="hide_btn">
                                                                                تأكيد
                                                                            </button>
                                                                            <button class="btn-basic" type="submit" formmethod="dialog">
                                                                                إلغاء
                                                                            </button>
                                                                        </form>
                                                                    </dialog>
	                                                            <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                        <!-- Closing Tag of Row -->
                                                        </div>
                                                        <!-- Meeting buttons for admin -->
                                                        <?php if ($_SESSION["admin"]) { ?>
                                                            <div class="current-meeting-buttons">
                                                                <form method="post" action="update_meeting.php" class="current-meeting-buttons">
                                                                    <input type="hidden" value="<?=$current_meeting_row['meeting_id']?>" name="meeting_id">
                                                                    <button name="update_meeting_btn" class="btn-basic">تعديل المجلس</button>
                                                                </form>
                                                                <form method="get" action="current_meeting_subject.php" class="current-meeting-buttons">
                                                                    <input type="hidden" value="<?=$current_meeting_row['meeting_id']?>" name="mid">
                                                                    <button class="btn-basic">موضوعات المجلس</button>
                                                                </form>
                                                                <a href="subjects_table.php" class="btn-basic">جدول الأعمال</a>
                                                                <button title=" يجب ان يكون المجلس مؤكد اولا"
                                                                        class="btn-basic disabled" disabled
                                                                        type="button">تسجيل الحضور</button>
                                                                <button title=" يجب ان يكون المجلس مؤكد اولا"
                                                                        class="btn-basic disabled" disabled
                                                                        type="button">محضر الاجتماع</button>
                                                                <button title=" يجب ان يكون المجلس مؤكد اولا"
                                                                        class="btn-basic disabled" disabled
                                                                        type="button">رفع ملف المجلس النهائي</button>
                                                            </div>
                                                        <?php } else { ?>
                                                            <!-- Meeting buttons for user -->
                                                            <div class="current-meeting-buttons">
                                                                <button class="btn-basic disabled" disabled
                                                                        title="لا يوجد موضوعات">
                                                                    موضوعات المجلس</button>
                                                                <button class="btn-basic disabled" disabled
                                                                        title="لا يوجد جدول أعمال">
                                                                    عرض ملف جدول الاعمال</button>
                                                                <button class="btn-basic disabled" disabled
                                                                        title="لا يوجد محضر اجتماع">
                                                                    محضر الاجتماع</button>
                                                                <button class="btn-basic disabled" disabled
                                                                        title="لا يوجد ملف مجلس نهائي">
                                                                    عرض ملف المجلس النهائي</button>
                                                            </div>
                                                        <?php } ?>
                                                        <!-- Closing Tag of Meeting box -->
                                                        </div>
                                                        <?php break;

                                                    case "confirmed":
                                                        if ($_SESSION["admin"]) { ?>
                                                            <div class="col">
                                                                <button data-open-modal class="btn-basic">
                                                                    إلغاء التأكيد
                                                                    <i class="fa-solid fa-check"></i>
                                                                </button>
                                                                <dialog data-modal>
                                                                    <form method="post" action="meeting_status.php">
                                                                        <h4>هل تريد الغاء تأكيد المجلس؟</h4>
                                                                        <input type="hidden" name="meeting_id" value=
                                                                        "<?=$current_meeting_row['meeting_id']?>">
                                                                        <button class="btn-basic" name="pending_btn">
                                                                            إلغاء تأكيد
                                                                        </button>

                                                                        <button type="submit" formmethod="dialog"
                                                                                class="btn-basic">لا</button>
                                                                    </form>
                                                                </dialog>
                                                                <button data-open-modal class="btn-basic">
                                                                    تحويل لمجلس سابق
                                                                    <i class="fa-solid fa-check"></i>
                                                                </button>
                                                                <dialog data-modal>
                                                                    <form method="post" action="meeting_status.php">
                                                                        <h4>هل تريد تحويل المجلس لمجلس سابق؟</h4>
                                                                        <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                        <button class="btn-basic" name="past_btn">
                                                                            تحويل لمجلس سابق
                                                                            <i class="fa-solid fa-check"></i>
                                                                        </button>
                                                                        <button type="submit" formmethod="dialog" class="btn-basic">لا</button>
                                                                    </form>
                                                                </dialog>
                                                                <?php if ($current_meeting_row["is_showed"] == "0") { ?>
                                                                    <button class="btn-basic" data-open-modal>
                                                                        إظهار المجلس
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                    <dialog data-modal>
                                                                        <h4>
                                                                            هل تريد إظهار المجلس لباقي الأعضاء؟
                                                                        </h4>
                                                                        <form method="post" action="meeting_status.php">
                                                                            <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                            <button class="btn-basic" name="show_btn">
                                                                                تأكيد
                                                                            </button>
                                                                            <button class="btn-basic" type="submit" formmethod="dialog">
                                                                                إلغاء
                                                                            </button>
                                                                        </form>
                                                                    </dialog>
                                                                <?php } else { ?>
                                                                    <button class="btn-basic" data-open-modal>
                                                                        إخفاء المجلس
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                    <dialog data-modal>
                                                                        <h4>
                                                                            هل تريد إخفاء المجلس عن باقي الأعضاء؟
                                                                        </h4>
                                                                        <form method="post" action="meeting_status.php">
                                                                            <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                            <button class="btn-basic" name="hide_btn">
                                                                                تأكيد
                                                                            </button>
                                                                            <button class="btn-basic" type="submit" formmethod="dialog">
                                                                                إلغاء
                                                                            </button>
                                                                        </form>
                                                                    </dialog>
                                                                <?php } ?>
                                                                </div>
                                                        <?php } ?>
                                                        <!-- Closing Tag of Row -->
                                                        </div>
                                                        <?php if ($_SESSION["admin"]) { ?>
                                                            <div class="current-meeting-buttons">
                                                                <button type="button" disabled class="btn-basic disabled" title="لا يمكن تعديل مجلس مؤكد">
                                                                    تعديل المجلس
                                                                </button>
                                                                <form method="get" action="current_meeting_subject.php"
                                                                      class="current-meeting-buttons">
                                                                    <input type="hidden" value="<?=$current_meeting_row['meeting_id']?>" name="mid">
                                                                    <button class="btn-basic">
                                                                        موضوعات المجلس
                                                                    </button>
                                                                </form>
                                                                <a href="subjects_table.php" class="btn-basic">جدول الأعمال</a>
                                                                <form method="post" action="meeting_attendance.php"
                                                                      class="current-meeting-buttons">
                                                                    <input type="hidden" name="meeting_id"
                                                                           value="<?= $current_meeting_row['meeting_id'] ?>">
                                                                    <button class="btn-basic" name="attendance_btn">
                                                                        تسجيل الحضور
                                                                    </button>
                                                                </form>
                                                                <a href="subjects_decisions.php?mid=<?= $current_meeting_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                                                <a class="btn-basic" href="meeting_attachment.php?mid=<?= $current_meeting_row['meeting_id'] ?>">
                                                                    رفع ملف المجلس النهائي
                                                                </a>
                                                            </div>
                                                        <?php } else { ?>
                                                            <div class="current-meeting-buttons">
                                                                <form method="get" action="current_meeting_subject.php">
                                                                    <input type="hidden" value="<?=$current_meeting_row['meeting_id']?>" name="mid">
                                                                    <button class="btn-basic">موضوعات المجلس</button>
                                                                </form>
                                                                <a href="subjects_table.php" class="btn-basic">عرض ملف جدول الاعمال</a>
                                                                <a href="subjects_decisions.php?mid=<?= $current_meeting_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                                                <button class="btn-basic disabled" title="لا يوجد ملف مجلس نهائي"
                                                                        disabled>عرض ملف المجلس النهائي</button>
                                                            </div>
                                                        <?php } ?>
                                                        <!-- Closing Tag of Meeting Box -->
                                                        </div>
                                                        <?php break;
                                                    case "finished":
	                                                    if ($_SESSION["admin"]) { ?>
                                                        <div class="col">
                                                            <button data-open-modal class="btn-basic">تحويل لمجلس سابق <i class="fa-solid fa-check"></i></button>
                                                            <dialog data-modal>
                                                                <form method="post" action="meeting_status.php">
                                                                    <div class="col">
                                                                        <h4>هل تريد تحويل المجلس لمجلس سابق؟</h4>
                                                                        <input type="hidden" name="meeting_id"
                                                                               value="<?=$current_meeting_row['meeting_id']?>">
                                                                        <button class="btn-basic" name="past_btn">
                                                                            تحويل لمجلس سابق
                                                                            <i class="fa-solid fa-check"></i>
                                                                        </button>
                                                                        <button type="submit" formmethod="dialog" class="btn-basic">لا</button>
                                                                    </div>
                                                                </form>
                                                            </dialog>

                                                            <?php if ($current_meeting_row["is_showed"] == "0") { ?>
                                                                <button class="btn-basic" data-open-modal>
                                                                    إظهار المجلس
                                                                    <i class="fa-solid fa-check"></i>
                                                                </button>
                                                                <dialog data-modal>
                                                                    <h4>
                                                                        هل تريد إظهار المجلس لباقي الأعضاء؟
                                                                    </h4>
                                                                    <form method="post" action="meeting_status.php">
                                                                        <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                        <button class="btn-basic" name="show_btn">
                                                                            تأكيد
                                                                        </button>
                                                                        <button class="btn-basic" type="submit" formmethod="dialog">
                                                                            إلغاء
                                                                        </button>
                                                                    </form>
                                                                </dialog>
                                                            <?php } else { ?>
                                                                <button class="btn-basic" data-open-modal>
                                                                    إخفاء المجلس
                                                                    <i class="fa-solid fa-check"></i>
                                                                </button>
                                                                <dialog data-modal>
                                                                    <h4>
                                                                        هل تريد إخفاء المجلس عن باقي الأعضاء؟
                                                                    </h4>
                                                                    <form method="post" action="meeting_status.php">
                                                                        <input type="hidden" name="meeting_id" value="<?=$current_meeting_row['meeting_id']?>">
                                                                        <button class="btn-basic" name="hide_btn">
                                                                            تأكيد
                                                                        </button>
                                                                        <button class="btn-basic" type="submit" formmethod="dialog">
                                                                            إلغاء
                                                                        </button>
                                                                    </form>
                                                                </dialog>
                                                            <?php } ?>
                                                        </div>
                                                        <?php }?>
                                                        </div>
                                                        <?php if ($_SESSION["admin"]) { ?>
                                                            <div class="current-meeting-buttons">
                                                                <button type="button" disabled class="btn-basic disabled" title="لا يمكن تعديل مجلس نهائي">
                                                                    تعديل المجلس
                                                                </button>
                                                                <form method="get" action="current_meeting_subject.php"
                                                                      class="current-meeting-buttons">
                                                                    <input type="hidden" value="<?=$current_meeting_row['meeting_id']?>" name="mid">
                                                                    <button class="btn-basic">
                                                                        موضوعات المجلس
                                                                    </button>
                                                                </form>
                                                                <form method="post" action="meeting_attendance.php"
                                                                      class="current-meeting-buttons">
                                                                    <input type="hidden" name="meeting_id"
                                                                           value="<?= $current_meeting_row['meeting_id'] ?>">
                                                                    <button type="button" disabled class="btn-basic disabled" title="لا يمكن تعديل مجلس نهائي">
                                                                        تسجيل الحضور
                                                                    </button>
                                                                </form>
                                                                <a href="subjects_decisions.php?mid=<?= $current_meeting_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                                                <a class="btn-basic" href="meeting_attachment.php?mid=<?= $current_meeting_row['meeting_id'] ?>">
                                                                    رفع ملف المجلس النهائي
                                                                </a>
                                                            </div>
                                                        <?php } else { ?>
                                                            <div class="current-meeting-buttons">
                                                                <form method="get" action="current_meeting_subject.php">
                                                                    <input type="hidden" value="<?=$current_meeting_row['meeting_id']?>" name="mid">
                                                                    <button class="btn-basic">موضوعات المجلس</button>
                                                                </form>
                                                                <a href="subjects_table.php" class="btn-basic">عرض ملف جدول الاعمال</a>
                                                                <a href="subjects_decisions.php?mid=<?= $current_meeting_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                                                <a class="btn-basic" href="meeting_attachment.php?mid=<?= $current_meeting_row['meeting_id'] ?>">عرض ملف المجلس النهائي</a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                        <?php break;
                                                }
                                                ?>
                                    <?php break;
                                }
                            }
                        }
                        else
                        {
                            ?>
                            <main id='empty' class='empty-meeting'>
                                <h4>لا يوجد مجلس حالي الان</h4>
                            </main>
                            <?php
                        }
                        $past_meetings_stmt = $conn->prepare("SELECT 
                                                                        m.*, 
                                                                        f.formation_number 
                                                                    FROM 
                                                                        p39_meeting as m 
                                                                            JOIN 
                                                                        p39_formation as f
                                                                            ON m.formation_id = f.formation_id 
                                                                                   AND m.is_current = 0 
                                                                    ORDER BY 
                                                                        meeting_id DESC");
                        $past_meetings_stmt->execute();
                        $past_meetings_result = $past_meetings_stmt->get_result();
                        $past_meetings_count = 0;

                        # Check if there are past meetings
                        if ($past_meetings_result->num_rows > 0)
                        { ?>
                            <div class="old-meetings"> <?php
                            while ($past_meetings_row = $past_meetings_result->fetch_assoc())
                            {
                                /* Check if the user exists in the formation of this meeting and is allowed to view it,
                                 * and if not skip that meeting
                                 * */
                                if (!in_array($past_meetings_row["formation_id"], $_SESSION["formation_ids"])
                                    AND !$_SESSION["admin"])
                                {
                                    continue;
                                }
                                if ($past_meetings_count == 0) { ?>
                                <h3>المجالس السابقة</h3>
                                <?php }
	                            ?>
                                <div class="old-meeting-box">
                                    <div class="row">
                                        <div class="col">
                                            <h4>
                                                رقم المجلس:
                                                <span class="meeting-number">
                                                    <?=$past_meetings_row["meeting_number"]?>
                                                </span>
                                            </h4>
                                            <h4>
                                                رقم تشكيل المجلس:
                                                <span class="meeting-formation-number">
                                                    <?=$past_meetings_row["formation_number"]?>
                                                </span>
                                            </h4>
                                            <h4>
                                                بتاريخ:
                                                <span class="meeting-month">
                                                    <?=$past_meetings_row["meeting_month"]?>
                                                </span>
                                                /
                                                <span class="meeting-year">
                                                    <?=$past_meetings_row["meeting_year"]?>
                                                </span>
                                            </h4>
                                            <h4>
                                                حالة المجلس:
	                                            <?php
	                                            switch ($past_meetings_row["status"])
	                                            {
		                                            case "pending":
			                                            echo "غير مؤكد";
			                                            break;
		                                            case "confirmed":
			                                            echo "مؤكد";
			                                            break;
		                                            case "finished":
			                                            echo "نهائي";
			                                            break;
		                                            default:
			                                            echo $past_meetings_row["status"];
			                                            break;
	                                            }
	                                            ?>
                                            </h4>
                                        </div>
	                                    <?php if ($_SESSION["admin"]) {
		                                    $subject_count_stmt = $conn->prepare("SELECT 
                                                                                            COUNT(subject_id) as s
                                                                                        FROM 
                                                                                            p39_subject 
                                                                                        WHERE 
                                                                                            meeting_id = ?");
		                                    $subject_count_stmt->bind_param("i", $past_meetings_row["meeting_id"]);
		                                    $subject_count_stmt->execute();
		                                    $subject_count_result = $subject_count_stmt->get_result();
		                                    $subject_count_row = $subject_count_result->fetch_assoc();

		                                    $attendance_count_stmt = $conn->prepare("SELECT 
                                                                                                COUNT(meeting_id) as ma
                                                                                            FROM 
                                                                                                p39_attendance 
                                                                                            WHERE 
                                                                                                meeting_id = ?");
		                                    $attendance_count_stmt->bind_param("i", $past_meetings_row["meeting_id"]);
		                                    $attendance_count_stmt->execute();
		                                    $attendance_count_result = $attendance_count_stmt->get_result();
		                                    $attendance_count_row = $attendance_count_result->fetch_assoc();

		                                    $subject_decision_stmt = $conn->prepare("SELECT
                                                                                                SUM(d.decision_type_id = 1) AS d1,
                                                                                                SUM(d.decision_type_id = 2) AS d2
                                                                                            FROM
                                                                                                p39_decision as d
                                                                                            JOIN p39_subject AS s
                                                                                            ON
                                                                                                s.subject_id = d.subject_id 
                                                                                                    AND 
                                                                                                s.meeting_id = ?");
		                                    $subject_decision_stmt->bind_param("i", $past_meetings_row["meeting_id"]);
		                                    $subject_decision_stmt->execute();
		                                    $subject_decision_result = $subject_decision_stmt->get_result();
		                                    $subject_decision_row = $subject_decision_result->fetch_assoc();

		                                    $exec_stmt = $conn->prepare("SELECT
                                                                                    SUM(d.needs_action = 1 AND d.is_action_done = 1) AS d1,
                                                                                    SUM(d.needs_action = 1 AND d.is_action_done = 0) AS d0
                                                                                FROM
                                                                                    p39_decision as d
                                                                                JOIN p39_subject AS s
                                                                                ON
                                                                                    s.subject_id = d.subject_id AND s.meeting_id = ?");
		                                    $exec_stmt->bind_param("i", $past_meetings_row["meeting_id"]);
		                                    $exec_stmt->execute();
		                                    $exec_result = $exec_stmt->get_result();
		                                    $exec_row = $exec_result->fetch_assoc();
		                                    ?>
                                            <div class="col">
                                                <div class="row">
                                                    <div class="card">
                                                        <div class="card-box">
						                                    <?= $subject_count_row["s"] == NULL
							                                    ? 0
							                                    : $subject_count_row["s"] ?>
                                                        </div>
                                                        <div class="card-text">عدد الموضوعات</div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-box">
						                                    <?= $attendance_count_row["ma"] == NULL
                                                                ? 0
                                                                : $attendance_count_row["ma"] ?>
                                                        </div>
                                                        <div class="card-text">الاعضاء الحاضرين</div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="card">
                                                        <div class="card-box">
						                                    <?= $subject_decision_row["d1"] == NULL
							                                    ? 0
							                                    : $subject_decision_row["d1"] ?>
                                                        </div>
                                                        <div class="card-text">الموضوعات المقبولة </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-box">
						                                    <?= $subject_decision_row["d2"] == NULL
							                                    ? 0
							                                    : $subject_decision_row["d2"] ?>
                                                        </div>
                                                        <div class="card-text">الموضوعات المرفوضة</div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="card">
                                                        <div class="card-box">
						                                    <?= $exec_row["d1"] == NULL
							                                    ? 0
							                                    : $exec_row["d1"] ?>
                                                        </div>
                                                        <div class="card-text">قرارات منفذة</div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-box">
						                                    <?= $exec_row["d0"] == NULL
							                                    ? 0
							                                    : $exec_row["d0"] ?>
                                                        </div>
                                                        <div class="card-text">قرارات غير منفذة</div>
                                                    </div>
                                                </div>
                                            </div>
	                                    <?php }
			                            if($_SESSION["admin"]):
                                            ?>
                                            <div class="col">
                                                <a href="subjects_decisions.php?mid=<?= $past_meetings_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                                <a class="btn-basic" href="meeting_attachment.php?mid=<?= $past_meetings_row['meeting_id'] ?>">
                                                    رفع ملف المجلس النهائي
                                                </a>
                                            </div>
			                            <?php else: ?>
                                            <div class="col">
                                                <?php if ($past_meetings_row["status"] == "finished") { ?>
                                                    <a href="meeting_attachment.php?mid=<?= $past_meetings_row['meeting_id'] ?>" class="btn-basic">
                                                        عرض ملف المجلس النهائي</a>
                                                <?php } else { ?>
                                                    <button type="button" title="لا يوجد ملف مجلس نهائي" class=
                                                    "btn-basic disabled" disabled>عرض ملف المجلس النهائي</button>
                                                <?php } ?>
                                                <a href="subjects_decisions.php?mid=<?= $past_meetings_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                            </div>
			                            <?php
			                            endif;
			                            ?>
                                    </div>
                                </div>
	                            <?php
	                            $past_meetings_count++;
                            }
	                        ?> </div> <?php
                        } ?>
                        </div>
                        <?php
                        if (@$_SESSION["admin"]):
                            $current_formation_stmt = $conn->prepare("SELECT formation_id FROM p39_formation WHERE is_current = 1");
                            $current_formation_stmt->execute();
                            $current_formation_result = $current_formation_stmt->get_result();
                            $current_formation_exists = $current_formation_result->num_rows > 0;
                            # Add New Meetings
                            if ($current_meeting_exists) { ?>
                                <div class="add-meeting">
                                    <button disabled title=" يجب تحويل المجلس الحالي لمجلس سابق أولًا "
                                            class="btn-basic disabled">
                                        إضافة مجلس جديد
                                    </button>
                                </div>
                            <?php } elseif (!$current_formation_exists) { ?>
                                <div class="add-meeting">
                                    <button disabled title=" يجب إضافة تشكيل حالي أولًا "
                                            class="btn-basic disabled">
                                        إضافة مجلس جديد
                                    </button>
                                </div>
                            <?php } else { ?>
                                <div class="add-meeting">
                                    <a href="add_meeting.php" class="btn-basic">اضافة مجلس جديد</a>
                                </div>
                            <?php } ?>
                        <?php endif;
                    elseif (!empty($_GET["search"])):
	                    switch ($_GET["f"])
	                    {
		                    case "fn":
			                    $search_stmt = "SELECT 
                                                    m.*,  
                                                    f.formation_number 
                                                FROM 
                                                    p39_meeting as m
                                                        JOIN 
                                                        p39_formation as f
                                                            ON m.formation_id = f.formation_id 
                                                                   AND f.formation_number LIKE ?";
			                    break;
		                    case "mn":
			                    $search_stmt = "SELECT 
                                                    m.*,  
                                                    f.formation_number 
                                                FROM 
                                                    p39_meeting as m
                                                        JOIN p39_formation as f
                                                            ON m.formation_id = f.formation_id 
                                                                   AND m.meeting_number LIKE ?";
			                    break;
		                    case "my":
			                    $search_stmt = "SELECT 
                                                    m.*,  
                                                    f.formation_number 
                                                FROM 
                                                    p39_meeting as m
                                                        JOIN p39_formation as f
                                                            ON m.formation_id = f.formation_id 
                                                                   AND m.meeting_year LIKE ?";
			                    break;
	                    }
	                    $restricted_search_count = 0;
	                    $search_result = Search($conn, NULL, @$search_stmt);
	                    @$search_result_count = $search_result->num_rows;
	                    if (@$search_result_count > 0)
	                    {
		                    while ($search_row = $search_result->fetch_assoc())
		                    {
			                    /* Check if the user exists in the formation of this meeting and is allowed to view it,
									* and if not skip that meeting
									* */
			                    if (!in_array($search_row["formation_id"], $_SESSION["formation_ids"])
                                    AND !$_SESSION["admin"])
			                    {
				                    $restricted_search_count += 1;
				                    if ($restricted_search_count == $search_result_count)
				                    {
					                    ?>
                                        <div class='current-meeting'>
                                            <main id='empty' class='empty-meeting'>
                                                <h4>عذرًا، لا يوجد مجالس تطابق رقم التشكيل</h4>
                                            </main>
                                        </div>
					                    <?php
				                    }
				                    continue;
			                    }
			                    if ($search_row["is_showed"] == "0" AND !$_SESSION["admin"])
			                    {
				                    $restricted_search_count += 1;
				                    if ($restricted_search_count == $search_result_count)
				                    {
					                    ?>
                                        <div class='current-meeting'>
                                            <main id='empty' class='empty-meeting'>
                                                <h4>عذرًا، لا يوجد مجالس تطابق رقم التشكيل</h4>
                                            </main>
                                        </div>
					                    <?php
				                    }
				                    continue;
			                    }
			                    ?>
                                <div class="old-meeting-box">
                                    <div class="row">
                                        <div class="col">
                                            <h4>
                                                رقم المجلس:
                                                <span class="meeting-number">
                                                <?=$search_row["meeting_number"]?>
                                            </span>
                                            </h4>
                                            <h4>
                                                رقم تشكيل المجلس:
                                                <span class="meeting-formation-number">
                                                <?=$search_row["formation_number"]?>
                                            </span>
                                            </h4>
                                            <h4>
                                                بتاريخ:
                                                <span class="meeting-month">
                                                <?=$search_row["meeting_month"]?>
                                            </span>
                                                /
                                                <span class="meeting-year">
                                                <?=$search_row["meeting_year"]?>
                                            </span>
                                            </h4>
                                            <h4>
                                                حالة المجلس:
                                                <span>
                                                    <?php
                                                    switch ($search_row["status"])
                                                    {
                                                        case "pending":
                                                            echo "غير مؤكد";
                                                            break;
                                                        case "confirmed":
                                                            echo "مؤكد";
                                                            break;
                                                        case "finished":
                                                            echo "نهائي";
                                                            break;
                                                        default:
                                                            echo $search_row["status"];
                                                            break;
                                                    } ?>
                                                </span>
                                            </h4>
                                            
                                        </div>
					                    <?php
					                    if($_SESSION["admin"]):
						                    ?>
                                            <div class="col">
                                                <a href="subjects_decisions.php?mid=<?= $search_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                                <a class="btn-basic" href="meeting_attachment.php?mid=<?= $search_row['meeting_id'] ?>">
                                                    رفع ملف المجلس النهائي
                                                </a>
                                            </div>
					                    <?php
					                    else:
						                    ?>
                                            <div class="col">
                                                <a class="btn-basic" href="meeting_attachment.php?mid=<?= $search_row['meeting_id'] ?>">عرض ملف المجلس النهائي</a>
                                                <a href="subjects_decisions.php?mid=<?= $search_row['meeting_id'] ?>" class="btn-basic">محضر الاجتماع</a>
                                            </div>
					                    <?php
					                    endif;
					                    ?>
                                    </div>
                                </div>
			                    <?php
		                    }
	                    }
	                    else
	                    {
		                    ?>
                            <div class='current-meeting'>
                                <main id='empty' class='empty-meeting'>
                                    <h4>عذرًا، لا يوجد مجالس تطابق البحث</h4>
                                </main>
                            </div>
		                    <?php
	                    }
                    else :
                        ?>
                        <div class='current-meeting'>
                            <main id='empty' class='empty-meeting'>
                                <h4>عذرًا، لا يوجد مجالس تطابق البحث</h4>
                            </main>
                        </div>
                    <?php endif; ?>
            </div>
        </main>
	<?php endif;
    Footer();
    ?>
    
    
    


    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>
</html>