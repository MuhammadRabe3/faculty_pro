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
<?php Head("عرض الأصوات"); ?>
<body dir="rtl">
<?php Headers();
if (is_admin()):
    Nav(); ?>
    <main class="add-formation-member-page ">
        <div class="container">
            <?php $sid = clean_data($_GET["sid"]);
            $sno_stmt = $conn->prepare("SELECT 
                                                    subject_number AS sno 
                                                FROM 
                                                    p39_subject 
                                                WHERE 
                                                    subject_id = ?");
            $sno_stmt->bind_param("i", $sid);
            $sno_stmt->execute();
            $sno_result = $sno_stmt->get_result();
            $sno_row = $sno_result->fetch_assoc();
            @$sno = $sno_row["sno"];
            $sno_stmt->close(); ?>
            <div class="title">
                <h1>عرض تصويت الأعضاء على موضوع رقم: <?= $sno ?></h1>
            </div>
            <?php
            $votes_stmt = $conn->prepare("SELECT
                                                    u.name AS un,
                                                    vt.vote_type_name AS v
                                                FROM
                                                    p39_vote AS v
                                                JOIN p39_users AS u
                                                ON
                                                    v.user_id = u.user_id
                                                JOIN p39_vote_type AS vt
                                                ON
                                                    v.vote_type_id = vt.vote_type_id
                                                WHERE
                                                    v.subject_id = ?");
            $votes_stmt->bind_param("i", $sid);
            $votes_stmt->execute();
            $votes_result = $votes_stmt->get_result();
            $votes_exist = $votes_result->num_rows > 0;
            if ($votes_exist) {
                $votes_count_stmt = $conn->prepare("SELECT
                                                        SUM(v.vote_type_id = 1) AS vy,
                                                        SUM(v.vote_type_id = 2) AS vn,
                                                        SUM(v.vote_type_id = 3) AS vab,
                                                        SUM(v.vote_type_id = 4) AS t7,
                                                        SUM(v.vote_type_id = 5) AS en
                                                    FROM
                                                        p39_vote AS v
                                                    WHERE 
                                                        v.subject_id = ?");
                $votes_count_stmt->bind_param("i", $sid);
                $votes_count_stmt->execute();
                $votes_count_result = $votes_count_stmt->get_result();
                $votes_count_row = $votes_count_result->fetch_assoc();
                $yes = $votes_count_row["vy"];
                $no = $votes_count_row["vn"];
                $ab = $votes_count_row["vab"];
                $t7 = $votes_count_row["t7"];
                $en = $votes_count_row["en"]; ?>
                <div class="piechart">
                    <div id="piechart"></div>
                </div>
                <div class="vote-table">
                    <table>
                        <thead>
                        <th>اسم العضو</th>
                        <th>التصويت</th>
                        </thead>
                        <tbody>
                        <?php while ($votes_row = $votes_result->fetch_assoc()) { ?>
                            <tr>
                                <td>
                                    <a href="members.php?f=mn&search=<?= $votes_row['un'] ?>"><?= $votes_row["un"] ?></a>
                                </td>
                                <td>
                                    <?= $votes_row["v"] ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="current-formation">
                    <main id="empty" class="empty-formation">
                        <h4>لا يوجد أصوات في هذا الموضوع</h4>
                    </main>
                </div>
            <?php } ?>
        </div>
    </main>
    <?php footer(); ?>

    <!-- Js Scripts and Plugins -->
    <script type="module" src="./js/main.js"></script>
    <script src="./js/select_all_button.js"></script>

    <!-- font Awesome -->
    <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load("current", {
        packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        const yes = <?php echo($yes)?>;
        const no = <?php echo($no)?>;
        const ab = <?php echo($ab)?>;
        const t7 = <?php echo($t7)?>;
        const en = <?php echo($en)?>;
        let data = google.visualization.arrayToDataTable([
            ['نوع', 'النسبة'],
            ['الموافقة', yes],
            ['الرفض', no],
            ['الامتناع', ab],
            ['تحفظ', t7],
            ['انسحاب', en]
        ]);

        let chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data);


    }
    </script>
</body>
<?php endif; ?>
</html>