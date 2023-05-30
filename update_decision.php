<?php
require_once "db.php";
require_once "functions.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php Head("تعديل قرار"); ?>

<body dir="rtl">
<?php Headers(); ?>
<?php if (is_admin()): ?>
	<?php Nav(); ?>
    <main class="add-member-page">
        <div class="container">
            <!-- عنوان الصفحة -->
            <div class="title">
                <h1>تعديل قرار</h1>
            </div>
            <form class="box" method="post" action="update_code.php" enctype="multipart/form-data">
				<?php
				$decision_type_stmt = $conn->prepare("SELECT 
                                                                * 
                                                            FROM 
                                                                p39_decision_type");
				$decision_type_stmt->execute();
				$decision_type_result = $decision_type_stmt->get_result();
                $decision_types = array();
				while ($decision_type_row = $decision_type_result->fetch_assoc()) {
					$decision_types[$decision_type_row["decision_type_id"]]
						= $decision_type_row["decision_type_name"];
				}
				$decision_type_stmt->close();
				$decision_stmt = $conn->prepare("SELECT 
                                                            * 
                                                        FROM 
                                                            p39_decision 
                                                        WHERE 
                                                            subject_id = ?");
				$decision_stmt->bind_param("i", $_POST["subject_id"]);
				$decision_stmt->execute();
				$decision_result = $decision_stmt->get_result();
				$decision_row = $decision_result->fetch_assoc(); ?>
                <div class="col">
                    <div class="row sp-row">
                        <h4>نوع القرار</h4>
                        <div class="row ">
							<?php foreach ($decision_types as $id => $name) { ?>
								<?php if ($decision_row["decision_type_id"] == $id) { ?>
                                    <div class="col">
                                        <h5><?= $name ?></h5>
                                        <input type="radio" name="decision_type" value="<?= $id ?>" checked>
                                    </div>
								<?php } else { ?>
                                    <div class="col">
                                        <h5><?= $name ?></h5>
                                        <input type="radio" name="decision_type" value="<?= $id ?>">
                                    </div>
								<?php } ?>
							<?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <h4>تفاصيل القرار</h4>
                        <textarea name="decision_details"><?= $decision_row["decision_details"] ?></textarea>
                    </div>
                    <div class="row sp-row">
                        <h4>هل له جواب تنفيذي؟</h4>
                        <div class="row ">
							<?php switch ($decision_row["needs_action"]) {
								case "0":
								case NULL: ?>
                                    <div class="col">
                                        <h5>نعم</h5>
                                        <input class="resp-yes" type="radio" name="needs_action" value="1">
                                    </div>
                                    <div class="col">
                                        <h5>لا</h5>
                                        <input class="resp-no" type="radio" name="needs_action" value="0" checked>
                                    </div>
									<?php break; ?>

								<?php case "1": ?>
                                    <div class="col">
                                        <h5>نعم</h5>
                                        <input class="resp-yes" type="radio" name="needs_action" value="1" checked>
                                    </div>
                                    <div class="col">
                                        <h5>لا</h5>
                                        <input class="resp-no" type="radio" name="needs_action" value="0">
                                    </div>
									<?php break; ?>
								<?php } ?>
                        </div>
                    </div>
                    <div class="row resp-to deactive">
                        <h4>الجواب التنفيذي موجه إلى:</h4>
                        <input type="text" name="action_to" value="<?= $decision_row['action_to'] ?>"/>
                    </div>

<!--                    <div class="row sp-row is-excu deactive">-->
<!--                        <h4>هل تم تنفيذ القرار؟</h4>-->
<!--                        <div class="row ">-->
<!--							--><?php //switch ($decision_row["is_action_done"]) {
//								case "0":
//								case NULL: ?>
<!--                                    <div class="col">-->
<!--                                        <h5>نعم</h5>-->
<!--                                        <input type="radio" name="is_action_done" value="1">-->
<!--                                    </div>-->
<!--                                    <div class="col">-->
<!--                                        <h5>لا</h5>-->
<!--                                        <input type="radio" name="is_action_done" value="0" checked>-->
<!--                                    </div>-->
<!--									--><?php //break; ?>
<!---->
<!--								--><?php //case "1": ?>
<!--                                    <div class="col">-->
<!--                                        <h5>نعم</h5>-->
<!--                                        <input type="radio" name="is_action_done" value="1" checked>-->
<!--                                    </div>-->
<!--                                    <div class="col">-->
<!--                                        <h5>لا</h5>-->
<!--                                        <input type="radio" name="is_action_done" value="0">-->
<!--                                    </div>-->
<!--									--><?php //break; ?>
<!--								--><?php //} ?>
<!--                        </div>-->
<!--                    </div>-->
                    <div class="row">
                        <h4>ملاحظات</h4>
                        <textarea name="decision_comments"><?= $decision_row["comments"] ?></textarea>
                    </div>

                    <div class="row sp2-row">
                        <form method="post" action="update_code.php">
                            <input type="hidden" name="decision_id" value="<?= $decision_row['decision_id'] ?>">
                            <input type="hidden" name="meeting_id" value="<?= $_POST['meeting_id'] ?>">
                            <button type="submit" class="btn-basic" name="update_decision_btn">
                                تعديل القرار
                            </button>
                        </form>
                        <?php
                        $decision_att_stmt = $conn->prepare("SELECT attachment_id FROM p39_decision_attachment WHERE decision_id = ?");
                        $decision_att_stmt->bind_param("i", $decision_row["decision_id"]);
                        $decision_att_stmt->execute();
                        $decision_att_result = $decision_att_stmt->get_result();
                        $decision_att_exist = $decision_att_result->num_rows > 0;
                        $decision_att_stmt->close();
                        if (!$decision_att_exist) { ?>
                            <form method="post" action="deletion_code.php">
                                <input type="hidden" name="decision_id" value="<?= $decision_row['decision_id'] ?>">
                                <input type="hidden" name="meeting_id" value="<?= $_POST['meeting_id'] ?>">
                                <button type="submit" class="btn-basic" name="delete_decision_btn">
                                    حذف القرار
                                </button>
                            </form>
                        <?php } else { ?>
                            <button type="button" class="btn-basic disabled" disabled
                                    title="لا يمكن حذف قرار له مرفقات">حذف القرار</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </main>
<?php endif; ?>
<?php footer(); ?>

<!-- Js Scripts and Plugins -->
<script type="module" src="./js/main.js"></script>
<script src="./js/add_update_decision.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>