<?php
require_once "db.php";
require_once "functions.php";

if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
    <?php
    Head("الأعضاء");
    ?>
    <body dir="rtl">
        <?php
        Headers();
        if(is_admin()):
            Nav();?>
            <!-- *Main Members Page Content  -->
            <!-- !Admin Apperance -->
            <!-- *Add "deactive" to Class Here ↓↓ To Test-->
            <main class="members-content">
                <div class="container">
                <div class="search-container">
                        <?php SearchBar(); ?>
                    </div>
                    <!-- عنوان الصفحة -->
                    <div class="members-title">
                        <h1>الأعضاء</h1>
                    </div>
                    
                    <?php
                    // Get all departments info
                    $departments_stmt = $conn->prepare("SELECT * FROM p39_department");
                    $departments_stmt->execute();
                    $departments_result = $departments_stmt->get_result();
                    $departments = array();
                    while ($departments_row = $departments_result->fetch_assoc())
                    {
                        $departments[$departments_row["department_id"]] = $departments_row["department_name"];
                    }
                    $departments_stmt->close();

                    // Get All Job Types
                    $job_types_stmt = $conn->prepare("SELECT * FROM p39_job_type");
                    $job_types_stmt->execute();
                    $job_types_result = $job_types_stmt->get_result();
                    $job_types = array();
                    while ($job_types_row = $job_types_result->fetch_assoc())
                    {
                        $job_types[$job_types_row["job_type_id"]] = $job_types_row["job_type_name"];
                    }
                    $job_types_stmt->close();

                    // Get All Job Ranks
                    $job_ranks_stmt = $conn->prepare("SELECT * FROM p39_job_rank");
                    $job_ranks_stmt->execute();
                    $job_ranks_result = $job_ranks_stmt->get_result();
                    $job_ranks = array();
                    while ($job_ranks_row = $job_ranks_result->fetch_assoc())
                    {
                        $job_ranks[$job_ranks_row["job_rank_id"]] = $job_ranks_row["job_rank_name"];
                    }
                    $job_ranks_stmt->close();

                    # $users_stmt = $conn->prepare("SELECT
                    #                                        *
                    #                                    FROM
                    #                                        `p39_users`
                    #                                    WHERE
                    #                                        user_id IN (
                    #                                            SELECT
                    #                                                user_id
                    #                                            FROM
                    #                                                p39_formation_user
                    #                                            WHERE
                    #                                                formation_id = (
                    #                                                    SELECT
                    #                                                        MIN(formation_id)
                    #                                                    FROM
                    #                                                        p39_formation_user
                    #                                                    )
                    #                                            )");
                    $users_stmt = $conn->prepare("SELECT * FROM p39_users");
                    $users_stmt->execute();
                    $users_result = $users_stmt->get_result();
                    $users_exist = $users_result->num_rows > 0;
                    if (!$users_exist && empty($_GET)):
                        ?>
                        <div class="members">
                            <main id="empty" class="empty-member">
                                <h4>لا يوجد أعضاء الآن</h4>
                            </main>
                        <?php
                    elseif ($users_exist && empty($_GET)):
                        $n = 1;
                        while ($users_row = $users_result->fetch_assoc())
                        {
                            ?>
                            <div class="member-box">
                                <div class="row">
                                    <div class="col">
                                        <h4>رقم العضو:
                                            <span class="member-number">
                                                <?=$n?>
                                            </span>
                                        </h4>
                                        <h4>اسم العضو:
                                            <span class="member-name">
                                                <?=$users_row["name"]?>
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="col">
<!--                                            <a href="update_member.php" class="btn-basic">تعديل بيانات العضو</a>-->
                                        <form method="post" action="update_member.php">
                                            <button class="btn-basic">تعديل بيانات العضو</button>
                                            <input type="hidden" name="user_id" value="<?=$users_row['user_id']?>">
                                        </form>
                                        <button class="btn-basic member-details-btn">تفاصيل العضو</button>
                                    </div>
                                </div>

                                <div class="member-details deactive">
                                    <div class="row">
                                        <div class="col">
                                            <img src="<?=$users_row['image']?>" alt="" class="member-image" />
                                        </div>
                                        <div class="col">
                                            <!--                                            <h4>نوع العضو: عضو مجلس</h4>-->
                                            <h4>الاسم : <?=$users_row["name"]?></h4>
                                            <!--                                            <h4>رقم العضو : 1</h4>-->
                                            <!--                                            <h4>رقم تشكيل المجلس المنضم له العضو:4</h4>-->
                                            <!--                                            <h4>رقم التليفون: 01102465132</h4>-->
                                            <h4>الايميل: <?=$users_row["email"]?></h4>
                                            <h4>المسمى الوظيفي: <?=$users_row["job_title"]?></h4>
                                            <h4>الفئة الوظيفية: <?=$job_types[$users_row["job_type_id"]]?></h4>
                                            <h4>الدرجة الوظيفية: <?=$job_ranks[$users_row["job_rank_id"]]?></h4>
                                            <h4>القسم العلمي: <?=$departments[$users_row["department_id"]]?></h4>
                                            <h4>حالة العضو: <?=$users_row["is_enabled"] == 1 ? "مفعل" : "غير مفعل"?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $n += 1;
                        }
                    elseif (!empty($_GET["search"])):
                        switch ($_GET["f"])
                        {
                            case "mn":
	                            $search_stmt = "SELECT * FROM p39_users WHERE name LIKE ?";
                                break;

                            case "jt":
	                            $search_stmt = "SELECT * FROM p39_users WHERE job_title LIKE ?";
                                break;
                        }
                        $search_result = Search($conn, NULL, @$search_stmt);
	                    @$search_result_count = $search_result->num_rows;
	                    if (@$search_result_count > 0)
	                    {
		                    $n = 1;
		                    while ($search_row = $search_result->fetch_assoc()) { ?>
                                <div class="member-box">
                                    <div class="row">
                                        <div class="col">
                                            <h4>رقم العضو:
                                                <span class="member-number">
                                                <?=$n?>
                                            </span>
                                            </h4>
                                            <h4>اسم العضو:
                                                <span class="member-name">
                                                <?= $search_row["name"] ?>
                                            </span>
                                            </h4>
                                        </div>
                                        <div class="col">
                                            <form method="post" action="update_member.php">
                                                <button class="btn-basic">تعديل بيانات العضو</button>
                                                <input type="hidden" name="user_id" value="<?=$search_row['user_id']?>">
                                            </form>
                                            <button class="btn-basic member-details-btn">تفاصيل العضو</button>
                                        </div>
                                    </div>

                                    <div class="member-details deactive">
                                        <div class="row">
                                            <div class="col">
                                                <img src="<?=$search_row['image']?>" alt="" class="member-image" />
                                            </div>
                                            <div class="col">
                                                <!--                                            <h4>نوع العضو: عضو مجلس</h4>-->
                                                <h4>الاسم : <?=$search_row["name"]?></h4>
                                                <!--                                            <h4>رقم العضو : 1</h4>-->
                                                <!--                                            <h4>رقم تشكيل المجلس المنضم له العضو:4</h4>-->
                                                <!--                                            <h4>رقم التليفون: 01102465132</h4>-->
                                                <h4>الايميل: <?=$search_row["email"]?></h4>
                                                <h4>المسمى الوظيفي: <?=$search_row["job_title"]?></h4>
                                                <h4>الفئة الوظيفية: <?=$job_types[$search_row["job_type_id"]]?></h4>
                                                <h4>الدرجة الوظيفية: <?=$job_ranks[$search_row["job_rank_id"]]?></h4>
                                                <h4>القسم العلمي: <?=$departments[$search_row["department_id"]]?></h4>
                                                <h4>حالة العضو: <?=$search_row["is_enabled"] == 1 ? "مفعل" : "غير مفعل"?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
		                    <?php $n += 1;
                            }
	                    } else { ?>
                            <main id="empty" class="empty-member">
                                <h4>لا يوجد أعضاء الآن</h4>
                            </main>
                        <?php }
                    else: ?>
                        <div class='current-meeting'>
                            <main id='empty' class='empty-meeting'>
                                <h4>عذرًا، لا يوجد أعضاء تطابق البحث</h4>
                            </main>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- اضافة عضو -->
                <div class="add-member">
                    <a href="add_member.php" class="btn-basic">إضافة عضو جديد</a>
                </div>
            </main>
        <?php
        endif;
        Footer();
        ?>

        <!-- Js Scripts and Plugins -->
        <script type="module" src="./js/main.js"></script>
        <script src="./js/member_details.js"></script>

        <!-- font Awesome -->
        <script src="https://kit.fontawesome.com/eb7dada2f7.js" crossorigin="anonymous"></script>
    </body>

</html>