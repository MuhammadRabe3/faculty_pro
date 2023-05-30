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
Head("التصويت");
?>

<body dir="rtl">
<?php Headers();
if (is_logged_in() AND !$_SESSION["admin"] AND isset($_POST["voting_btn"])):
    Nav(); ?>
    <!-- ?main content of the page -->
    <main class="voting-content">
        <div class="container">
            <!-- عنوان الصفحة -->
            <div class="title">
                <h1>التصويت علي موضوع رقم: <span class="subject-num"><?= $_POST["subject_id"] ?></span></h1>
            </div>
            <?php
            $subject_vote_stmt = $conn->prepare("SELECT vote_type_id FROM p39_vote WHERE subject_id = ? AND user_id = ?");
            $subject_vote_stmt->bind_param("ii", $_POST["subject_id"], $_SESSION["user_id"]);
            $subject_vote_stmt->execute();
            $subject_vote_result = $subject_vote_stmt->get_result();
            $subject_vote_exists = $subject_vote_result->num_rows > 0;
            $subject_vote_row = $subject_vote_result->fetch_assoc();
            $vote_types_stmt = $conn->prepare("SELECT * FROM p39_vote_type");
            $vote_types_stmt->execute();
            $vote_types_result = $vote_types_stmt->get_result();
            if (!$subject_vote_exists) { ?>
                <div class="box">
                    <form action="addition_code.php" method="post">
                        <div class="row vote-radio">
                            <?php while ($vote_types_row = $vote_types_result->fetch_assoc()) { ?>
                                <div class="col">
                                    <h3><?= $vote_types_row["vote_type_name"] ?></h3>
                                    <input type="radio" name="vote" value="<?= $vote_types_row['vote_type_id'] ?>"/>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <input type="hidden" name="subject_id" value="<?= $_POST['subject_id'] ?>">
                            <button class="btn-basic" name="add_vote_btn">تصويت</button>
                        </div>
                    </form>
                </div>
            <?php } else { ?>
                <div class="box">
                    <form action="update_code.php" method="post">
                        <div class="row vote-radio">
				            <?php while ($vote_types_row = $vote_types_result->fetch_assoc()) { ?>
                                <?php if ($vote_types_row["vote_type_id"] == $subject_vote_row["vote_type_id"]) { ?>
                                    <div class="col">
                                        <h3><?= $vote_types_row["vote_type_name"] ?></h3>
                                        <input type="radio" name="vote" value="<?= $vote_types_row['vote_type_id'] ?>"
                                               checked/>
                                    </div>
                                <?php } else { ?>
                                    <div class="col">
                                        <h3><?= $vote_types_row["vote_type_name"] ?></h3>
                                        <input type="radio" name="vote" value="<?= $vote_types_row['vote_type_id'] ?>"/>
                                    </div>
                                <?php } ?>
				            <?php } ?>
                        </div>
                        <div class="row">
                            <input type="hidden" name="subject_id" value="<?= $_POST['subject_id'] ?>">
                            <button class="btn-basic" name="update_vote_btn">تصويت</button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </main>
<?php else:
    header("location: index.php", true, 303);
endif;
footer(); ?>

<!-- Js Scripts and Plugins -->
<script type="module" src="./js/main.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>