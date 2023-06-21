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
<?php Head("جدول الأعمال"); ?>
<body dir="rtl">
    <?php Headers(); ?>
    <?php if (is_logged_in()) { ?>
        <?php Nav(); ?>
        <?php
        $meeting_stmt = $conn->prepare("SELECT
                                                    meeting_id,
                                                    meeting_number,
                                                    meeting_date,
                                                    meeting_month,
                                                    meeting_year, 
                                                    formation_id as fid
                                                FROM
                                                    p39_meeting
                                                WHERE
                                                    is_current = 1");
        $meeting_stmt->execute();
        $meeting_result = $meeting_stmt->get_result();
        $meeting_row = $meeting_result->fetch_assoc();
        $meeting_id = @$meeting_row["meeting_id"];
        $n = 1;
        if (in_array($meeting_row["fid"], $_SESSION["formation_ids"]) || $_SESSION["admin"]) { ?>
            <main class="subjects-table-page">
                
                    <div class="title">
                        <h1> جدول الأعمال </h1>
    <!--                </div>-->
                    <?php
    //                $subject_table_stmt = $conn->prepare("SELECT
    //                                                                subject_name,
    //                                                                subject_details
    //                                                            FROM
    //                                                                p39_subject
    //                                                            WHERE meeting_id = ? AND ");
                    $subject_table_stmt = $conn->prepare("SELECT
                                                                    subject_id,
                                                                    subject_name,
                                                                    subject_details
                                                                FROM
                                                                    p39_subject 
                                                                WHERE 
                                                                    meeting_id = ?
                                                                ORDER BY
                                                                    - order_id
                                                                DESC");
                    $subject_table_stmt->bind_param("i", $meeting_id);
                    $subject_table_stmt->execute();
                    $subject_table_result = $subject_table_stmt->get_result();
                    if ($subject_table_result->num_rows > 0) { ?>
                        <h3>جدول أعمال لجنة مجلس الكلية مرحلتي البكالريوس والدراسات العليا</h3>
                        <h3>جلسة رقم
                            <span><?= $meeting_row["meeting_number"] ?></span>
                            شهر
                            <span><?= $meeting_row["meeting_month"] ?></span>
                            لسنة
                            <span><?= $meeting_row["meeting_year"]?></span>
                        </h3>
                        <h3> المنعقدة بتاريخ <span><?= $meeting_row["meeting_date"] ?></span></h3>
                        </div>
                        <div class="table-container">
                            <table class="subjects-table">
                                <tbody>
                                <?php while ($subject_table_row = $subject_table_result->fetch_assoc()) {
                                    $subject_attachment_stmt = $conn->prepare("SELECT * FROM p39_subject_picture WHERE subject_id = ?");
                                    $subject_attachment_stmt->bind_param("i", $subject_table_row["subject_id"]);
                                    $subject_attachment_stmt->execute();
                                    $subject_attachment_result = $subject_attachment_stmt->get_result();
                                    ?>
                                    <tr class="subject-row">
                                        <td>الموضوع <?= $n ?></td>
                                        <td>
                                            <strong><?= $subject_table_row["subject_name"] ?></strong>
                                            <p><?= $subject_table_row["subject_details"] ?></p>
    <!--                                        --><?php //if (!emp)?>
                                            <?php while ($subject_attachment_row = $subject_attachment_result->fetch_assoc()) { ?>
                                                <img src="<?= $subject_attachment_row['picture_name'] ?>" alt="صورة">
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr class="decision-row">
                                        <td>القرار</td>
                                        <td>
                                            <p></p>
                                        </td>
                                    </tr>
                                    <?php $n++ ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="print-container">
                            <button class="btn-basic" onclick="window.print()">طباعة</button>
                        </div>
                    <?php } else { ?>
                        </div>
                        <div class='current-meeting'>
                            <main id='empty' class='empty-meeting'>
                                <h4>عذرًا، لا يوجد جدول أعمال الآن</h4>
                            </main>
                        </div>
                    <?php } ?>
                
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