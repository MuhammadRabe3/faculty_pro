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
<?php Head("أعضاء التشكيل"); ?>

<body dir="rtl">
<?php Headers();
if (is_logged_in()) {
    Nav();
    $search = clean_data($_GET["fid"]);
    $formation_num = $conn->prepare("SELECT formation_number, formation_id as fid FROM p39_formation WHERE formation_id = ?");
    $formation_num->bind_param("i", $search);
    $formation_num->execute();
    $formation_num_result = $formation_num->get_result();
    $formation_num_row = $formation_num_result->fetch_assoc();
    $formation_number = $formation_num_row["formation_number"];
    if (in_array($formation_num_row["fid"], $_SESSION["formation_ids"]) || $_SESSION["admin"]) {
        $formation_members_stmt = $conn->prepare("SELECT
                                                            fu.job_title AS jt,
                                                            u.name AS n
                                                        FROM
                                                            `p39_formation_user` AS fu
                                                        JOIN p39_users AS u
                                                        ON
                                                            u.user_id = fu.user_id AND fu.formation_id = ?");
        $formation_members_stmt->bind_param("i", $search);
        $formation_members_stmt->execute();
        $formation_members_result = $formation_members_stmt->get_result();
        $formation_members_exist = $formation_members_result->num_rows > 0; ?>
        <main class="formation-members-content">
            <div class="container">
                <div class="title">
                    <h1> سجل اعضاء التشكيل رقم <?= $formation_number ?> </h1>
                </div>
<!--                --><?php //if (@in_array($search, $_SESSION["formation_ids"]) OR $_SESSION["admin"]) { ?>
                    <?php if ($formation_members_exist) { ?>
                        <div class="meeting-attendance-page meetings-members">
                            <table class="attend-table">
                                <tbody>
                                <?php while ($formation_members_row = $formation_members_result->fetch_assoc()) { ?>
                                    <tr>
                                        <?php if ($_SESSION["admin"]) { ?>
                                        <td><a href="members.php?f=mn&search=<?= $formation_members_row["n"] ?>"><?= $formation_members_row["n"] ?></a></td>
                                        <?php } else { ?>
                                            <td><?= $formation_members_row["n"] ?></td>
                                        <?php } ?>
                                        <td><?= $formation_members_row["jt"] ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="print-container">
                            <button class="btn-basic" onclick="window.print()">طباعة</button>
                        </div>
                    <?php } else { ?>
                        <main id="empty" class="empty-member">
                            <h4>لا يوجد أعضاء في هذا التشكيل</h4>
                        </main>
                    <?php } ?>
<!--                --><?php //} else { ?>
<!--                    <main id="empty" class="empty-member">-->
<!--                        <h4>لا يوجد أعضاء في هذا التشكيل</h4>-->
<!--                    </main>-->
<!--                --><?php //} ?>
            </div>
        </main>
    <?php } else {
    header("location: formation.php", true, 303);}
}
footer(); ?>

<!-- Js Scripts and Plugins -->
<script type="module" src="./js/main.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
</body>

</html>