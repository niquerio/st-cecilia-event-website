<?php
/*
 *  Test Document
 */
include_once('includes/header.php');
/*
$conflicts = $db->check_conflicts("SELECT DISTINCT C1.id as id, C1.name,
    CONCAT(COALESCE(U1.sca_first,''), COALESCE(U1.sca_last,''),'[',COALESCE(U1.first,''),COALESCE(U1.last,''),']') as user_id, C1.day,
    ADDTIME(C1.day, SEC_TO_TIME(C1.hours*60)) as end
            FROM class C1
            INNER JOIN coteacher T1 ON T1.class_id = C1.id
            INNER JOIN class C2 ON C2.id != C1.id
            LEFT OUTER JOIN user U1 ON U1.id = T1.user_id
            INNER JOIN coteacher T2 ON C2.id = T2.class_id AND T1.user_id = T2.user_id
            WHERE C1.kwds_ID = 10 AND C2.kwds_ID = 10
                AND ((C1.day >= C2.day AND C1.day <= ADDTIME(C2.day, SEC_TO_TIME(C1.hours*60)))
                OR ((ADDTIME(C1.day, SEC_TO_TIME(C1.hours*60)) >= C2.day AND ADDTIME(C1.day, SEC_TO_TIME(C1.hours*60)) <= ADDTIME(C2.day, SEC_TO_TIME(C1.hours*60)))))
            ORDER BY T1.user_id, C1.day");
*/
/*
$conflicts = $db->test ("SELECT T1.id as id FROM coteacher T1 INNER JOIN coteacher T2 WHERE T1.user_id = T2.user_id
    AND T1.class_id = T2.class_id AND T1.id != T2.id");
 */

$conflicts = $db->test("SELECT id, class_id as name, user_id FROM coteacher ORDER BY user_id, class_id");
echo count($conflicts);
echo '<table border="1"><tr><th>ID</th><th>Class Name</th><th>Teacher</th><th>Begin Date/Time</th><th>End Date/Time</th></tr>';

foreach ($conflicts as $conflict) {
    echo '<tr><td>'.$conflict['id'].'</td>';
    echo '<td>'.$conflict['name'].'</td>';
    echo '<td>'.$conflict['user_id'].'</td>';
    echo '<td>'.$conflict['day'].'</td>';
    echo '<td>'.$conflict['end'].'</td></tr>';
}
echo '</table>';


include_once('includes/footer.php');
?>
