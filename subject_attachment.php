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
<?php Head("مرفقات الموضوعات"); ?>

<body dir="rtl">
    <?php
    Headers();
    if (is_admin()) : ?>
        <?php Nav();
	    $search = clean_data($_GET["sid"]);
	    $subject_num_stmt = $conn->prepare("SELECT subject_number FROM p39_subject WHERE subject_id = ?");
	    $subject_num_stmt->bind_param("i", $search);
	    $subject_num_stmt->execute();
	    $subject_num_result = $subject_num_stmt->get_result();
	    $subject_num_row = $subject_num_result->fetch_assoc();
	    $subject_number = $subject_num_row["subject_number"];
	    $subject_num_stmt->close(); ?>
        <main class="attachment-content">
            <div class="container">
                <!-- عنوان الصفحة -->
                <div class="title">
                    <h1>المرفقات الخاصة بموضوع رقم <?= $subject_number == NULL
                            ? "لا يوجد"
                            : $subject_number ?></h1>
                </div>
                <?php
                $subject_att_stmt = $conn->prepare("SELECT * FROM p39_subject_attachment WHERE subject_id = ?");
                $subject_att_stmt->bind_param("i", $search);
                $subject_att_stmt->execute();
                $subject_att_result = $subject_att_stmt->get_result();
                $subject_att_exist = $subject_att_result->num_rows > 0;

                $subject_pic_stmt = $conn->prepare("SELECT * FROM p39_subject_picture WHERE subject_id = ?");
                $subject_pic_stmt->bind_param("i", $search);
                $subject_pic_stmt->execute();
                $subject_pic_result = $subject_pic_stmt->get_result();
                $subject_pic_exist = $subject_pic_result->num_rows > 0;
                if (!$subject_att_exist) { ?>
                    <div>
                        <h3>المرفقات</h3>
                    </div>
                    <main id="empty" class="empty">
                        <h4>لا يوجد مرفقات الآن</h4>
                    </main>
                <?php } else { ?>
                    <div>
                        <h3>المرفقات</h3>
                    </div>
                    <div class="attatchement-box">
                        <?php while ($subject_att_row = $subject_att_result->fetch_assoc()) { ?>
                            <div class="col">
                                <?php switch (pathinfo($subject_att_row["attachment_title"], PATHINFO_EXTENSION)) {
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
                                        <img src="<?= $subject_att_row['attachment_name'] ?>" alt="image" class="attachment-picture">
		                                <?php break;

	                                default:
		                                echo '<img src="images/icons/image.svg" alt="" class="attachment-picture">';
		                                break;
                                }?>
                                <a href="<?= $subject_att_row['attachment_name'] ?>" target="_blank">
                                    <?= $subject_att_row["attachment_title"] ?></a>
                                <!-- this is not delete button -->
                                <button data-open-modal class="btn-basic">حذف</button>
                                <dialog data-modal>
                                    <h4>هل تريد مسح الملف <?= $subject_att_row["attachment_title"] ?> ؟</h4>
                                    <form method="post" action="deletion_code.php">
                                        <input type="hidden" name="subject_id" value="<?= $search ?>">
                                        <input type="hidden" name="attachment_id"
                                               value="<?= $subject_att_row['attachment_id'] ?>">
                                        <!-- this is the delete code -->
                                        <button class="btn-basic" name="delete_subject_attachment_btn">نعم</button>
                                        <button type="submit" formmethod="dialog" class="btn-basic">لا</button>
                                    </form>
                                </dialog>
                            </div>
                        <?php } ?>
                    </div>
                <?php }
                if (!$subject_pic_exist) { ?>
                    <div>
                        <h3>صور تفاصيل الموضوع</h3>
                    </div>
                    <main id="empty" class="empty">
                        <h4>لا يوجد صور الآن</h4>
                    </main>
                <?php } else { ?>
                    <div>
                        <h3>صور تفاصيل الموضوع</h3>
                    </div>
                    <div class="attatchement-box">
		                <?php while ($subject_pic_row = $subject_pic_result->fetch_assoc()) { ?>
                            <div class="col">
				                <?php switch (pathinfo($subject_pic_row["picture_title"], PATHINFO_EXTENSION)) {
					                case "png":
                                    case "gif":
                                    case "jpg":
                                    case "jfif":
                                    case "pjp":
                                    case "pjpeg":
                                    case "jpeg": ?>
						                <img src="<?= $subject_pic_row['picture_name'] ?>" alt="image" class="attachment-picture">
						                <?php break;

					                default:
						                echo '<img src="images/icons/image.svg" alt="" class="attachment-picture">';
						                break;
                                } ?>
                    <a href="<?= $subject_pic_row['picture_name'] ?>" target="_blank">
                        <?= $subject_pic_row["picture_title"] ?></a>
                    <?php if ($_SESSION["admin"]) { ?>
                    <button data-open-modal class="btn-basic">حذف</button>
                    <dialog data-modal>
                        <h4>هل تريد مسح الملف <?= $subject_pic_row["picture_title"] ?> ؟</h4>
                        <form method="post" action="deletion_code.php">
                            <input type="hidden" name="subject_id" value="<?= $search ?>">
                            <input type="hidden" name="picture_id" value="<?= $subject_pic_row['picture_id'] ?>">
                            <!-- this is the delete code -->
                            <button class="btn-basic" name="delete_subject_picture_btn">نعم</button>
                            <button type="submit" formmethod="dialog" class="btn-basic">لا</button>
                        </form>
                    </dialog>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <div class="col dialog-col">
            <?php if ($_SESSION["admin"]) { ?>
                <button data-open-modal class="btn-basic add-attachment">رفع مرفقات </button>
                <dialog data-modal>
                    <form method="post" action="addition_code.php" enctype="multipart/form-data">
                        <input type="hidden" name="subject_id" value="<?= $search ?>">
                        <div class="btn-basic">
                            <label for="up1">
                                رفع مرفق
                                <i class="fa-solid fa-upload"></i>
                                <input id="up1" type="file" name="subject_attachment[]" class="upload-button"
                                    accept="application/pdf, image/png, image/gif, image/jpeg" multiple />
                            </label>
                        </div>
                        <button type="submit" name="add_subject_attachment_btn" class="btn-basic">رفع</button>
                        <button type="submit" formmethod="dialog" class="btn-basic dialog-btn">إلغاء</button>
                    </form>
                    <div class="file-list"></div>
                </dialog>
                <button data-open-modal class="btn-basic add-attachment">رفع صورة</button>
                <dialog data-modal>
                    <form method="post" action="addition_code.php" enctype="multipart/form-data">
                        <input type="hidden" name="subject_id" value="<?= $search ?>">
                        <div class="btn-basic">
                            <label for="up2">
                                رفع صورة
                                <i class="fa-solid fa-upload"></i>
                                <input id="up2" type="file" class="upload-button" name="subject_picture[]"
                                    accept="image/png, image/gif, image/jpeg" multiple />
                            </label>
                        </div>
                        <button type="submit" name="add_subject_picture_btn" class="btn-basic">رفع</button>
                        <button type="submit" formmethod="dialog" class="btn-basic dialog-btn">إلغاء</button>
                    </form>
                    <div class="file-list"></div>
                </dialog>
            <?php } ?>
        </div>
    </main>
    <?php endif;
    footer(); ?>

    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>
