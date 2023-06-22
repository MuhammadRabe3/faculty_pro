<?php
require_once "db.php";

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

if (empty(@$_SESSION["app_name"]))
{
    // Get Website header information
    $header_stmt = $conn->prepare("SELECT `app_name`, 
                                            `Uni_name`, 
                                            `Faculty_name`, 
                                            `Program_name`, 
                                            `Faculty-Uni_logo`, 
                                            `Program_logo` 
                                    FROM application_data");
    $header_stmt->execute();
    $header_result = $header_stmt->get_result();
    $header_row = $header_result->fetch_assoc();
    $_SESSION["app_name"] = $header_row["app_name"];
    $_SESSION["uni_name"] = $header_row["Uni_name"];
    $_SESSION["faculty_name"] = $header_row["Faculty_name"];
    $_SESSION["program_name"] = $header_row["Program_name"];
    $_SESSION["faculty_logo"] = $header_row["Faculty-Uni_logo"];
    $_SESSION["program_logo"] = $header_row["Program_logo"];
}

if (empty(@$_SESSION["image"]))
{
    $user_stmt = $conn->prepare("SELECT name, image FROM p39_users WHERE user_id = ?");
    $user_stmt->bind_param("i", $_SESSION["user_id"]);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();

    @$_SESSION["image"] =  $user_row['image'];
    @$_SESSION["name"]  =  $user_row['name'];
}


function Headers()
{
	?>
    <header>
        <div class="user-logged">
			<?php if (!empty($_SESSION["loggedin"])) { ?>
                <img src="<?= @$_SESSION['image'] ?>" alt="">
                <h5 class="user-name"> <?= @$_SESSION['name'] ?></h5>
                <button class="button btn-basic" onclick="location.href='logout.php'">خروج</button>
            <?php } ?>
        </div>

        <div class="header-container">
            <a href="./index.php"><img class="logo univ-logo" src="images/<?= $_SESSION["faculty_logo"] ?>" alt=""/></a>
            <div class="header-title">
                <h3 class="univ-name"><?= $_SESSION["uni_name"] ?> - <?= $_SESSION["faculty_name"] ?></h3>

                <h4 class="prog-name" dir=ltr><?= $_SESSION["program_name"] ?></h4>
                <h1 class="project-title">
					<?= $_SESSION["app_name"] ?>
                </h1>
            </div>
            <a href="./index.php"><img class="logo prog-logo" src="images/<?= $_SESSION["program_logo"] ?>" alt=""/></a>
        </div>


    </header>
	<?php
}

function Nav()
{
    ?>
    <section class="nav-bar">
        <nav>
            <ul>
                <button class="icon" href="#"><i class="fa-solid fa-bars fa-2xl"></i></button>
                <div class="links">
                    <li><a href="index.php">الصفحة الرئيسية</a></li>
                    <li><a href="formation.php">التشكيلات</a></li>
                    <li><a href="meetings.php">المجالس</a></li>
<!--                    <li><a href="#">الموضوعات</a></li>-->
					<?php if (@$_SESSION["admin"]): ?>
                        <li><a href="all_subjects.php?f=all">الموضوعات</a></li>
                        <li><a href="members.php">الأعضاء</a></li>
                        <li><a href="executive_decisions.php">القرارات التنفيذية</a></li>
					<?php endif; ?>
                </div>
            </ul>
            <?php SearchBar() ?>
        </nav>
    </section>
<?php

}

function SearchBar()
{
    ?>
    <form class="search" action="<?= basename($_SERVER['PHP_SELF']) ?>" method="get">
		<?php switch (basename($_SERVER["PHP_SELF"])) {
			case "formation.php": ?>
                <div class="select-basic">
                    <select name="f">
                        <?php switch ($_GET["f"]) {
                            case "fn": ?>
                                <option value="">عن طريق</option>
                                <option value="fn" selected>رقم التشكيل</option>
                                <option value="y">سنة التشكيل</option>
                                <?php break;
                            case "y": ?>
                                <option value="">عن طريق</option>
                                <option value="fn">رقم التشكيل</option>
                                <option value="y" selected>سنة التشكيل</option>
	                            <?php break;
                            default: ?>
                                <option value="">عن طريق</option>
                                <option value="fn">رقم التشكيل</option>
                                <option value="y">سنة التشكيل</option>
                        <?php } ?>
                    </select>
                </div>
                <input type="text" placeholder="ابحث عن تشكيل" name="search"/>
                <button type="submit" class="btn-basic">
                    <i class="fa fa-search"></i>
                </button>
				<?php break;
			case "meetings.php": ?>
                <div class="select-basic">
                    <select name="f">
                        <?php switch ($_GET["f"]) {
                            case "mn": ?>
                                <option value="">عن طريق</option>
                                <option value="mn" selected>رقم المجلس</option>
                                <option value="fn">رقم التشكيل</option>
                                <option value="my">سنة المجلس</option>
                                <option value="sn">عنوان الموضوع</option>
                                <option value="sd">تفاصيل الموضوع</option>
                                <?php break;
                            case "fn": ?>
	                            <option value="">عن طريق</option>
                                <option value="mn">رقم المجلس</option>
                                <option value="fn" selected>رقم التشكيل</option>
                                <option value="my">سنة المجلس</option>
                                <option value="sn">عنوان الموضوع</option>
                                <option value="sd">تفاصيل الموضوع</option>
                                <?php break;
                            case "my": ?>
                                <option value="">عن طريق</option>
                                <option value="mn">رقم المجلس</option>
                                <option value="fn">رقم التشكيل</option>
                                <option value="my" selected>سنة المجلس</option>
                                <option value="sn">عنوان الموضوع</option>
                                <option value="sd">تفاصيل الموضوع</option>
                                <?php break;
	                        case "sn": ?>
                                <option value="">عن طريق</option>
                                <option value="mn">رقم المجلس</option>
                                <option value="fn">رقم التشكيل</option>
                                <option value="my">سنة المجلس</option>
                                <option value="sn" selected>عنوان الموضوع</option>
                                <option value="sd">تفاصيل الموضوع</option>
		                        <?php break;
	                        case "sd": ?>
                                <option value="">عن طريق</option>
                                <option value="mn">رقم المجلس</option>
                                <option value="fn">رقم التشكيل</option>
                                <option value="my">سنة المجلس</option>
                                <option value="sn">عنوان الموضوع</option>
                                <option value="sd" selected>تفاصيل الموضوع</option>
		                        <?php break;
                            default: ?>
                                <option value="">عن طريق</option>
                                <option value="mn">رقم المجلس</option>
                                <option value="fn">رقم التشكيل</option>
                                <option value="my">سنة المجلس</option>
                                <option value="sn">عنوان الموضوع</option>
                                <option value="sd">تفاصيل الموضوع</option>
                                <?php break;
                        } ?>
                    </select>
                </div>
                <input type="text" placeholder="ابحث عن مجلس" name="search"/>
                <button type="submit" class="btn-basic" name="search_btn">
                    <i class="fa fa-search"></i>
                </button>
				<?php break;
            case "members.php": ?>
                <div class="select-basic">
                    <select name="f">
		                <?php switch ($_GET["f"]) {
			                case "mn": ?>
                                <option value="">عن طريق</option>
                                <option value="mn" selected>اسم العضو</option>
                                <option value="jt">المسمى الوظيفي</option>
				                <?php break;
			                case "jt": ?>
                                <option value="">عن طريق</option>
                                <option value="mn">اسم العضو</option>
                                <option value="jt" selected>المسمى الوظيفي</option>
				                <?php break;
			                default: ?>
                                <option value="">عن طريق</option>
                                <option value="mn">اسم العضو</option>
                                <option value="jt">المسمى الوظيفي</option>
				                <?php break;
		                } ?>
                    </select>
                </div>
                <input type="text" placeholder="ابحث عن عضو" name="search"/>
                <button type="submit" class="btn-basic">
                    <i class="fa fa-search"></i>
                </button>
	            <?php break;
        } ?>
    </form>
    <?php
}

function Head($title)
{
    ?>

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?=$title?></title>
  <!-- Css  -->
  <!-- Css Components and Initialize Styles  -->
  <link rel="stylesheet" href="css/initialize.css" />
  <!-- Your Css Here  -->
  <link rel="stylesheet" href="css/style.css" />
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Work+Sans:wght@200;300;400;500;600;700;800&display=swap"
    rel="stylesheet" />
</head>
<?php
}

function Footer()
{
    $y = date("Y");
    ?>
    <footer>
        <p>جميع الحقوق محفوظة &copy; لدى  <a href="./credits.php" target="_blank">فريق رقم 39</a> Bis Seniors <?=$y?></p>
    </footer>
    <?php
}

function clean_data($str)
{
    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str, ENT_QUOTES, "UTF-8");
    return $str;
}

// Function for validating email
function is_valid_email($email)
{
    if (empty($email))
    {
        return false;
    }
    else
    {
        $email = clean_data($email);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
    }
    return $email;
}

function is_admin():bool
{
    if(@$_SESSION["admin"])
    {
        return true;
    } else { ?>
        <div class="error">
            <img src="./images/icons/error.svg" alt="">
            <p>
            ليس لديك صلاحية لتحميل هذه الصفحة، سيتم تحويلك تلقائيًا خلال 5 ثوان إلى الصفحة الرئيسية.
            </p>
        </div>
        <br>
        <?php
        header("refresh:5; url=index.php");
        footer();
        die();
    }
}

function is_logged_in():bool
{
    if(@$_SESSION["loggedin"] === true)
    {
        return true;
    }
    else
    {
        ?>
        <div class="error">
            <img src="./images/icons/error.svg" alt="">
            <p>
                يجب تسجيل الدخول لتحميل هذه الصفحة، سيتم تحويلك تلقائيًا خلال 5 ثوان إلى صفحة تسجيل الدخول.
            </p>
        </div>
        <br>
        <?php header("refresh:5; url=login.php");
        footer();
        die();
    }
}

function Upload($source, $destination, $allowed_formats)
{
    $result = array();
//    $destination = self::Path($destination);

    if ((is_dir($destination) === true) && (array_key_exists($source, $_FILES) === true))
    {
        if (count($_FILES[$source], COUNT_RECURSIVE) == 5)
        {
            foreach ($_FILES[$source] as $key => $value)
            {
                $_FILES[$source][$key] = array($value);
            }
        }

        foreach (array_map('basename', $_FILES[$source]['name']) as $key => $value)
        {
            $new_file_path = $destination . $value;
            $file_type = pathinfo($new_file_path, PATHINFO_EXTENSION);
            if (in_array($file_type, $allowed_formats))
            {
                $result[$value] = false;
                if ($_FILES[$source]['error'][$key] == UPLOAD_ERR_OK)
                {
//                $file = ph()->Text->Slug($value, '_', '.');
                    $file = NULL;
                    $file = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file)));

                    if (file_exists($destination . $file) === true)
                    {
                        $file = substr_replace($file, '_' . md5_file($_FILES[$source]['tmp_name'][$key]) . ".$file_type", strrpos($value, '.'), 0);
//                        $file = substr_replace($file, '_' . hash_file('sha256', $_FILES[$source]['tmp_name'][$key]) . ".$file_type", strrpos($value,'.'),0);
                    }

                    if (move_uploaded_file($_FILES[$source]['tmp_name'][$key], $destination . $file) === true)
                    {
                        $result[$value] = $destination . $file;
                    }
                }
            }
        }
    }
    return $result;
}

function Search($conn, $table = NULL, $query = NULL)
{
    if (!empty(clean_data($_GET["f"])))
    {
	    switch (clean_data($_GET["f"]))
	    {
		    case "y":
			    $column = "start_year";
			    break;
		    case "fn":
			    $column = "formation_number";
			    break;
            case "mn":
                $column = "meeting_number";
                break;
	    }
	    $search = "%" . clean_data($_GET["search"]) . "%";
        if ($query === NULL)
        {
	        $search_stmt = $conn->prepare("SELECT * FROM $table WHERE " . $column . " LIKE ?");
        }
        else
        {
            $search_stmt = $conn->prepare($query);
        }
	    $search_stmt->bind_param("s", $search);
	    $search_stmt->execute();
	    $search_result = $search_stmt->get_result();
	    return $search_result;
    }
}

function Delete($conn, $table, $where)
{
    $delete_stmt = $conn->prepare("DELETE FROM {$table} WHERE {$where}");
    return $delete_stmt;
}

function Validate(array $inputs)
{
    $error = false;
    foreach ($inputs as $input)
    {
        if (empty($_POST[$input]))
        {
            $error = true;
            return $error;
        }
    }
    return $error;
}