CREATE DATABASE IF NOT EXISTS faculty_meeting_system;

CREATE TABLE IF NOT EXISTS application_data
(
    `app_id`               tinyint(4)                                              NOT NULL auto_increment,
    `app_name`             varchar(100) character set utf8 collate utf8_unicode_ci NOT NULL,
    `Uni_name`             varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
    `Faculty_name`         varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
    `Program_name`         varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
    `Faculty-Uni_logo`     varchar(50)                                            default NULL,
    `Program_logo`         varchar(50)                                            default NULL,
    `Faculty_Dean`         varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
    `Post_grad_vice_dean`  varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
    `st_affairs_vice_dean` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
    `Program_coordinator`  varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
    PRIMARY KEY (`app_id`)
);
INSERT INTO `application_data` (`app_id`, `app_name`, `Uni_name`, `Faculty_name`, `Program_name`, `Faculty-Uni_logo`,
                                `Program_logo`, `Faculty_Dean`, `Post_grad_vice_dean`, `st_affairs_vice_dean`,
                                `Program_coordinator`)
VALUES (1, 'النظام الإلكتروني لإدارة موضوعات مجلس الكلية', 'جامعة حلوان', 'كلية التجارة وإدارة الأعمال',
        'BIS برنامج نظم معلومات الأعمال', 'Facultylogo.jpg', 'program.png', 'أ.د. حسام الرفاعي', 'أ.د. هند عودة',
        'أ.د. أماني فاخر', 'أ.م.د. رشا فرغلى');

CREATE TABLE IF NOT EXISTS p39_job_type
(
    job_type_id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_type_name VARCHAR(255) character set utf8 collate utf8_unicode_520_ci
);
INSERT INTO p39_job_type (job_type_name)
VALUES ("عميد"),
       ("وكيل الكلية"),
       ("رئيس قسم"),
       ("عضو هيئة تدريس"),
       ("إداري"),
       ("عضو حارجي"),
       ("أخرى");

CREATE TABLE IF NOT EXISTS p39_job_rank
(
    job_rank_id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_rank_name VARCHAR(255) character set utf8 collate utf8_unicode_520_ci
);
INSERT INTO p39_job_rank (job_rank_name)
VALUES ("أستاذ"),
       ("أستاذ مساعد"),
       ("مدرس"),
       ("خبير"),
       ("إداري");

CREATE TABLE IF NOT EXISTS p39_department
(
    department_id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(255) character set utf8 collate utf8_unicode_520_ci
);
INSERT INTO p39_department (department_name)
VALUES ("قسم المحاسبة"),
       ("قسم إدارة الأعمال"),
       ("قسم الاقتصاد والتجارة الخارجية"),
       ("قسم الإحصاء"),
       ("قسم العلوم السياسية"),
       ("قسم نظم المعلومات"),
       ("عضو خارجي"),
       ("إداري");

CREATE TABLE IF NOT EXISTS p39_users
(
    user_id       SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255) character set utf8 collate utf8_unicode_520_ci NOT NULL,
    job_title     VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    job_type_id   TINYINT UNSIGNED,
    job_rank_id   TINYINT UNSIGNED,
    department_id TINYINT UNSIGNED,
    gender        ENUM ("M", "F") character set utf8 collate utf8_unicode_520_ci,
    image         VARCHAR(255) character set utf8 collate utf8_unicode_520_ci DEFAULT "images/members/user.svg",
    email         VARCHAR(255) character set utf8 collate utf8_unicode_520_ci UNIQUE,
    password      VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    is_admin      TINYINT UNSIGNED,
    added_by      SMALLINT UNSIGNED,
    is_enabled    TINYINT UNSIGNED                                            DEFAULT 1,
    FOREIGN KEY (added_by) References p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (job_type_id) References p39_job_type (job_type_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (job_rank_id) References p39_job_rank (job_rank_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (department_id) References p39_department (department_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_formation
(
    formation_id     SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    formation_number MEDIUMINT UNSIGNED,
    start_year       YEAR UNIQUE,
    is_current       TINYINT UNSIGNED,
    added_on         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by         SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_dates
(
    date_id      SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    month        TINYINT(2) UNSIGNED,
    year         YEAR,
    formation_id SMALLINT UNSIGNED,
    FOREIGN KEY (formation_id) REFERENCES p39_formation (formation_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_formation_user
(
    formation_id SMALLINT UNSIGNED,
    user_id      SMALLINT UNSIGNED,
    job_title    varchar(255),
    PRIMARY KEY (formation_id, user_id),
    FOREIGN KEY (formation_id) REFERENCES p39_formation (formation_id) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (user_id) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS p39_user_transaction
(
    transaction_id   SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_type ENUM ("Edit", "Delete"),
    user_id          SMALLINT UNSIGNED,
    old_row          VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    new_row          VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    made_on          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    made_by          SMALLINT UNSIGNED,
    FOREIGN KEY (made_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_meeting
(
    meeting_id     SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    meeting_number MEDIUMINT UNSIGNED,
    meeting_month  SMALLINT UNSIGNED,
    meeting_year   YEAR,
    meeting_date   DATE                                      DEFAULT NULL,
    is_current     TINYINT UNSIGNED                          DEFAULT 1,
    status         ENUM ("pending", "confirmed", "finished") DEFAULT "pending",
    is_shown       TINYINT UNSIGNED                          DEFAULT 0,
    formation_id   SMALLINT UNSIGNED,
    added_on       TIMESTAMP                                 DEFAULT CURRENT_TIMESTAMP,
    added_by       SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (formation_id) REFERENCES p39_formation (formation_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_meeting_transaction
(
    transaction_id   SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_type ENUM ("Edit", "Delete"),
    meeting_id       SMALLINT UNSIGNED,
    old_row          VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    new_row          VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    made_on          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    made_by          SMALLINT UNSIGNED,
    FOREIGN KEY (made_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_meeting_attachment
(
    attachment_id    SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attachment_name  VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    attachment_title VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    meeting_id       SMALLINT UNSIGNED,
    is_final         TINYINT UNSIGNED,
    added_on         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by         SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (meeting_id) REFERENCES p39_meeting (meeting_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_subject_type
(
    subject_type_id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_type_name VARCHAR(255) character set utf8 collate utf8_unicode_520_ci
);
INSERT INTO p39_subject_type (subject_type_name)
VALUES ("شئون التعليم والطلاب"),
       ("شئون الدراسات العليا والبحوث"),
       ("شئون خدمة المجتمع وتنمية البيئة"),
       ("قسم المحاسبة"),
       ("قسم إدارة الأعمال"),
       ("قسم الاقتصاد والتجارة الخارجية"),
       ("قسم الإحصاء"),
       ("قسم العلوم السياسية"),
       ("قسم نظم المعلومات"),
       ("لجنة البرامج"),
       ("موضوعات عامة"),
       ("لجنة البرامج الجديدة");

CREATE TABLE IF NOT EXISTS p39_subject
(
    order_id        TINYINT UNSIGNED DEFAULT NULL,
    subject_id      MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_number  MEDIUMINT UNSIGNED,
    subject_name    VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    subject_details TEXT character set utf8 collate utf8_unicode_520_ci,
    subject_type_id TINYINT UNSIGNED,
    meeting_id      SMALLINT UNSIGNED,
    comments        TEXT character set utf8 collate utf8_unicode_520_ci,
    added_on        TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    added_by        SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (subject_type_id) REFERENCES p39_subject_type (subject_type_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (meeting_id) REFERENCES p39_meeting (meeting_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_subject_transaction
(
    transaction_id   SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_type ENUM ("Edit", "Delete"),
    subject_id       MEDIUMINT UNSIGNED,
    old_row          VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    new_row          VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    made_on          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    made_by          SMALLINT UNSIGNED,
    FOREIGN KEY (made_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_subject_attachment
(
    attachment_id    SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attachment_name  VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    attachment_title VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    subject_id       MEDIUMINT UNSIGNED,
    added_on         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by         SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (subject_id) REFERENCES p39_subject (subject_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_subject_picture
(
    picture_id    SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    picture_name  VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    picture_title VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    subject_id    MEDIUMINT UNSIGNED,
    added_on      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by      SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (subject_id) REFERENCES p39_subject (subject_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_decision_type
(
    decision_type_id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    decision_type_name VARCHAR(255) character set utf8 collate utf8_unicode_520_ci
);
INSERT INTO p39_decision_type(decision_type_name)
VALUES ("موافقة"),
       ("رفض"),
       ("تأجيل");

CREATE TABLE IF NOT EXISTS p39_decision
(
    decision_id      SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    decision_details TEXT character set utf8 collate utf8_unicode_520_ci,
    decision_type_id TINYINT UNSIGNED,
    subject_id       MEDIUMINT UNSIGNED,
    needs_action     TINYINT UNSIGNED,
    action_to        VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    is_action_done   TINYINT UNSIGNED,
    comments         TEXT character set utf8 collate utf8_unicode_520_ci,
    added_on         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by         SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (decision_type_id) REFERENCES p39_decision_type (decision_type_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (subject_id) REFERENCES p39_subject (subject_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_decision_attachment
(
    attachment_id    SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attachment_name  VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    attachment_title VARCHAR(255) character set utf8 collate utf8_unicode_520_ci,
    decision_id      SMALLINT UNSIGNED,
    added_on         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by         SMALLINT UNSIGNED,
    FOREIGN KEY (added_by) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (decision_id) REFERENCES p39_decision (decision_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_attendance
(
    user_id    SMALLINT UNSIGNED,
    meeting_id SMALLINT UNSIGNED,
    PRIMARY KEY (user_id, meeting_id),
    FOREIGN KEY (user_id) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (meeting_id) REFERENCES p39_meeting (meeting_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS p39_vote_type
(
    vote_type_id   TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vote_type_name VARCHAR(255) character set utf8 collate utf8_unicode_520_ci
);

CREATE TABLE IF NOT EXISTS p39_vote
(
    user_id      SMALLINT UNSIGNED,
    subject_id   MEDIUMINT UNSIGNED,
    PRIMARY KEY (user_id, subject_id),
    vote_type_id TINYINT UNSIGNED,
    added_on     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES p39_users (user_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (vote_type_id) REFERENCES p39_vote_type (vote_type_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (subject_id) REFERENCES p39_subject (subject_id) ON UPDATE CASCADE ON DELETE RESTRICT
);
INSERT INTO p39_vote_type (vote_type_name)
VALUES ("موافقة"),
       ("رفض"),
       ("امتناع"),
       ("تحفظ"),
       ("انسحاب");


###### Dummy Data #######

### Users (Password: 123)
INSERT INTO `p39_users` (`user_id`, `name`, `job_title`, `job_type_id`, `job_rank_id`, `department_id`, `gender`,
                         `email`, `password`, `is_admin`, `added_by`, `is_enabled`)
VALUES (NULL, 'محمود بدر', 'Admin', '1', '1', '1', 'M', 'm@hotmail.com',
        '$2y$10$QyL5sGwbWIk./cUXORlNV.9C4ZZsHPV6llGcX5WggZ8tyGcNo0tXS', '1', NULL, '1'),

       (NULL, 'محمد ايمن', 'Admin', '1', '1', '1', 'M', 'aymon@gmail.com',
        '$2y$10$QyL5sGwbWIk./cUXORlNV.9C4ZZsHPV6llGcX5WggZ8tyGcNo0tXS', '1', NULL, '1'),

       (NULL, 'دكتور 1', 'Doctor', '2', '2', '2', 'F', 'd@hotmail.com',
        '$2y$10$jtRDgq2Biaz14RYq8hKcaOdegq6P8gErvotb1c11.ANXWcs4j55nG', '0', '1', '1');

### Formation
INSERT INTO `p39_formation` (`formation_id`, `start_year`, `added_on`, `added_by`)
VALUES (NULL, '2023', '2023-04-30 21:39:09', '1'),
       (NULL, '2022', '2023-05-01 00:16:19', '1');

### Meetings
INSERT INTO `p39_meeting` (`meeting_id`, `meeting_number`, `meeting_month`, `meeting_year`, `meeting_date`,
                           `is_current`, `status`, `formation_id`, `added_on`, `added_by`)
VALUES (NULL, '1', '9', '2023', NULL, '1', 'confirmed', '1', '2023-04-30 21:45:17', '1'),
       (NULL, '2', '10', '2023', NULL, '0', 'finished', '1', '2023-04-30 21:46:07', '1'),
       (NULL, '3', '11', '2023', NULL, '0', 'finished', '1', '2023-04-30 22:46:07', '1'),
       (NULL, '4', '9', '2022', NULL, '0', 'finished', '2', '2023-04-30 22:46:07', '1'),
       (NULL, '5', '10', '2022', NULL, '0', 'finished', '2', '2023-04-30 22:46:07', '1');

### Formation_User
INSERT INTO `p39_formation_user` (`formation_id`, `user_id`)
VALUES ('1', '1'),
       ('1', '2');
