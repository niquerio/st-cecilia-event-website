<?php
/*
 * KWDS DATABASE CLASS
 */

        require_once(dirname(__FILE__).'/ChromePhp.php');
class db {
    private $connection;

    // Select and connect to the database
    function db() {
        require_once(dirname(__FILE__).'/config.php');
        $this->connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)
//        AND $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME)
            OR die('Unable to connect to database!');
	mysqli_set_charset($this->connection,'utf8');
    }

    // Checks teacher attendance against his/her scheduled classes
    function check_attendance($kwds) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        return $this->query("SELECT DISTINCT class.id as id FROM class
            INNER JOIN coteacher ON class.id = coteacher.class_id
            INNER JOIN attendance ON attendance.user_id = coteacher.user_id
            WHERE class.kwds_id = $kwds
                AND (arrival > day or departure < ADDTIME(day, SEC_TO_TIME(hours*60)))");
    }

    // Checks classes for same teacher conflicts
    function check_conflicts($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("SELECT DISTINCT C1.id as id, C1.name,
    CONCAT(COALESCE(U1.sca_first,''),' ', COALESCE(U1.sca_last,''),' [',COALESCE(U1.first,''),' ',COALESCE(U1.last,''),']') as user_id, C1.day,
    ADDTIME(C1.day, SEC_TO_TIME(C1.hours*60)) as end
            FROM class C1
            INNER JOIN coteacher T1 ON T1.class_id = C1.id
            LEFT OUTER JOIN user U1 ON U1.id = T1.user_id
            INNER JOIN class C2 ON C2.id != C1.id
            INNER JOIN coteacher T2 ON C2.id = T2.class_id AND T1.user_id = T2.user_id
            WHERE C1.kwds_ID = $id AND C2.kwds_ID = $id
                AND ((C1.day >= C2.day AND C1.day < ADDTIME(C2.day, SEC_TO_TIME(C2.hours*60)))
                OR ((ADDTIME(C1.day, SEC_TO_TIME(C1.hours*60)) > C2.day
                    AND ADDTIME(C1.day, SEC_TO_TIME(C1.hours*60)) <= ADDTIME(C2.day, SEC_TO_TIME(C2.hours*60)))))
            ORDER BY T1.user_id, C1.day");
    }

    // Deletes an attendance record from the attendance table
    function delete_attendance($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("DELETE FROM attendance WHERE user_id = $id");
    }

    // Deletes a fee from the fees table
    function delete_fee($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("DELETE FROM fees WHERE id='$id'");
    }

    // Delete an activity from the master schedule
    function delete_master_schedule($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("DELETE FROM master WHERE id='$id'");
    }

    // Delete links for the password reset page
    function delete_reset($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("DELETE FROM password WHERE user_id='$id'");
    }

    // Remove someone from a role
    function delete_role($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("DELETE FROM role WHERE role_id='$id'");
    }

    // Remove a room from a KWDS
    function delete_room($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("DELETE FROM room WHERE id='$id'");
    }

    // Checks to see if email exists in the system (Returns TRUE/FALSE)
    function email_exist($email) {
       $email = mysqli_real_escape_string($this->connection, $email); 
        $result = $this->query("SELECT email FROM user WHERE email='$email'");

        if (is_array($result) && count($result) > 0) {
            return true;
        }
        return false;
    }

    //Returns a list of accepted classes for a user
    function get_accepted_classes($id, $kwds) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        return $this->query(
            "SELECT name, upload, id
            FROM `class`
            WHERE user_id = '$id' AND kwds_id = '$kwds' AND accepted = 1
            ORDER BY name"
            );
    }

    // Returns a single record if user is attending a KWDS
    function get_attendance($id, $kwds) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        $return = $this->query("SELECT user_id, arrival, departure, kwds_id FROM attendance WHERE user_id=$id AND kwds_id=$kwds");
        if ($return) return $return[0];
        else return array();
    }
    
    // Returns charset of database
    function get_charset(){ 
        return mysqli_character_set_name($this->connection); 
    }

    // Returns a single record information for a particular class
    function get_class($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $return = $this->query(
            //"SELECT accepted, aerobic_id AS AerobicID, aerobic.name AS AerobicName, class.description AS ClassDescription,
            //    class.kwds_id AS KWID, class.name AS ClassName, class.user_id AS teacher, day, difficulty_id AS DifficultyID,
            //    difficulty.name AS DifficultyName, era_id AS EraID, era.name AS EraName, fee, hours, `class`.`limit` AS 'limit',
            //    `class`.other AS other, prefix.name AS PrefixName, room.id AS RoomID, room.name AS RoomName, sca_first AS SCAFirst,
            //    sca_last AS SCALast, style_id AS StyleID, style.name AS StyleName, title.name AS 'Title', type_id AS TypeID,
            //    type.name AS TypeName, class.url as 'url',user.first AS MundaneFirst, user.id AS UserID, user.last AS MundaneLast
            //FROM `aerobic`, `class`, `difficulty`, `era`, `group`, `kingdom`, `prefix`, `room`, `style`, `title`, `type`, `user`
            //WHERE aerobic.id=aerobic_id AND difficulty_id=difficulty.id AND era_id=era.id AND `class`.id='$id'
            //    AND user.group_id=`group`.id AND kingdom_id=kingdom.id AND prefix_id=prefix.id
            //    AND (room_id=room.id or room_id='0') AND style_id=style.id AND title_id=title.id AND type_id=type.id
            //    AND class.user_id=user.id
            //GROUP BY room_id
            //ORDER BY class.name"
            "SELECT accepted, class.description AS ClassDescription,
            class.kwds_id AS KWID, class.name AS ClassName, class.user_id AS teacher, day, difficulty_id AS DifficultyID,
                difficulty.name AS DifficultyName, fee, hours, `class`.`limit` AS 'limit',
                `class`.other AS other, prefix.name AS PrefixName, room.id AS RoomID, room.name AS RoomName, sca_first AS SCAFirst,
                sca_last AS SCALast, style_id AS StyleID, style.name AS StyleName, title.name AS 'Title', type_id AS TypeID,
                type.name AS TypeName, class.url as 'url',user.first AS MundaneFirst, user.id AS UserID, user.last AS MundaneLast
                FROM `class`, `difficulty`, `group`, `kingdom`, `prefix`, `room`, `style`, `title`, `type`, `user`
                WHERE difficulty_id=difficulty.id AND `class`.id='$id'
                AND user.group_id=`group`.id AND kingdom_id=kingdom.id AND prefix_id=prefix.id
                AND (room_id=room.id or room_id='0') AND style_id=style.id AND title_id=title.id AND type_id=type.id
                AND class.user_id=user.id
                GROUP BY room_id
            ORDER BY class.name"
        );
        if ($return) return $return[0];
        else return array();
    }

    //Returns the class submission cut-off date of a particular KWDS
    function get_class_cutoff($kwds) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        $return = $this->query("SELECT date_format(class_date, '%M %e, %Y') as cutoff FROM kwds WHERE id='$kwds'");
        if ($return) {return $return[0]['cutoff'];}
        return 0;
    }

    // Returns a list of class information for a particular KWDS
    function get_class_info($kwds) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        return $this->query(
            //"SELECT class.name AS ClassName, sca_first AS SCAFirst, sca_last AS SCALast, title.name AS TitleName,
            //    user.id AS UserID, user.first AS MundaneFirst, user.last AS MundaneLast, room.name AS RoomName,
            //    room.id AS RoomID, class.description AS ClassDescription, day, hours, style_id as StyleID,type_id as TypeID,
            //    difficulty_id AS DifficultyID, aerobic_id AS AerobicID, era_id AS EraID, prefix.name AS PrefixName,
            //    class.upload AS ClassUpload, class.id AS ClassID, aerobic.name as aerobicName, style.name as styleName,
            //    difficulty.name as difficultyName
            //FROM `aerobic`, `class`, `difficulty`, `era`, `group`, `kingdom`, `prefix`, `room`, `style`, `title`, `type`, `user`
            //WHERE aerobic.id=aerobic_id AND class.kwds_id='$kwds' AND difficulty_id=difficulty.id AND era_id=era.id
            //    AND user.group_id=group.id AND kingdom_id=kingdom.id AND prefix_id=prefix.id AND room_id=room.id
            //    AND title_id=title.id AND type_id=type.id AND style_id = style.id AND class.user_id=user.id AND accepted=1
            //ORDER BY class.name"
            "SELECT class.name AS ClassName, sca_first AS SCAFirst, sca_last AS SCALast, title.name AS TitleName,
                user.id AS UserID, user.first AS MundaneFirst, user.last AS MundaneLast, room.name AS RoomName,
                room.id AS RoomID, class.description AS ClassDescription, day, hours, style_id as StyleID,type_id as TypeID,
                difficulty_id AS DifficultyID, prefix.name AS PrefixName,
                class.upload AS ClassUpload, class.id AS ClassID, style.name as styleName,
                difficulty.name as difficultyName
            FROM  `class`, `difficulty`, `group`, `kingdom`, `prefix`, `room`, `style`, `title`, `type`, `user`
            WHERE class.kwds_id='$kwds' AND difficulty_id=difficulty.id 
                AND user.group_id=group.id AND kingdom_id=kingdom.id AND prefix_id=prefix.id AND room_id=room.id
                AND title_id=title.id AND type_id=type.id AND style_id = style.id AND class.user_id=user.id AND accepted=1
            ORDER BY class.name"
        );
    }

    // Returns the name of a class
    function get_class_name($class) {
       $class = mysqli_real_escape_string($this->connection, $class); 
        $result= $this->query("SELECT name FROM `class` WHERE id='$class'");
        return display_HTML($result[0][name]);
    }

    // Returns a list of classes from a particular room for a certain day
    function get_class_rooms($id, $day, $where_inputs) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $day = mysqli_real_escape_string($this->connection, $day); 
       $where = "type_id>0";
       foreach($where_inputs as $index=>$attributes){
        foreach($attributes as $attribute=>$value){
           $attribute = mysqli_real_escape_string($this->connection, $attribute); 
           $value = mysqli_real_escape_string($this->connection, $value); 
           $where.=" AND $attribute != $value";
        }
       }

       
        return $this->query(
            "SELECT class.name AS ClassName, class.id as ClassID, description, `class`.other AS other,
            GROUP_CONCAT(user.sca_first,' ',user.sca_last SEPARATOR ', ') AS user,
            type_id AS TypeID, difficulty_id as DifficultyID, style_id as StyleID, day, hours,
                ((((DATE_FORMAT(day,'%k') - 9) * 60) + DATE_FORMAT(day,'%i')) * 1.15) as time
                FROM `class` 
                LEFT OUTER JOIN `coteacher` ON coteacher.class_id = class.id
                LEFT OUTER JOIN `user` ON user.id = coteacher.user_id
                WHERE 
                room_id='$id' 
                AND (DATE_FORMAT(day,'%j') + 1)='$day' 
                AND ($where)
            GROUP BY class_id"
        );
    }

    // Returns a list of teachers for a class
    function get_class_teachers($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query(
            "SELECT sca_first, sca_last, first, last, title.name AS title, prefix.name AS prefix, user.id as UserID
                FROM `user`, `coteacher`, `prefix`, `title`
                WHERE user_id = user.id AND class_id='$id' AND prefix.id=prefix_id AND title.id=title_id"
        );
    }

    // Returns a list of requested user emails
    function get_emails($euro,$middle,$music,$other, $kwds) {
       $euro = mysqli_real_escape_string($this->connection, $euro); 
       $middle = mysqli_real_escape_string($this->connection, $middle); 
       $music = mysqli_real_escape_string($this->connection, $music); 
       $other = mysqli_real_escape_string($this->connection, $other); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        $where= "WHERE ";
        if ($euro==1) {
            $where = $where."(class.type_id=2 AND class.kwds_id='$kwds') OR ";
        }
        if ($middle==1) {
            $where = $where."(class.type_id=3 AND class.kwds_id='$kwds') OR ";
        }
        if ($music==1) {
            $where = $where."(class.type_id=4 AND class.kwds_id=$kwds) OR ";
        }
        if ($other==1) {
            $where = $where."(class.type_id=5 AND class.kwds_id=$kwds) OR ";
        }
        $where = $where."1!=1";
        $return = $this->query("SELECT DISTINCT user.email FROM user
            INNER JOIN coteacher ON user.id=coteacher.user_id
            INNER JOIN class ON coteacher.class_id = class.id ".$where);
        return $return;
    }

    // Returns a single record of a fee
    function get_fee($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $return = $this->query("SELECT description, fee_type_id, name, prereg, price FROM fees WHERE id='$id'");
        if ($return) return $return[0];
        else return array();
    }

    // Returns a list of fees from a particular KWDS
    function get_fees($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query(
            "SELECT `fees`.id AS FeeID, `fees`.name as FeeName, price, description,
                `fee_type`.name AS FeeTypeName, prereg
            FROM `fees`, `fee_type`
            WHERE `kwds_id` = '$id' and `fee_type_id` = `fee_type`.`id`
            ORDER BY `prereg` DESC, `fee_type_id` ASC"
        );
    }

    // Returns a list of future KWDS events
    function get_future_kwds() {
        return $this->query("SELECT city, country, end_date, id, state FROM ".DB_NAME.".kwds WHERE NOW() <= end_date");
    }

    // Returns a single record for a KWDS event
    function get_kwds($kwds) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
	//ChromePhp::log("In get_kwds: " . $kwds);
        $result = $this->query(
            "SELECT *, kwds.id as KWID, kingdom.name as kingdom, kwds.name as kwdsName, kwds.url as KWurl
            FROM kwds, kingdom
            WHERE kwds.id='$kwds' AND kingdom_id=kingdom.id"
        );
        if (count($result) == 0) {
            $result = $this->query(
                "SELECT *, kwds.id as KWID, kingdom.name as kingdom, kwds.name as kwdsName
                FROM kwds, kingdom
                WHERE now() < end_date AND kingdom_id=kingdom.id LIMIT 1"
            );
        }
        return $result[0];
    }

    // Returns a single value as indicated by the variable sent in
    function get_kwds_field($field, $id) {
       $field = mysqli_real_escape_string($this->connection, $field); 
       $id = mysqli_real_escape_string($this->connection, $id); 
        $result = $this->query("SELECT $field FROM `kwds` WHERE id='$id'");

        if (count($result==1)) return display_HTML($result[0][$field]);
        else return array();
    }

    // Returns a list of KWDS's for which classes can still be submitted for
    function get_kwds_submissions() {
        return $this->query(
            "SELECT id, CONCAT('KWDS ',id) as name
            FROM ".DB_NAME.".kwds
            WHERE NOW() <= ADDTIME(class_date, '23:59:00')"
        );
    }

    // Returns a list of the names and IDs from a table called $type
    function get_list($type) {
       $type = mysqli_real_escape_string($this->connection, $type); 
        return $this->query("SELECT name, id FROM $type ORDER BY name");
    }

    // Returns a list of items for the master schedule of a KWDS
    function get_master_schedule($id) {
        return $this->query("SELECT master.id as id, master.name as event, DATE_FORMAT(begin, '%W') AS beginDay, DATE_FORMAT(begin, '%l:%i %p') AS beginTime,
                DATE_FORMAT(end, '%l:%i %p') AS endTime, begin, end, ordinal, estimate, room.name as place
                FROM master INNER JOIN room ON room.id = room_id
                WHERE master.kwds_id = '$id' ORDER BY begin, end");
    }

    // Returns a list of class information that you selected
    function get_my_schedule($where) {
       $where = mysqli_real_escape_string($this->connection, $where); 
        return $this->query(
            "SELECT day, `room`.`name` AS RoomName, `class`.`name` AS ClassName, 
            GROUP_CONCAT(user.sca_first,' ',user.sca_last SEPARATOR ', ') AS Prof
            FROM `class`
            LEFT OUTER JOIN `coteacher` ON coteacher.class_id = class.id
            LEFT OUTER JOIN `user` ON user.id = coteacher.user_id
            LEFT OUTER JOIN `room` ON `room`.`id` = class.room_id

            WHERE $where 
            GROUP BY class_id
            ORDER BY `day`"
        );
    }

    // Returns a single record for the number of the next KWDS event
    function get_next_kwds() {
        $result = $this->query("SELECT id FROM kwds WHERE now() < end_date LIMIT 1");
        return $result[0]['id'];
    }


    // Returns a list of information for the previous KWDS events
    function get_previous_kwds() {
        return $this->query("SELECT city, country, end_date, id, state FROM `kwds` WHERE `end_date` < NOW()");
    }

    function get_proceeding_extension($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $return = $this->query("SELECT proceeding from kwds WHERE id = $id");
        if ($return) return $return[0]['proceeding'];
        else return array();
    }

    // Returns a single record of registration information for the event registration page
    function get_registration($kwds) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        $return = $this->query("SELECT linkName, linkUrl, linkDesc FROM ".DB_NAME.".kwds WHERE kwds.id='$kwds'");

        if ($return) return $return[0];
        else return array();
    }

    // Returns a list of a user's roles...**DOUBLE CHECK THIS LOGIC**
    function get_role($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query(
            "SELECT username, job.name as JobName, kwds.id as kwdsID, job.id as JobID, user_id AS UserID
            FROM kwds, role, job, user
            WHERE user_id=user.id AND job.id=job_id AND kwds.id=kwds_id AND role_id='$id'"
        );
    }

    // Returns info about a particular room
    function get_room($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $return = $this->query("SELECT name, room.id as id, building, size, note, floor FROM room WHERE room.id='$id'");

        if ($return) return $return[0];
        else return array();
    }

    // Returns a list of rooms for a particular KWDS
    function get_rooms($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("SELECT name, room.id AS id, building, size, note, floor FROM room WHERE kwds_id='$id' ORDER BY name");
    }

    // Returns a list of classes that have special notes for scheduling
    function get_special_notes($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("SELECT name, other FROM class WHERE kwds_id='$id' and other!=''");
    }

    // Returns a list of all staff members of a particular KWDS
    function get_staff($num) {
       $num = mysqli_real_escape_string($this->connection, $num); 
        return $this->query(
            "SELECT CONCAT(`username`,'(',`job`.`name`,')') as name, `role`.`role_id` as id,
                `job`.`name` AS JobName, `prefix`.`name` AS PrefixName, `user`.`first` AS MundaneFirst,
                `user`.`last` AS MundaneLast, `title`.`name` AS 'TitleName', `user`.`email` AS UserEmail,
                `sca_first` AS SCAFirst, `sca_last` AS SCALast, `job`.`id` AS `JobID`, `role`.`user_id` as UserID
            FROM `title`, `prefix`, `user`, `role`, `job`, `kwds`
            WHERE `title`.`id` = `title_id` AND `prefix`.`id` = `prefix_id` AND `user`.`id` = `user_id`
                AND `job`.`id` = `job_id` AND `kwds`.`id` = `kwds_id` AND `kwds`.`id` = $num
            ORDER BY `job`.`id`"
        );
    }

    // Returns a list of a teacher's classes for a KWDS
    function get_teacher_classes($id, $kwds) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        return $this->query("SELECT class_id FROM coteacher
            INNER JOIN class ON class.id = class_id
            WHERE kwds_id = $kwds AND coteacher.user_id IN ($id)");
    }

    // Returns a list of teachers for a particular KWDS
    function get_teachers($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query(
            "SELECT `user`.`id` AS UserID, `user`.`sca_first` AS SCAFirst, `user`.`sca_last` AS SCALast,
                `user`.`first` AS MundaneFirst, `user`.`last` AS MundaneLast, kwds_id AS KWID, `class`.`id` AS ClassID,
                `class`.`name` AS ClassName
            FROM `user`, `class`, `coteacher`
            WHERE `kwds_id` = '$id' and accepted = 1
                AND coteacher.class_id=class.id AND coteacher.user_id = user.id
            ORDER BY `sca_first`, `sca_last`, `last`, `first`, `class`.`name`"
        );
    }

    function get_users_not_teaching_class($cid){
       $cid = mysqli_real_escape_string($this->connection, $cid); 
        return $this->query(
            "SELECT user.id AS UserID, user.sca_first AS SCAFirst, user.sca_last AS SCALast,
             user.first AS MundaneFirst, user.last AS MundaneLast 
            FROM user 
            WHERE user.id NOT IN (
                SELECT user.id
                FROM user, coteacher 
                WHERE coteacher.user_id = user.id AND coteacher.class_id = $cid)");
    }
    //Returns a list of classes that have not been scheduled yet
    function get_unscheduled_classes($num) {
       $num = mysqli_real_escape_string($this->connection, $num); 
        if (is_class_scheduler($_SESSION['user_id'], $num) == true) {
            return $this->query("SELECT accepted, difficulty_id as DifficultyID, hours, name, other, id AS ClassID, type_id AS TypeID, style_id AS StyleID
                    FROM class WHERE kwds_id='$num' AND (room_id IS NULL OR room_id=0 or room_id='') ORDER BY name");
        }
        return $this->query("SELECT accepted, difficulty_id as DifficultyID, hours, name, id as ClassID, type_id AS TypeID, style_id AS StyleID
                FROM class WHERE kwds_id='$num' AND (room_id IS NULL OR room_id=0 or room_id='') AND accepted=1 ORDER BY name");
    }

    // Returns a list of the updates
    function get_updates() {
        return $this->query(
            "SELECT update.user_id AS user_id, update.description AS description, date, username
            FROM ".DB_NAME.".update, user
            WHERE user.id=user_id
            ORDER BY update.id DESC"
        );
    }

    //Returns a single record of information for a user
    function get_user($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $return = $this->query("SELECT * FROM user WHERE user.id='$id'");

        if ($return) return $return[0];
        else return array();
    }

    // Returns a single record of the user's address
    function get_user_address($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $return = $this->query("SELECT address, city, state, country, zip FROM user WHERE id='$id'");

        if ($return) return $return[0];
        else return array();
    }

    // Returns a list of a classes taught by a user
    function get_user_classes($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query(
            "SELECT DISTINCT class.name as ClassName, class.id as ClassID, kwds_id as KWID, room_id as RoomID, accepted
            FROM class, user, coteacher
            WHERE (coteacher.user_id='$id' AND coteacher.user_id=user.id and class.id = coteacher.class_id)
            ORDER BY kwds_id DESC, class.name ASC"
        );
    }

    // Returns a single record of an user's email address from the database
    function get_user_email($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $result = $this->query("SELECT email FROM user WHERE id='$id'");
        return $result[0]['email'];
    }

    // Returns a single record of profile information for a particular user
    function get_user_info($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $return = $this->query(
            "SELECT `user`.`id` AS UserID, `user`.`first` AS MundaneFirst, `user`.`last` AS MundaneLast,
                `sca_first` AS SCAFirst, `sca_last` AS SCALast, `title`.`name` AS TitleName,
                `prefix`.`name` as PrefixName, `nickname`, `email`, `group`.`name` AS GroupName,
                `group`.`url` AS GroupURL, `kingdom`.`name` AS KingdomName, `kingdom`.`url` AS KingdomURL, `about`
            FROM `user`, `group`, `title`, `prefix`, `kingdom`
            WHERE `title`.`id` = `title_id` AND `prefix`.`id` = `prefix_id` AND `user`.`id` = '$id'
                AND `group`.`id` = `user`.`group_id` AND `kingdom`.`id` = `group`.`kingdom_id`"
        );

        if ($return) return $return[0];
        else return array();
    }

    // Returns a list of jobs that a user has at a particular KWDS **CHECK LOGIC**
    function get_user_job($id, $kwds) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        return $this->query(
            "SELECT job.id as id FROM ".DB_NAME.".kwds, role, job, user
            WHERE job.id=job_id AND kwds.id=kwds_id AND user_id=user.id AND user.id='$id' AND kwds.id='$kwds'
            ORDER BY job.id, kwds.id DESC"
        );
    }

    // Returns a list of people and their jobs at a particular KWDS **CHECK LOGIC**
    function get_user_jobs($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query(
            "SELECT kwds.id as KWID, job.name AS JobName
            FROM ".DB_NAME.".kwds, role, job, user
            WHERE user.id='$id' AND user_id=user.id AND job_id=job.id AND kwds.id=kwds_id"
        );
    }

    // Returns a list of up to 10 usernames from the database
    function get_user_list($search) {
       $search = mysqli_real_escape_string($this->connection, $search); 
        return $this->query("SELECT username, first, last, sca_first, sca_last, id FROM user
            WHERE username LIKE '%$search%' OR first LIKE '%$search%' OR last LIKE '%$search%'
                OR nickname LIKE '%$search%' OR sca_first LIKE '%$search%' OR sca_last LIKE '%$search%'
            ORDER BY username LIMIT 10");
    }

    // Returns a list of a classes taught by a user
    function get_user_submissions($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query(
            "SELECT DISTINCT class.name as ClassName, class.id as ClassID, kwds_id as KWID, room_id as RoomID, accepted
            FROM class, user, coteacher
            WHERE (class.user_id='$id' AND class.user_id=user.id and class.teacher = 0)
            ORDER BY kwds_id DESC, class.name ASC"
        );
    }

    // Returns a single value of a user's nickname, SCA name, mundane name, or username
    function get_username($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $result = $this->query("SELECT nickname, username, sca_first, first FROM user WHERE user.id='$id'");
        if (count($result) == 1) {
            if ($result[0]['nickname'] != "") {
                $name = $result[0]['nickname'];
            } elseif ($result[0]['sca_first'] != "") {
                $name = $result[0]['sca_first'];
            } elseif ($result[0]['first'] != "") {
                $name = $result[0]['first'];
            } else {
                $name = $result[0]['username'];
            }

            return ucfirst($name);
        }
        else {
            return "Unidentified User";
        }
    }

    // Returns a list of usernames from the database
    function get_users_list() {
        return $this->query("SELECT CONCAT(COALESCE(sca_first,''), ' ', COALESCE(sca_last,''), ' [',COALESCE(first,''),' ',COALESCE(last,''),'] ') AS name, id 
            FROM user ORDER BY sca_first, sca_last, first, last");
    }

    // Add a new class to the database
    function insert_class($desc, $diff, $fee, $hours, $kwds, $limit, $name, $notes, $style, $teacher, $type, $url, $user) {
       $desc = mysqli_real_escape_string($this->connection, $desc); 
       $diff = mysqli_real_escape_string($this->connection, $diff); 
       $fee = mysqli_real_escape_string($this->connection, $id); 
       $hours = mysqli_real_escape_string($this->connection, $hours); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
       $limit = mysqli_real_escape_string($this->connection, $limit); 
       $name = mysqli_real_escape_string($this->connection, $name); 
       $notes = mysqli_real_escape_string($this->connection, $notes); 
       $style = mysqli_real_escape_string($this->connection, $style); 
       $teacher = mysqli_real_escape_string($this->connection, $id); 
       $type = mysqli_real_escape_string($this->connection, $type); 
       $url = mysqli_real_escape_string($this->connection, $url); 
       $user = mysqli_real_escape_string($this->connection, $user); 
        $this->query(

            "INSERT INTO ".DB_NAME.".class ( description, difficulty_id, fee, hours,
                kwds_id, class.limit, name, other, style_id, teacher, type_id, url, user_id)
            VALUES ('$desc', '$diff', '$fee', '$hours', 
                '$kwds','$limit', '$name', '$notes', '$style', '$teacher', '$type', '$url', '$user')"
        );
        if ($teacher == 1) {
            $this->query(
                //"INSERT INTO coteacher(user_id, class_id)
                //SELECT user_id, id FROM class WHERE aerobic_id='$aero' AND description='$desc' AND difficulty_id='$diff'
                //    AND era_id='$era' AND fee='$fee' AND hours='$hours' AND kwds_id='$kwds' AND class.limit='$limit'
                //    AND name='$name' AND other='$notes' AND style_id='$style' AND teacher='$teacher' AND type_id='$type'
                //    AND url='$url' AND user_id='$user'");
                "INSERT INTO coteacher(user_id, class_id)
                SELECT user_id, id FROM class WHERE description='$desc' AND difficulty_id='$diff'
                    AND fee='$fee' AND hours='$hours' AND kwds_id='$kwds' AND class.limit='$limit'
                    AND name='$name' AND other='$notes' AND style_id='$style' AND teacher='$teacher' AND type_id='$type'
                    AND url='$url' AND user_id='$user'");
        }
    }

    // Add a new fee to the database
    function insert_fee($kwds, $name, $price, $desc, $pre, $type) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
       $name = mysqli_real_escape_string($this->connection, $name); 
       $price = mysqli_real_escape_string($this->connection, $price); 
       $desc = mysqli_real_escape_string($this->connection, $desc); 
       $pre = mysqli_real_escape_string($this->connection, $pre); 
       $type = mysqli_real_escape_string($this->connection, $type); 
        $this->query(
            "INSERT INTO fees (description, kwds_id, name, prereg, price, fee_type_id)
            VALUES ('$desc', '$kwds', '$name', '$pre', '$price', '$type')"
        );
    }

    // Add a new group to the database
    function insert_group($name, $url, $kingdom) {
       $name = mysqli_real_escape_string($this->connection, $name); 
       $url = mysqli_real_escape_string($this->connection, $url); 
       $kingdom = mysqli_real_escape_string($this->connection, $kingdom); 
        $this->query(
            "INSERT INTO ".DB_NAME.".group (name, url, kingdom_id)
            VALUES ('$name', '$url', '$kingdom')"
        );
    }

    // Insert new KWDS
    function insert_kwds($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        $this->query("INSERT INTO kwds (url, name, group_id, kingdom_id, status_id, begin_date, end_date)
            VALUES ('http://kwds.org/index.php?kwds=$id', 'Unknown', 0, 20, 4, DATE_ADD(CURDATE(),INTERVAL 5 Year),DATE_ADD(CURDATE(),INTERVAL 5 Year))");
    }

    //' Add a new activity to the master schedule
    function insert_master_schedule($name, $kwds, $room, $begin, $end) { 
       $name = mysqli_real_escape_string($this->connection, $name); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
       $room = mysqli_real_escape_string($this->connection, $room); 
       $begin = mysqli_real_escape_string($this->connection, $begin); 
       $end = mysqli_real_escape_string($this->connection, $end); 
        $this->query(
            "INSERT INTO master (name, kwds_id, room_id, begin, end, estimate, useEnd, ordinal)
            VALUES ('$name', '$kwds', '$room', '$begin', '$end', '$estimate', '$useEnd', '$ordinal')"
        );
    }

    // Add a new role to the database
    function insert_role($kwds, $user, $job) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
       $user = mysqli_real_escape_string($this->connection, $user); 
       $job = mysqli_real_escape_string($this->connection, $job); 
        $this->query(
            "INSERT INTO role (kwds_id, job_id, user_id)
            VALUES ('$kwds', '$job', '$user')"
        );
    }

    // Add a new room to the database
    function insert_room($name, $building, $size, $kwds, $notes, $floor='1') {
       $name = mysqli_real_escape_string($this->connection, $name); 
       $building = mysqli_real_escape_string($this->connection, $building); 
       $size = mysqli_real_escape_string($this->connection, $size); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
       $notes = mysqli_real_escape_string($this->connection, $notes); 
       $floor = mysqli_real_escape_string($this->connection, $floor); 
        $this->query(
            "INSERT INTO room (name, building, size, kwds_id, note, floor)
            VALUES ('$name', '$building', '$size', '$kwds', '$notes', '$floor')"
        );
    }

    // Add a teacher to a class
    function insert_teacher($class, $user) {
       $class = mysqli_real_escape_string($this->connection, $class); 
       $user = mysqli_real_escape_string($this->connection, $user); 
        $result = $this->query("SELECT id FROM coteacher WHERE user_id='$user' AND class_id='$class'");
        if (count($result) == 0) {
        $this->query("INSERT INTO coteacher (class_id, user_id)
            VALUES ('$class', '$user')");
        }
    }

    // Add a new update to the database
    function insert_update($id, $desc) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $desc = mysqli_real_escape_string($this->connection, $desc); 
        
        $this->query(
            "INSERT INTO ".DB_NAME.".update (user_id, description, date)
            VALUES ('$id', '$desc', NOW())"
        );
    }

    // Add a new user to the database
    function insert_user($address, $about, $city, $country, $email, $first_name, $group, $last_name,
        $nickname, /**$password,**/ $phone, $prefix, $sca_first, $sca_last, $state, $title, $username, $zip)
    {
       $address = mysqli_real_escape_string($this->connection, $address); 
       $about = mysqli_real_escape_string($this->connection, $about); 
       $city = mysqli_real_escape_string($this->connection, $city); 
       $country = mysqli_real_escape_string($this->connection, $country); 
       $email = mysqli_real_escape_string($this->connection, $email); 
       $first_name = mysqli_real_escape_string($this->connection, $first_name); 
       $group = mysqli_real_escape_string($this->connection, $group); 
       $last_name = mysqli_real_escape_string($this->connection, $last_name); 
       $nickname = mysqli_real_escape_string($this->connection, $nickname); 
       $phone = mysqli_real_escape_string($this->connection, $phone); 
       $prefix = mysqli_real_escape_string($this->connection, $prefix); 
       $sca_first = mysqli_real_escape_string($this->connection, $sca_first); 
       $sca_last = mysqli_real_escape_string($this->connection, $sca_last); 
       $state = mysqli_real_escape_string($this->connection, $state); 
       $title = mysqli_real_escape_string($this->connection, $title); 
       $username = mysqli_real_escape_string($this->connection, $username); 
       $zip = mysqli_real_escape_string($this->connection, $zip); 

        $insert = "INSERT INTO user (email, password, username";
        $values = "VALUES ('$email', '$password', '$username'";
        $insert .= ( $address == "") ? "" : ", address";
        $values .= ( $address == "") ? "" : ", '$address'";
        $insert .= ( $about == "") ? "" : ", about";
        $values .= ( $about == "") ? "" : ", '$about'";
        $insert .= ( $city == "") ? "" : ", city";
        $values .= ( $city == "") ? "" : ", '$city'";
        $insert .= ( $country == "") ? "" : ", country";
        $values .= ( $country == "") ? "" : ", '$country'";
        $insert .= ( $first_name == "") ? "" : ", first";
        $values .= ( $first_name == "") ? "" : ", '$first_name'";
        $insert .= ( $group == "") ? "" : ", group_id";
        $values .= ( $group == "") ? "" : ", '$group'";
        $insert .= ( $last_name == "") ? "" : ", last";
        $values .= ( $last_name == "") ? "" : ", '$last_name'";
        $insert .= ( $nickname == "") ? "" : ", nickname";
        $values .= ( $nickname == "") ? "" : ", '$nickname'";
        $insert .= ( $phone == "") ? "" : ", phone";
        $values .= ( $phone == "") ? "" : ", '$phone'";
        $insert .= ( $prefix == "") ? "" : ", prefix_id";
        $values .= ( $prefix == "") ? "" : ", '$prefix'";
        $insert .= ( $sca_first == "") ? "" : ", sca_first";
        $values .= ( $sca_first == "") ? "" : ", '$sca_first'";
        $insert .= ( $sca_last == "") ? "" : ", sca_last";
        $values .= ( $sca_last == "") ? "" : ", '$sca_last'";
        $insert .= ( $state == "") ? "" : ", state";
        $values .= ( $state == "") ? "" : ", '$state'";
        $insert .= ( $title == "") ? "" : ", title_id";
        $values .= ( $title == "") ? "" : ", '$title'";
        $insert .= ( $zip == "") ? "" : ", zip";
        $values .= ( $zip == "") ? "" : ", '$zip'";
        $insert .= ", created";
        $values .= ", NOW()";
        $insert .= ") ";
        $values .= ")";
        $this->query($insert . $values);

        mail('niquerio@gmail.com', 'New Registered User for Cecilia',
                $first_name.' '.$last_name.'('.$sca_first.' '.$sca_last.') has registered a new account.');
    }

    // Returns the class cut-off date of a KWDS
    function is_class_cutoff($kwds) {
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        //$return = $this->query("SELECT class_date FROM kwds WHERE id='$kwds'");
        $return = $this->query("SELECT class_date FROM kwds WHERE id ='$kwds' AND class_date < now()");
        if ($return) return true;
        else return false;
    }

    // Checks to see if the user submitted or is a teacher of a class
    function is_teacher($class, $user) {
       $class = mysqli_real_escape_string($this->connection, $class); 
       $user = mysqli_real_escape_string($this->connection, $user); 
        $result = $this->query("SELECT class.id FROM class WHERE user_id = '$user' and class.id='$class'");
        if (count($result)>0) {return true;}
        $result = $this->query("SELECT coteacher.id FROM coteacher, class, user
            WHERE coteacher.class_id = class.id AND coteacher.user_id = user.id
            AND class.id = '$class' AND user.id = '$user'");
        if (count($result)>0) {return true;}
        else {return false;}
    }

    // Verifies user login information matchese the database information
    function login($username, $pass, $remember) {
       $username = mysqli_real_escape_string($this->connection, $username); 
       $pass = mysqli_real_escape_string($this->connection, $pass); 
       $remember = mysqli_real_escape_string($this->connection, $remember); 
        global $session;
        $result = $this->query(
            "SELECT id FROM user
            WHERE (username='$username' OR email='$username') AND password='$pass'"
        );
        if (count($result) == 1) {
            $session->login($result[0]['id'], $remember);
        }
        return $result[0];
    }

    // Use this function to call any query
    function query($query_string) {
        $result = mysqli_query($this->connection, $query_string)
                or die('Error is query: ' . $query_string . '.' . mysql_error());

        if ($result === TRUE || $result === FALSE) return $result; //Boolean on DML-type queries

        //Results are null if there are no rows.  Make it an empty array, to play nice with loops
        if (is_null($result)) return array();

        $return = array();
        for ($i = 0; $i < $result->num_rows; $i++) {
            $return[] = $result->fetch_assoc(); //Array of associative arrays on DSL-type queries
        }
        $result->free();

        return $return;
    }

    // Removes the room from a class to take it off the schedule
    function remove_from_schedule($id) {
       $id = mysqli_real_escape_string($this->connection, $id); 
        return $this->query("UPDATE class SET room_id=0 WHERE class.id='$id'");
    }

    function remove_class($cid){
       $cid = mysqli_real_escape_string($this->connection, $cid); 
        return $this->query("DELETE FROM class WHERE class.id='$cid'");
    }

    // Add a teacher to a class
    function remove_teacher($class, $user) {
       $class = mysqli_real_escape_string($this->connection, $class); 
       $user = mysqli_real_escape_string($this->connection, $user); 
        return $this->query("DELETE FROM coteacher WHERE class_id='$class' AND user_id='$user'");
    }
    // Function that lets the database know you need your password changed
    function setup_password($email, $random) {
       $email = mysqli_real_escape_string($this->connection, $email); 
       $random = mysqli_real_escape_string($this->connection, $random); 
        $result = $this->query("SELECT id FROM user WHERE email='$email'");

        if (count($result) == 1) {
            $uid = $result[0]['id'];
            $this->query("INSERT INTO password (user_id, value) VALUES ('$uid', '$random')");
        }
        else echo '<div class="box error">That email does not exist in our system.</div>';
    }

    // Pass in a query to run
    function test($qry) {
        return $this->query($qry);
    }

    //Update the times when a user is planning on being on site.
    function update_attendance($id, $kwds, $begin, $end) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
       $begin = mysqli_real_escape_string($this->connection, $begin); 
       $end = mysqli_real_escape_string($this->connection, $end); 

        $aid = $this->query("SELECT id from attendance WHERE user_id = '$id' AND kwds_id = '$kwds'");
        $aid=$aid[0]['id'];
        if (isset($aid) AND $aid > 0) {
            return $this->query("UPDATE attendance SET arrival = '$begin', departure = '$end' WHERE id= $aid");
        }
        else {
            return $this->query("INSERT INTO attendance (user_id, kwds_id, arrival, departure) VALUES ('$id', '$kwds', '$begin', '$end')");
        }
    }

    //Update the ball information
    function update_concerts($id, $info) {
       $id = mysqli_real_escape_string($this->connection, $id);
       $info = mysqli_real_escape_string($this->connection, $info);
        return $this->query ("UPDATE kwds SET concerts='$info' WHERE id='$id'");
    }

    //Update the ball information
    function update_evening_activities($id, $info) {
       $id = mysqli_real_escape_string($this->connection, $id);
       $info = mysqli_real_escape_string($this->connection, $info);
        return $this->query ("UPDATE kwds SET evening_activities='$info' WHERE id='$id'");
    }
    // Update the information of a class
    function update_class_by_user($cid, $desc, $diff, $fee, $hours, $name, $style, $type, $notes='') {
        $other='';
        if ($notes!='') {
            $notes = mysqli_real_escape_string($this->connection, $notes);
            $other=", other='$notes'";
        }
            $cid = mysqli_real_escape_string($this->connection, $cid);
            $desc = mysqli_real_escape_string($this->connection, $desc);
            $diff = mysqli_real_escape_string($this->connection, $diff);
            $fee = mysqli_real_escape_string($this->connection, $fee);
            //$hours = mysqli_real_escape_string($this->connection, $hours);
            $hours = 50;
            $name = mysqli_real_escape_string($this->connection, $name);
            $style = mysqli_real_escape_string($this->connection, $style);
            $type = mysqli_real_escape_string($this->connection, $type);

        return $this->query(
            "UPDATE class SET description='$desc', 
                difficulty_id='$diff', fee='$fee',hours='$hours',name='$name',
                 style_id='$style', type_id='$type'".$other."
            WHERE id='$cid'"
        );
    }

    // Update the information of a class
    function update_class($accept, $cid, $date, $desc, $diff, $fee, $hours, $name, $room, $style, $type, $notes='') {
        $other='';
        if ($notes!='') {
            $notes = mysqli_real_escape_string($this->connection, $notes);
            $other=", other='$notes'";
        }
            $accept = mysqli_real_escape_string($this->connection, $accept);
            $cid = mysqli_real_escape_string($this->connection, $cid);
            $date = mysqli_real_escape_string($this->connection, $date);
            $desc = mysqli_real_escape_string($this->connection, $desc);
            $diff = mysqli_real_escape_string($this->connection, $diff);
            $fee = mysqli_real_escape_string($this->connection, $fee);
            //$hours = mysqli_real_escape_string($this->connection, $hours);
            $hours = 50;
            $name = mysqli_real_escape_string($this->connection, $name);
            //$room = mysqli_real_escape_string($this->connection, $room);
            $style = mysqli_real_escape_string($this->connection, $style);
            $type = mysqli_real_escape_string($this->connection, $type);

        return $this->query(
            //"UPDATE class SET accepted='$accept', aerobic_id='$aero', day='$date', description='$desc', 
            //    difficulty_id='$diff', era_id='$era',fee='$fee',hours='$hours',name='$name',
            //    room_id='$room', style_id='$style', type_id='$type'".$other."
            //WHERE id='$cid'"
            "UPDATE class SET accepted='$accept', day='$date', description='$desc', 
                difficulty_id='$diff', fee='$fee',hours='$hours',name='$name',
                room_id='$room', style_id='$style', type_id='$type'".$other."
            WHERE id='$cid'"
        );
    }
    // Update class submission date
    function update_classSubmissionDate($date, $id) {
            $date = mysqli_real_escape_string($this->connection, $date);
            $id = mysqli_real_escape_string($this->connection, $id);
        return $this->query ("UPDATE kwds SET class_date='$date' WHERE id='$id'");
    }

    // Updates a fee
    function update_fee($desc, $id, $name, $pre, $price, $type) {
        $desc = mysqli_real_escape_string($this->connection,$desc);
        $id = mysqli_real_escape_string($this->connection,$id);
        $name = mysqli_real_escape_string($this->connection,$name);
        $pre = mysqli_real_escape_string($this->connection,$pre);
        $price = mysqli_real_escape_string($this->connection,$price);
        $type = mysqli_real_escape_string($this->connection,$type);

        return $this->query(
            "UPDATE fees SET description='$desc', name='$name', prereg='$pre', price='$price', fee_type_id='$type'
            WHERE id='$id'"
        );
    }


    // Update the KWDS site information
    function update_kwds($address, $attraction, $concerts, $evening_activities, $banner, $city, $class_date, $country, $desc, $dir, $end_date,
        $facebook, $faq, $food, $group, $kingdom, $kwds, $linkDesc, $linkName, $linkUrl, $lodging, $merchant, $name,
        $parking, $proceeding, $start_date, $state, $status, $zip)
    {
       $address = mysqli_real_escape_string($this->connection, $address);
       $attraction = mysqli_real_escape_string($this->connection, $attraction);
       $concerts = mysqli_real_escape_string($this->connection, $concerts);
       $evening_activities = mysqli_real_escape_string($this->connection, $evening_activities);
       $banner = mysqli_real_escape_string($this->connection, $banner);
       $city = mysqli_real_escape_string($this->connection, $city);
       $class_date = mysqli_real_escape_string($this->connection, $class_date);
       $country = mysqli_real_escape_string($this->connection, $country);
       $desc = mysqli_real_escape_string($this->connection, $desc);
       $dir = mysqli_real_escape_string($this->connection, $dir);
       $end_date = mysqli_real_escape_string($this->connection, $end_date);
       $facebook = mysqli_real_escape_string($this->connection, $facebook);
       $faq = mysqli_real_escape_string($this->connection, $faq);
       $food = mysqli_real_escape_string($this->connection, $food);
       $group = mysqli_real_escape_string($this->connection, $group);
       $kingdom = mysqli_real_escape_string($this->connection, $kingdom);
       $kwds = mysqli_real_escape_string($this->connection, $kwds);
       $linkDesc = mysqli_real_escape_string($this->connection, $linkDesc);
       $linkName = mysqli_real_escape_string($this->connection, $linkName);
       $linkUrl = mysqli_real_escape_string($this->connection, $linkUrl);
       $lodging = mysqli_real_escape_string($this->connection, $lodging);
       $merchant = mysqli_real_escape_string($this->connection, $merchant);
       $name = mysqli_real_escape_string($this->connection, $name);
       $parking = mysqli_real_escape_string($this->connection, $parking); 
       $proceeding = mysqli_real_escape_string($this->connection, $proceeding); 
       $start_date = mysqli_real_escape_string($this->connection, $start_date); 
       $state = mysqli_real_escape_string($this->connection, $state); 
       $status = mysqli_real_escape_string($this->connection, $status); 
       $zip = mysqli_real_escape_string($this->connection, $zip); 
        return $this->query(
            "UPDATE kwds SET address='$address', attractions='$attraction', concerts='$concerts', evening_activities='$evening_activities', banner='$banner', city='$city',
                class_date='$class_date', country='$country', description='$desc', directions='$dir',
                end_date='$end_date', facebook='$facebook', faq='$faq', food='$food', group_id='$group', kingdom_id='$kingdom',
                linkDesc='$linkDesc', linkName='$linkName', linkUrl='$linkUrl', lodging='$lodging',
                merchants='$merchant', name='$name', parking='$parking', proceedings='$proceeding',
                start_date='$start_date', state='$state', status_id='$status', zip='$zip'
            WHERE id='$kwds'"
        );
    }

    // Update user's password
    function update_password($id, $email, $pass) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $email = mysqli_real_escape_string($this->connection, $email); 
       $pass = mysqli_real_escape_string($this->connection, $pass); 
        return $this->query("UPDATE user SET password='$pass' WHERE id='$id' AND email='$email'");
    }

    // Update for successfully uploading class notes for the proceedings
    function update_proceeding_upload($id,$ext) {
       $id = mysqli_real_escape_string($this->connection, $id); 
       $ext = mysqli_real_escape_string($this->connection, $ext); 
        return $this->query("UPDATE class SET upload='$ext' WHERE id='$id'");
    }

    // Update the text for the proceedings page
    function update_proceedings($text,$id,$ext='') {
       $text = mysqli_real_escape_string($this->connection, $text); 
       $id = mysqli_real_escape_string($this->connection, $id); 
       $ext = mysqli_real_escape_string($this->connection, $ext); 
        return $this->query("UPDATE class SET upload='$ext' WHERE id='$id'");
        return $this->query("UPDATE `kwds` SET proceedings='$text',proceeding='$ext' WHERE id='$id'");
    }

    // Update a peron's role/job
    function update_role($role, $user, $job, $kwds) {
       $role = mysqli_real_escape_string($this->connection, $role); 
       $user = mysqli_real_escape_string($this->connection, $user); 
       $job = mysqli_real_escape_string($this->connection, $job); 
       $kwds = mysqli_real_escape_string($this->connection, $kwds); 
        return $this->query("UPDATE role SET user_id='$user', job_id='$job', kwds_id='$kwds' WHERE role_id='$role'");
    }

    // Update a room's information
    function update_room($building, $floor, $name, $note, $roomid, $size) {
       $building = mysqli_real_escape_string($this->connection, $building); 
       $floor = mysqli_real_escape_string($this->connection, $floor); 
       $name = mysqli_real_escape_string($this->connection, $name); 
       $note = mysqli_real_escape_string($this->connection, $note); 
       $roomid = mysqli_real_escape_string($this->connection, $roomid); 
       $size = mysqli_real_escape_string($this->connection, $size); 
        return $this->query("UPDATE room SET building='$building', floor='$floor', name='$name', note='$note', size='$size' WHERE room.id='$roomid'");
    }

    // Update a user's profile infomation
    function update_user($about, $address, $city, $country, $email, $first, $group_id, $id, $last, 
        $nickname, $password, $phone, $prefix_id, $sca_first, $sca_last, $state, $title_id, $username, $zip)
    {
       $about = mysqli_real_escape_string($this->connection, $about); 
       $address = mysqli_real_escape_string($this->connection, $address); 
       $city = mysqli_real_escape_string($this->connection, $city); 
       $country = mysqli_real_escape_string($this->connection, $country); 
       $email = mysqli_real_escape_string($this->connection, $email); 
       $first = mysqli_real_escape_string($this->connection, $first); 
       $group_id = mysqli_real_escape_string($this->connection, $group_id); 
       $id = mysqli_real_escape_string($this->connection, $id); 
       $last = mysqli_real_escape_string($this->connection, $last); 
       $nickname = mysqli_real_escape_string($this->connection, $nickname); 
       $password = mysqli_real_escape_string($this->connection, $password); 
       $phone = mysqli_real_escape_string($this->connection, $phone); 
       $prefix_id = mysqli_real_escape_string($this->connection, $prefix_id); 
       $sca_first = mysqli_real_escape_string($this->connection, $sca_first); 
       $sca_last = mysqli_real_escape_string($this->connection, $sca_last); 
       $state = mysqli_real_escape_string($this->connection, $state); 
       $title_id = mysqli_real_escape_string($this->connection, $title_id); 
       $username = mysqli_real_escape_string($this->connection, $username); 
       $zip = mysqli_real_escape_string($this->connection, $zip); 
        return $this->query(
            "UPDATE user SET about='$about', address='$address', city='$city', country='$country',
                email='$email', first='$first', group_id='$group_id', last='$last', nickname='$nickname',
                password='$password', phone='$phone', prefix_id='$prefix_id', sca_first='$sca_first',
                sca_last='$sca_last', state='$state', title_id='$title_id', username='$username', zip='$zip'
            WHERE user.id='$id'"
        );
    }

    // Verify that the email was entered correctly and matches the password reset page
    function verify_email($x, $email) {
       $x = mysqli_real_escape_string($this->connection, $x); 
       $email = mysqli_real_escape_string($this->connection, $email); 
        $result = $this->query(
            "SELECT user.id FROM user, password
            WHERE user_id=user.id AND user.email='$email' AND value='$x'"
        );

        if (count($result) > 0) {
            return $result[0]['id'];
        }
        else return 0;
    }

    // Returns TRUE/FALSE to verify that the value for changing password is in the database
    function verify_value($x) {
       $x = mysqli_real_escape_string($this->connection, $x); 
        $result = $this->query("select id from password where value='$x'");

        if (count($result) > 0) {
            return true;
        }
        else return false;
    }
}
