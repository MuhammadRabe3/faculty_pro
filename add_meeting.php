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
Head("اضافة مجلس");
?>

<body dir="rtl">
<?php
Headers();
if (is_admin()):
	Nav();
	$last_meeting_stmt = $conn->prepare("SELECT 
                                                    meeting_number 
                                                FROM 
                                                    p39_meeting 
                                                WHERE 
                                                    meeting_id = (SELECT 
                                                                      max(meeting_id) 
                                                                  FROM 
                                                                      p39_meeting)");
	$last_meeting_stmt->execute();
	$last_meeting_result = $last_meeting_stmt->get_result();
	$last_meeting_row = $last_meeting_result->fetch_assoc();
	@$last_meeting_number = $last_meeting_row["meeting_number"]; ?>
	<main class="add-member-page">
		<div class="container">
			<!-- عنوان الصفحة -->
			<div class="title">
				<h1>إضافة مجلس جديد</h1>
			</div>
			<form class="box" method="post" action="addition_code.php">
                <?php if (@$_SESSION["error"]["add"]) { ?>
                    <p class="login-error">يرجى إدخال جميع الحقول</p>
                    <?php unset($_SESSION["error"]["add"]);
                } ?>
				<div class="col">
					<div class="row">
						<h4>رقم المجلس</h4>
						<input type="number" name="number" required min="1"/>
						<?php if (!empty(@$last_meeting_number)) { ?>
							<h6>رقم المجلس السابق: <?= $last_meeting_number ?></h6>
						<?php } ?>
					</div>
					<div class="row">
						<?php
						$meeting_months_stmt = $conn->prepare("SELECT
                                                                        month,
                                                                        year
                                                                    FROM
                                                                        `p39_dates` AS d
                                                                    JOIN p39_formation AS f
                                                                    ON
                                                                        d.formation_id = f.formation_id AND f.is_current = 1
                                                                    WHERE
                                                                        d.month NOT IN(
                                                                        SELECT
                                                                            meeting_month
                                                                        FROM
                                                                            p39_meeting
                                                                        WHERE
                                                                            formation_id = f.formation_id
                                                                    )");
						$meeting_months_stmt->execute();
						$meeting_months_result = $meeting_months_stmt->get_result();
						?>
						<h4>الشهر</h4>
						<div class="select-basic">
							<select name="month" required>
								<option value="">اختر</option>
								<?php
								$meeting_years = array();
								while ($meeting_months_row = $meeting_months_result->fetch_assoc())
								{
									if (!in_array($meeting_months_row["year"], $meeting_years))
									{
										$meeting_years[] = $meeting_months_row["year"];
									}
									?>
									<option value='<?= $meeting_months_row["month"] ?>'>
										<?= $meeting_months_row["month"] ?></option>
								<?php }
								?>
							</select>
						</div>
					</div>
					<div class="row">
						<h4>السنة</h4>
						<div class="select-basic">
							<select name="year" required>
								<option value="">اختر</option>
								<?php foreach ($meeting_years as $year) { ?>
									<option value="<?= $year ?>"><?= ($year + 1) . '-' . $year ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="row">
						<button type="submit" class="btn-basic" name="add_meeting_btn">اضافة مجلس جديد</button>
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
