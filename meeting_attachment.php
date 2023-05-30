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
<?php Head("ملف المجلس الموثق"); ?>

<body dir="rtl">
    <?php
    Headers();
    if (is_logged_in()) :
        Nav();
	    $search = clean_data($_GET["mid"]);
	    $meeting_num_stmt = $conn->prepare("SELECT 
                                                        meeting_number, 
                                                        formation_id as fid
                                                    FROM 
                                                        p39_meeting 
                                                    WHERE 
                                                        meeting_id = ?");
	    $meeting_num_stmt->bind_param("i", $search);
	    $meeting_num_stmt->execute();
	    $meeting_num_result = $meeting_num_stmt->get_result();
	    $meeting_num_row = $meeting_num_result->fetch_assoc();
	    $meeting_number = $meeting_num_row["meeting_number"];
	    $meeting_num_stmt->close();
        if (in_array($meeting_num_row["fid"], $_SESSION["formation_ids"]) || $_SESSION["admin"]) { ?>
            <main class="attachment-content">
                <div class="container">
                    <!-- عنوان الصفحة -->
                    <div class="title">
                        <h1>الملفات الموثقة لمجلس رقم <?= $meeting_number ?></h1>
                    </div>
                    <?php
                    $meeting_att_stmt = $conn->prepare("SELECT 
                                                                    * 
                                                                FROM 
                                                                    p39_meeting_attachment 
                                                                WHERE 
                                                                    meeting_id = ? 
                                                                  AND 
                                                                    is_final = 1");
                    $meeting_att_stmt->bind_param("i", $search);
                    $meeting_att_stmt->execute();
                    $meeting_att_result = $meeting_att_stmt->get_result();
                    $meeting_att_exist = $meeting_att_result->num_rows > 0;
                    if (!$meeting_att_exist) { ?>
                        <div>
                            <h3>ملفات المجلس الموثق</h3>
                        </div>
                        <main id="empty" class="empty">
                            <h4>لا يوجد ملفات الان</h4>
                        </main>
                    <?php } else { ?>
                        <div>
                            <h3>ملفات المجلس الموثق</h3>
                        </div>
                        <div class="attatchement-box">
                            <?php while ($meeting_att_row = $meeting_att_result->fetch_assoc()) { ?>
                                <div class="col">
                                    <?php switch (pathinfo($meeting_att_row["attachment_title"], PATHINFO_EXTENSION)) {
                                        case "pdf":
                                            echo '<img src="images/icons/pdf.svg" alt="pdf" class="attachment-picture">';
                                            break;

                                        case "png":
                                        case "gif":
                                        case "jpg":
                                        case "jfif":
                                        case "pjp":
                                        case "pjpeg":
                                        case "jpeg": ?>
                                            <img src="<?= $meeting_att_row['attachment_name'] ?>" alt="image" class="attachment-picture">
                                            <?php break;

                                        default:
                                            echo '<img src="images/icons/pdf.svg" alt="" class="attachment-picture">';
                                            break;
                                    }?>
                                    <a href="<?= $meeting_att_row['attachment_name'] ?>" target="_blank">
                                        <?= $meeting_att_row["attachment_title"] ?></a>
                                    <?php if ($_SESSION["admin"]) { ?>
                                        <button data-open-modal class="btn-basic">حذف</button>
                                        <dialog data-modal>
                                            <h4>هل تريد مسح الملف <?= $meeting_att_row["attachment_title"] ?> ؟</h4>
                                            <form method="post" action="deletion_code.php">
                                                <input type="hidden" name="meeting_id" value="<?= $search ?>">
                                                <input type="hidden" name="attachment_id"
                                                       value="<?= $meeting_att_row['attachment_id'] ?>">
                                                <!-- this is the delete code -->
                                                <button class="btn-basic" name="delete_meeting_attachment_btn">نعم</button>
                                                <button type="submit" formmethod="dialog" class="btn-basic">لا</button>
                                            </form>
                                        </dialog>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php }
                    $meeting_att_stmt1 = $conn->prepare("SELECT 
                                                                    * 
                                                                FROM 
                                                                    p39_meeting_attachment 
                                                                WHERE 
                                                                    meeting_id = ? 
                                                                  AND 
                                                                    is_final = 0");
                    $meeting_att_stmt1->bind_param("i", $search);
                    $meeting_att_stmt1->execute();
                    $meeting_att_result1 = $meeting_att_stmt1->get_result();
                    $meeting_att_exist1 = $meeting_att_result1->num_rows > 0;
                    if (!$meeting_att_exist1) { ?>
                        <div>
                            <h3>ملفات أخرى</h3>
                        </div>
                        <main id="empty" class="empty">
                            <h4>لا يوجد ملفات الان</h4>
                        </main>
                    <?php } else { ?>
                        <div>
                            <h3>ملفات أخرى</h3>
                        </div>
                        <div class="attatchement-box">
                            <?php while ($meeting_att_row1 = $meeting_att_result1->fetch_assoc()) { ?>
                                <div class="col">
                                    <?php switch (pathinfo($meeting_att_row1["attachment_title"], PATHINFO_EXTENSION)) {
                                        case "pdf":
                                            echo '<img src="images/icons/pdf.svg" alt="pdf" class="attachment-picture">';
                                            break;

                                        case "png":
                                        case "gif":
                                        case "jpg":
                                        case "jfif":
                                        case "pjp":
                                        case "pjpeg":
                                        case "jpeg": ?>
                                            <img src="<?= $meeting_att_row1['attachment_name'] ?>" alt="image" class="attachment-picture">
                                            <?php break;

                                        default:
                                            echo '<img src="images/icons/pdf.svg" alt="" class="attachment-picture">';
                                            break;
                                    } ?>
                                    <a href="<?= $meeting_att_row1['attachment_name'] ?>" target="_blank">
                                        <?= $meeting_att_row1["attachment_title"] ?></a>
                                    <?php if ($_SESSION["admin"]) { ?>
                                        <button data-open-modal class="btn-basic">حذف</button>
                                        <dialog data-modal>
                                            <h4>هل تريد مسح الملف <?= $meeting_att_row1["attachment_title"] ?> ؟</h4>
                                            <form method="post" action="deletion_code.php">
                                                <input type="hidden" name="meeting_id" value="<?= $search ?>">
                                                <input type="hidden" name="attachment_id"
                                                       value="<?= $meeting_att_row1['attachment_id'] ?>">
                                                <!-- this is the delete code -->
                                                <button class="btn-basic" name="delete_meeting_attachment_btn">نعم</button>
                                                <button type="submit" formmethod="dialog" class="btn-basic">لا</button>
                                            </form>
                                        </dialog>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <!--</div>
        </div>-->
                <div class="col dialog-col">
                    <?php if ($_SESSION["admin"]) { ?>
                    <button data-open-modal class="btn-basic add-attachment">رفع ملف المجلس الموثق</button>
                    <dialog data-modal>
                        <form method="post" action="addition_code.php" enctype="multipart/form-data">
                            <input type="hidden" name="meeting_id" value="<?= $search ?>">
                            <div class="btn-basic">
                                <label for="up1">
                                    رفع الملف الموثق
                                    <i class="fa-solid fa-upload"></i>
                                    <input id="up1" type="file" name="meeting_attachment[]" class="upload-button"
                                        accept="application/pdf, image/png, image/gif, image/jpeg" multiple />
                                </label>
                            </div>
                            <button type="submit" name="add_meeting_attachment_btn" class="btn-basic">رفع</button>
                            <button type="submit" formmethod="dialog" class="btn-basic dialog-btn">الغاء</button>
                        </form>
                        <div class="file-list"></div>
                    </dialog>
                    <button data-open-modal class="btn-basic add-attachment">رفع ملفات أخرى</button>
                    <dialog data-modal>
                        <form method="post" action="addition_code.php" enctype="multipart/form-data">
                            <input type="hidden" name="meeting_id" value="<?= $search ?>">
                            <div class="btn-basic">
                                <label for="up2">
                                    رفع ملفات أخرى
                                    <i class="fa-solid fa-upload"></i>
                                    <input id="up2" type="file" name="meeting_attachment1[]" class="upload-button"
                                        accept="application/pdf, image/png, image/gif, image/jpeg" multiple />
                                </label>
                            </div>
                            <button type="submit" name="add_meeting_attachment_btn1" class="btn-basic">رفع</button>
                            <button type="submit" formmethod="dialog" class="btn-basic dialog-btn">الغاء</button>
                        </form>
                        <div class="file-list"></div>
                </div>
                <?php } ?>
            </main>
        <?php } else {
            header("location: index.php", true, 303);
        } ?>
    <?php endif;
    footer();
    ?>

    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>
