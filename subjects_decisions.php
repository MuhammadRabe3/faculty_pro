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
Head("محضر الاجتماع");
?>

<body dir="rtl">
<?php Headers();
if (is_logged_in()) {
    Nav();
    $search = clean_data($_GET["mid"]);
    $meeting_number_stmt = $conn->prepare("SELECT 
                                                        meeting_number as mn,
                                                        meeting_date as md, 
                                                        formation_id as fid
                                                    FROM 
                                                        p39_meeting 
                                                    WHERE 
                                                        meeting_id = ?");
    $meeting_number_stmt->bind_param("i", $search);
    $meeting_number_stmt->execute();
    $meeting_number_result = $meeting_number_stmt->get_result();
    $meeting_number_row = $meeting_number_result->fetch_assoc();
    $meeting_number = $meeting_number_row["mn"];
    if (in_array($meeting_number_row["fid"], $_SESSION["formation_ids"]) || $_SESSION["admin"]) { ?>
        <main class="subjects-table-page">
            
                <div class="title">
                    <h1> محضر الاجتماع </h1>
                    <h3>محضر اجتماع لجنة إدارة البرامج الجديدة بالكلية مرحلتي البكالريوس والدراسات العليا</h3>
                    <h3>جلسة رقم <span><?= $meeting_number ?></span> بتاريخ <span><?= $meeting_number_row["md"] ?></span></h3>
                </div>
                <h4>انعقدت اللجنة في تمام الساعة الثالثة عصرا يوم الاربعاء <?= $meeting_number_row["md"] ?> وبرئاسة الأستاذ الدكتور صلاح الدين اسماعيل
                    عميد الكلية ورئيس اللجنة</h4>
                
                <h4 style="margin-top:10px;">بعضوية كلا من:</h4>
                <div class="meeting-attendance-page meetings-members">
                    <?php
                    $meeting_attendance_stmt = $conn->prepare("SELECT
                                                                            u.job_title AS jt,
                                                                            u.name AS n
                                                                        FROM
                                                                            p39_attendance AS a
                                                                        JOIN p39_users AS u
                                                                        ON
                                                                            a.user_id = u.user_id 
                                                                                AND 
                                                                            a.meeting_id = ?");
                    $meeting_attendance_stmt->bind_param("i", $search);
                    $meeting_attendance_stmt->execute();
                    $meeting_attendance_result = $meeting_attendance_stmt->get_result();
                    $meeting_attendance_exist = $meeting_attendance_result->num_rows > 0;
                    if ($meeting_attendance_exist) { ?>
                        <table class="attend-table">
                            <tbody>
                            <?php while ($meeting_attendance_row = $meeting_attendance_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $meeting_attendance_row["n"] ?></td>
                                    <td><?= $meeting_attendance_row["jt"] ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <main class="empty">
                            <h4>
                                لا يوجد أعضاء مسجلين حضور
                            </h4>
                        </main>
                    <?php } ?>
                </div>
                <div class="table-container">
                    <?php
                    $subject_stmt = $conn->prepare("SELECT
                                                                d.decision_details AS dd,
                                                                s.subject_name AS sn,
                                                                s.subject_details AS sd,
                                                                s.subject_number AS sno,
                                                                s.subject_id AS sid
                                                            FROM
                                                                p39_subject AS s
                                                            LEFT JOIN p39_decision AS d
                                                            ON
                                                                s.subject_id = d.subject_id 
                                                                    WHERE 
                                                                s.meeting_id = ?");
                    $subject_stmt->bind_param("i", $search);
                    $subject_stmt->execute();
                    $subject_result = $subject_stmt->get_result();
                    $subject_exists = $subject_result->num_rows > 0;
                    if ($subject_exists) { ?>
                    <table class="subjects-table">
                        <tbody>
                        <?php while ($subject_row = $subject_result->fetch_assoc()) {
                            $subject_pic_stmt = $conn->prepare("SELECT 
                                                                            picture_name
                                                                        FROM 
                                                                            p39_subject_picture 
                                                                        WHERE 
                                                                            subject_id = ?");
                            $subject_pic_stmt->bind_param("i", $subject_row["sid"]);
                            $subject_pic_stmt->execute();
                            $subject_pic_result = $subject_pic_stmt->get_result();
                            $subject_pic_exists = $subject_pic_result->num_rows > 0; ?>
                            <tr class="subject-row">
                                <td>الموضوع (<?= $subject_row["sno"] ?>)</td>
                                <td>
                                    <strong><?= $subject_row["sn"] ?></strong>
                                    <p><?= $subject_row["sd"] ?></p>
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
                                    <p><?= @$subject_row["dd"] == NULL
                                            ? "لا يوجد"
                                            : $subject_row["dd"] ?></p>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <main class="empty">
                            <h4>
                                لا يوجد موضوعات أو قرارات
                            </h4>
                        </main>
                    <?php } ?>
                </div>
                <div class="print-container">
                    <button class="btn-basic" onclick="window.print()">طباعة</button>
                </div>

            
        </main>
    <?php } else {
        header("location: index.php", true, 303);
    }
}
footer(); ?>

<!-- Js Scripts and Plugins -->
<script type="module" src="./js/main.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>