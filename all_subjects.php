<?php
require_once "db.php";
require_once "functions.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
} ?>

<!DOCTYPE html>
<html lang="en">
<?php Head("جميع الموضوعات"); ?>

<body dir="rtl">
<?php Headers();
if (is_admin()):
	Nav(); ?>
	<main class="all-subject-content">
		<div class="container">
			<div class="decision-title">
                <h1>جميع الموضوعات</h1>
			</div>
			<?php
            $subjects_count_stmt = $conn->prepare("SELECT
                                                                SUM(d.decision_type_id = 1) AS a_s,
                                                                SUM(d.decision_type_id = 2) AS r_s,
                                                                SUM(d.decision_type_id = 3) AS p_s,
                                                                SUM(d.needs_action = 1) AS e_s,
                                                                SUM(d.needs_action = 1 AND d.is_action_done = 1) AS e_s1,
                                                                SUM(s.subject_id > 0) AS s_all,
                                                                SUM(d.decision_id IS NULL) AS s_no
                                                            FROM
                                                                p39_subject AS s
                                                            LEFT JOIN p39_decision AS d
                                                            ON
                                                                s.subject_id = d.subject_id");
            $subjects_count_stmt->execute();
            $subjects_count_result = $subjects_count_stmt->get_result();
            $subjects_count_row = $subjects_count_result->fetch_assoc();
            ?>
            <!-- الفلتر للصفحة -->
            <div class="decision-filter">
                <div class="row">
                    <a class="btn-basic" href="all_subjects.php?f=all">
                        جميع الموضوعات (<?= $subjects_count_row["s_all"] == NULL
                            ? 0
                            : $subjects_count_row["s_all"] ?>)
                    </a>
                    <a class="btn-basic" href="all_subjects.php?f=s_no">
                        الموضوعات بدون قرارات (<?= $subjects_count_row["s_no"] == NULL
		                    ? 0
		                    : $subjects_count_row["s_no"] ?>)
                    </a>
                    <a class="btn-basic" href="all_subjects.php?f=a_s">
                        الموضوعات المقبولة (<?= $subjects_count_row["a_s"] == NULL
		                    ? 0
		                    : $subjects_count_row["a_s"] ?>)
                    </a>
                    <a class="btn-basic" href="all_subjects.php?f=r_s">
                        الموضوعات المرفوضة (<?= $subjects_count_row["r_s"] == NULL
		                    ? 0
		                    : $subjects_count_row["r_s"] ?>)
                    </a>
                    <a class="btn-basic" href="all_subjects.php?f=p_s">
                        الموضوعات المؤجلة (<?= $subjects_count_row["p_s"] == NULL
		                    ? 0
		                    : $subjects_count_row["p_s"] ?>)
                    </a>
                    <a class="btn-basic" href="all_subjects.php?f=e_s">
                        الموضوعات بقرار تنفيذي (<?= $subjects_count_row["e_s"] == NULL
		                    ? 0
		                    : $subjects_count_row["e_s"] ?>)
                    </a>
                    <a class="btn-basic" href="all_subjects.php?f=e_s1">
                        الموضوعات بقرار تنفيذي منفذ (<?= $subjects_count_row["e_s1"] == NULL
		                    ? 0
		                    : $subjects_count_row["e_s1"] ?>)
                    </a>
                </div>
            </div>
            <?php if (!empty($_GET)) {
                foreach ($_GET as $key => $value)
                {
                    switch ($key)
                    {
                        case "f":
                            switch ($value)
                            {
                                case "all":
                                    $subjects_stmt = $conn->prepare("SELECT
                                                                                s.order_id AS oid,
                                                                                s.subject_id AS sid,
                                                                                d.decision_details AS dd,
                                                                                s.subject_number AS sno,
                                                                                s.subject_name AS sn,
                                                                                s.subject_details AS sd,
                                                                                st.subject_type_name AS st,
                                                                                m.meeting_number AS mno,
                                                                                m.meeting_month AS mm,
                                                                                m.meeting_year AS my,
                                                                                dt.decision_type_name AS dt
                                                                            FROM
                                                                                p39_subject AS s
                                                                            LEFT JOIN p39_meeting AS m
                                                                            ON
                                                                                s.meeting_id = m.meeting_id
                                                                            LEFT JOIN p39_decision AS d
                                                                            ON
                                                                                s.subject_id = d.subject_id
                                                                            LEFT JOIN p39_decision_type AS dt
                                                                            ON
                                                                                d.decision_type_id = dt.decision_type_id
                                                                            LEFT JOIN p39_subject_type AS st
                                                                            ON
                                                                                s.subject_type_id = st.subject_type_id
                                                                            ORDER BY
                                                                                s.meeting_id");
                                    break;
                                case "s_no":
                                    $subjects_stmt = $conn->prepare("SELECT
                                                                                s.order_id AS oid,
                                                                                s.subject_id AS sid,
                                                                                d.decision_details AS dd,
                                                                                s.subject_number AS sno,
                                                                                s.subject_name AS sn,
                                                                                s.subject_details AS sd,
                                                                                st.subject_type_name AS st,
                                                                                m.meeting_number AS mno,
                                                                                m.meeting_month AS mm,
                                                                                m.meeting_year AS my,
                                                                                dt.decision_type_name AS dt
                                                                            FROM
                                                                                p39_subject AS s
                                                                            LEFT JOIN p39_meeting AS m
                                                                            ON
                                                                                s.meeting_id = m.meeting_id
                                                                            LEFT JOIN p39_decision AS d
                                                                            ON
                                                                                s.subject_id = d.subject_id
                                                                            LEFT JOIN p39_decision_type AS dt
                                                                            ON
                                                                                d.decision_type_id = dt.decision_type_id
                                                                            LEFT JOIN p39_subject_type AS st
                                                                            ON
                                                                                s.subject_type_id = st.subject_type_id
                                                                            WHERE 
                                                                                d.decision_id IS NULL
                                                                            ORDER BY
                                                                                s.meeting_id");
                                    break;
                                case "a_s":
                                    $subjects_stmt = $conn->prepare("SELECT
                                                                                s.order_id AS oid,
                                                                                s.subject_id AS sid,
                                                                                d.decision_details AS dd,
                                                                                s.subject_number AS sno,
                                                                                s.subject_name AS sn,
                                                                                s.subject_details AS sd,
                                                                                st.subject_type_name AS st,
                                                                                m.meeting_number AS mno,
                                                                                m.meeting_month AS mm,
                                                                                m.meeting_year AS my,
                                                                                dt.decision_type_name AS dt
                                                                            FROM
                                                                                p39_subject AS s
                                                                            LEFT JOIN p39_meeting AS m
                                                                            ON
                                                                                s.meeting_id = m.meeting_id
                                                                            LEFT JOIN p39_decision AS d
                                                                            ON
                                                                                s.subject_id = d.subject_id
                                                                            LEFT JOIN p39_decision_type AS dt
                                                                            ON
                                                                                d.decision_type_id = dt.decision_type_id
                                                                            LEFT JOIN p39_subject_type AS st
                                                                            ON
                                                                                s.subject_type_id = st.subject_type_id
                                                                            WHERE
                                                                                dt.decision_type_name = 'موافقة'
                                                                            ORDER BY
                                                                                s.meeting_id");
                                    break;
                                case "r_s":
                                    $subjects_stmt = $conn->prepare("SELECT
                                                                                s.order_id AS oid,
                                                                                s.subject_id AS sid,
                                                                                d.decision_details AS dd,
                                                                                s.subject_number AS sno,
                                                                                s.subject_name AS sn,
                                                                                s.subject_details AS sd,
                                                                                st.subject_type_name AS st,
                                                                                m.meeting_number AS mno,
                                                                                m.meeting_month AS mm,
                                                                                m.meeting_year AS my,
                                                                                dt.decision_type_name AS dt
                                                                            FROM
                                                                                p39_subject AS s
                                                                            LEFT JOIN p39_meeting AS m
                                                                            ON
                                                                                s.meeting_id = m.meeting_id
                                                                            LEFT JOIN p39_decision AS d
                                                                            ON
                                                                                s.subject_id = d.subject_id
                                                                            LEFT JOIN p39_decision_type AS dt
                                                                            ON
                                                                                d.decision_type_id = dt.decision_type_id
                                                                            LEFT JOIN p39_subject_type AS st
                                                                            ON
                                                                                s.subject_type_id = st.subject_type_id
                                                                            WHERE
                                                                                dt.decision_type_name = 'رفض'
                                                                            ORDER BY
                                                                                s.meeting_id");
                                    break;
                                case "p_s":
                                    $subjects_stmt = $conn->prepare("SELECT
                                                                                s.order_id AS oid,
                                                                                s.subject_id AS sid,
                                                                                d.decision_details AS dd,
                                                                                s.subject_number AS sno,
                                                                                s.subject_name AS sn,
                                                                                s.subject_details AS sd,
                                                                                st.subject_type_name AS st,
                                                                                m.meeting_number AS mno,
                                                                                m.meeting_month AS mm,
                                                                                m.meeting_year AS my,
                                                                                dt.decision_type_name AS dt
                                                                            FROM
                                                                                p39_subject AS s
                                                                            LEFT JOIN p39_meeting AS m
                                                                            ON
                                                                                s.meeting_id = m.meeting_id
                                                                            LEFT JOIN p39_decision AS d
                                                                            ON
                                                                                s.subject_id = d.subject_id
                                                                            LEFT JOIN p39_decision_type AS dt
                                                                            ON
                                                                                d.decision_type_id = dt.decision_type_id
                                                                            LEFT JOIN p39_subject_type AS st
                                                                            ON
                                                                                s.subject_type_id = st.subject_type_id
                                                                            WHERE
                                                                                dt.decision_type_name = 'تأجيل'
                                                                            ORDER BY
                                                                                s.meeting_id");
                                    break;
                                case "e_s":
                                    $subjects_stmt = $conn->prepare("SELECT
                                                                                s.order_id AS oid,
                                                                                s.subject_id AS sid,
                                                                                d.decision_details AS dd,
                                                                                s.subject_number AS sno,
                                                                                s.subject_name AS sn,
                                                                                s.subject_details AS sd,
                                                                                st.subject_type_name AS st,
                                                                                m.meeting_number AS mno,
                                                                                m.meeting_month AS mm,
                                                                                m.meeting_year AS my,
                                                                                dt.decision_type_name AS dt
                                                                            FROM
                                                                                p39_subject AS s
                                                                            LEFT JOIN p39_meeting AS m
                                                                            ON
                                                                                s.meeting_id = m.meeting_id
                                                                            LEFT JOIN p39_decision AS d
                                                                            ON
                                                                                s.subject_id = d.subject_id
                                                                            LEFT JOIN p39_decision_type AS dt
                                                                            ON
                                                                                d.decision_type_id = dt.decision_type_id
                                                                            LEFT JOIN p39_subject_type AS st
                                                                            ON
                                                                                s.subject_type_id = st.subject_type_id
                                                                            WHERE
                                                                                d.needs_action = 1
                                                                            ORDER BY
                                                                                s.meeting_id");
                                    break;
                                case "e_s1":
                                    $subjects_stmt = $conn->prepare("SELECT
                                                                                s.order_id AS oid,
                                                                                s.subject_id AS sid,
                                                                                d.decision_details AS dd,
                                                                                s.subject_number AS sno,
                                                                                s.subject_name AS sn,
                                                                                s.subject_details AS sd,
                                                                                st.subject_type_name AS st,
                                                                                m.meeting_number AS mno,
                                                                                m.meeting_month AS mm,
                                                                                m.meeting_year AS my,
                                                                                dt.decision_type_name AS dt
                                                                            FROM
                                                                                p39_subject AS s
                                                                            LEFT JOIN p39_meeting AS m
                                                                            ON
                                                                                s.meeting_id = m.meeting_id
                                                                            LEFT JOIN p39_decision AS d
                                                                            ON
                                                                                s.subject_id = d.subject_id
                                                                            LEFT JOIN p39_decision_type AS dt
                                                                            ON
                                                                                d.decision_type_id = dt.decision_type_id
                                                                            LEFT JOIN p39_subject_type AS st
                                                                            ON
                                                                                s.subject_type_id = st.subject_type_id
                                                                            WHERE
                                                                                d.needs_action = 1 AND d.is_action_done = 1
                                                                            ORDER BY
                                                                                s.meeting_id");
                                    break;
                            }
                            break;
                        default:
                            header("location: all_subjects.php?f=all");
                    }
                }
                $subjects_stmt->execute();
                $subjects_result = $subjects_stmt->get_result();
                while ($subjects_row = $subjects_result->fetch_assoc())
                { ?>
                    <div class="current-subject-foradmin">
                        <div class="box">
                            <div class="row">
                                <div class="col">
                                    <h4>
                                        ترتيب عرض الموضوع:
                                        <span>
                                            <?= $subjects_row["oid"] == NULL
                                                ? "لا يوجد"
                                                : $subjects_row["oid"] ?>
                                        </span>
                                    </h4>
                                    <h4>
                                        رقم إدخال الموضوع:
                                        <span>
                                            <?= $subjects_row["sno"] == NULL
	                                            ? "لا يوجد"
	                                            : $subjects_row["sno"] ?>
                                        </span>
                                    </h4>
                                    <h4>
                                        تصنيف الموضوع:
                                        <span>
                                            <?= $subjects_row["st"] == NULL
	                                            ? "لا يوجد"
	                                            : $subjects_row["st"] ?>
                                        </span>
                                    </h4>
                                    <h4>
                                        عنوان الموضوع:
                                        <span>
                                            <?= $subjects_row["sn"] ?>
                                        </span>
                                    </h4>
                                    <h4>
                                        مجلس رقم
                                        <span>
                                            (<?= $subjects_row["mno"] ?>)
                                        </span>
                                         لشهر
                                        <span>
                                            <?= $subjects_row["mm"] ?>
                                        </span>
                                        سنة
                                        <span>
                                            <?= $subjects_row["my"] ?>
                                        </span>
                                    </h4>
                                    <h4>
                                        نوع القرار:
                                        <span>
                                            <?= $subjects_row["dt"] == NULL
                                                ? "لا يوجد"
                                                : $subjects_row["dt"] ?>
                                        </span>
                                    </h4>
                                </div>
                                <div class="col">
                                    <button class="btn-basic subject-details-btn">
                                        تفاصيل الموضوع
                                    </button>
                                    <a class="btn-basic" href="votes.php?sid=<?=$subjects_row['sid']?>">عرض الأصوات</a>
                                </div>

                            </div>
                            <div class="current-subject-details deactive">
                                <div class="table-container">
                                    <table class="subjects-table">
                                        <tbody>
                                        <tr class="subject-row">
                                            <td>الموضوع (<?= $subjects_row["sno"] ?>)</td>
                                            <td>
                                                <strong><?= $subjects_row["sn"] ?></strong>
                                                <p><?= $subjects_row["sd"] ?></p>
                                                <?php
                                                $subject_pic_stmt = $conn->prepare("SELECT 
                                                                                        picture_name
                                                                                    FROM 
                                                                                        p39_subject_picture 
                                                                                    WHERE 
                                                                                        subject_id = ?");
                                                $subject_pic_stmt->bind_param("i", $subjects_row["sid"]);
                                                $subject_pic_stmt->execute();
                                                $subject_pic_result = $subject_pic_stmt->get_result();
                                                $subject_pic_exists = $subject_pic_result->num_rows > 0; ?>
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
                                                <p><?= @$subjects_row["did"] == NULL
                                                        ? "لا يوجد"
                                                        : $subjects_row["did"] ?></p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
            }
			else {
                header("location: all_subjects.php?f=all");
			} ?>
		</div>
	</main>
<?php endif;
footer(); ?>

<!-- Js Scripts and Plugins -->
<script type="module" src="./js/main.js"></script>
<script src="./js/subject_details.js"></script>

<!-- font Awesome -->
<script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>

</body>
</html>