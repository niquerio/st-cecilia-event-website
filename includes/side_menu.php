<div id="left_sidebar">
    <?php if ($session->isLoggedIn()) {
        global $_SESSION;
        echo '<div class="welcome">Welcome, '.$db->get_username($_SESSION['user_id']).'!</div>';
    } ?>
    <h2>Main Menu</h2>
    <ul>
        <li><a href="directions.php?kwds=<?php echo $kwds['KWID'] ?>">Directions</a></li>
        <li><a href="master.php?kwds=<?php echo $kwds['KWID'] ?>">Master Schedule</a></li>
        <li><a href="classes.php?kwds=<?php echo $kwds['KWID'] ?>">Class Listing</a></li>
        <li><a href="schedule.php?kwds=<?php echo $kwds['KWID'] ?>">Class Schedule</a></li>
        <li><a href="concerts.php?kwds=<?php echo $kwds['KWID'] ?>">Concert</a></li>
        <li><a href="evening_activities.php?kwds=<?php echo $kwds['KWID'] ?>">Evening Activities</a></li>
        <li><a href="registration.php?kwds=<?php echo $kwds['KWID'] ?>">Site Fees</a></li>
        <li><a href="food.php?kwds=<?php echo $kwds['KWID'] ?>">Food</a></li>
        <li><a href="about.php?kwds=<?php echo $kwds['KWID'] ?>">FAQ</a></li>
        <!--<li><a href="attractions.php?kwds=<?php echo $kwds['KWID'] ?>">Local Attractions</a></li>-->
        <li><a href="lodging.php?kwds=<?php echo $kwds['KWID'] ?>">Lodging</a></li>
        <!--<li><a href="merchants.php?kwds=<?php echo $kwds['KWID'] ?>">Merchants</a></li>-->
        <!--<li><a href="parking.php?kwds=<?php echo $kwds['KWID'] ?>">Parking</a></li>-->
        <!--<li><a href="proceedings.php?kwds=<?php echo $kwds['KWID'] ?>">Proceedings</a></li>-->
        <li><a href="contacts.php?kwds=<?php echo $kwds['KWID'] ?>">Staff</a></li>
        <li><a href="teacher.php?kwds=<?php echo $kwds['KWID'] ?>">Teachers</a></li>
        <li><a href="index.php?kwds=<?php echo $kwds['KWID'] ?>">Home</a></li>
    </ul>

    <h2>User Options</h2>
    <ul>
        <?php if (!$session->isLoggedIn()) { ?>
        <li><a href="register.php">Create an Account</a></li> 
        <li><a href="login.php">Login</a></li>
        <li><a href="login.php?submit=1">Submit a Class</a></li>

        <?php } else { ?>
        <li><a href="group.php">Add a SCA Group</a></li>

        <?php if (is_super_user()) {
            echo '<li><a href="quick.php">Quick Login</a></li>';
        } ?>

        <li><a href="class.php?kwds=<?php echo $kwds['KWID']; ?>">Submit a Class</a></li>
        <?php if (can_add_rooms($_SESSION['user_id'], $kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds()) { ?>
        <li><a href="updates.php">Updates</a></li>
        <?php } ?>
        <li><a href="profile.php">View Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
        <?php } ?>
    </ul>
    
    <?php if (can_add_rooms($_SESSION['user_id'], $kwds['KWID']) OR is_super_user()) {
        echo '<h2>Class Options</h2>
        <ul>';
    }
        if (can_add_rooms($_SESSION['user_id'], $kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds() OR is_super_user()) {
            echo'<li><a href="room.php?kwds='.$kwds['KWID'].'">Add/Edit Rooms</a></li>
            <li><a href="class_schedule.php?kwds='.$kwds['KWID'].'">Edit Class Schedule</a></li>
            <li><a href="edit_concert.php?kwds='.$kwds['KWID'].'">Edit Concert Information</a></li>
            <li><a href="edit_evening_activities.php?kwds='.$kwds['KWID'].'">Edit Evening Activities Information</a></li>';
                }
         if (can_add_rooms($_SESSION['user_id'], $kwds['KWID']) OR is_super_user()) {
            echo'<li><a href="messages.php?kwds='.$kwds['KWID'].'">Send Mass Email</a></li>
        </ul>
';
    }
    $staff = $db->get_staff($kwds['KWID']);
    if ((is_autocrat($_SESSION['user_id'], $kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds()) 
            OR (is_autocrat($_SESSION['user_id'], ($kwds['KWID']-1)) AND count($staff)==0)
            OR is_super_user()) {
        echo '<h2>Site Options</h2>
        <ul>';
        
        if (is_autocrat($_SESSION['user_id'], ($kwds['KWID']-1)) AND count($staff)==0) {
            echo '<li><a href="staff.php?kwds='.$kwds['KWID'].'">Add Next Autocrat</a></li>';
        }
        else {
        echo'
            <li><a href="kwds.php?kwds='.$kwds['KWID'].'">Edit St. Cecilia Site Info</a></li>
            <li><a href="fees.php?kwds='.$kwds['KWID'].'">Add/Edit Site Fees</a></li>
            <li><a href="staff.php?kwds='.$kwds['KWID'].'">Edit Staff</a></li>';
        }
        echo'
        </ul>
';
        
    } ?>


    <h2>Upcoming Events</h2>
    <ul>
    <?php foreach ($db->get_future_kwds() as $row) {
        $title = $row['city'];
        if ($row['city'] != "" && $row['state'] != "") $title .= ', ';
        $title .= $row['state'];
        if ($row['state'] != "" && $row['country'] != "") $title .= ', ';
        $title .= $row['country'];
        ?>
        <li>
            <a href="index.php?kwds=<?php echo $row['id']; ?>" title="<?php echo $title; ?>">
                St. Cecilia <?php echo roman($row['id']); ?> (<?php echo date('Y', strtotime($row['end_date'])); ?>)
            </a>
        </li>
    <?php } ?>
    </ul>

    <h2>Previous Events</h2>
    <ul>
    <?php foreach ($db->get_previous_kwds() as $row) {
        $title = $row['city'];
        if ($row['city'] != "" && $row['state'] != "") $title .= ', ';
        $title .= $row['state'];
        if ($row['state'] != "" && $row['country'] != "") $title .= ', ';
        $title .= $row['country'];
        echo '<li><a href="index.php?kwds=' . $row['id'] . '" title="' . $title . '">St. Cecilia '.
            roman($row['id']) . ' (' . date('Y', strtotime($row['end_date'])) . ')</a></li>';
    } ?>
    </ul>

    <h2>Links</h2>
    <ul>
        <li><a href="http://www.sca.org/" title="Society for Creative Anachronism, Inc." target="_blank">SCA</a></li>
        <li><a href="http://www.cynnabar.org/" title="The Barony of Cynnabar" target="_blank">Cynnabar</a></li>
    </ul>
</div>
