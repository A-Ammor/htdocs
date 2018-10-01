<?php
session_start();
require_once 'Db.php';
?>


<!DOCTYPE html>
<html>
<body>

<?php
$id_invitee = 2;
$id_person = 1;
$list = array();
$invitee_ids = array();
$invitee_id = array();
$remark_msgs = array();
$remark_msg = array();

//echo "<img src='https://softwareguardian.eu/talentpass/avatars/{$id_invitee}.jpeg' alt='avatars' height=\"100\" width=\"100\">" . "<br>";

//
//$sth = $pdo->prepare("SELECT distinct id_invitee, appreciation_remark FROM feedback_rel where id_invitee = 1 AND id_person = $id_person ORDER BY period_id desc limit 5");
//$sth->execute(array($id_invitee));
//$result = $sth->fetchAll();
//foreach ($result as $row) {
//    echo $row['appreciation_remark'] . "<br><br>";
//}

$sth = $pdo->prepare("SELECT distinct id_invitee, appreciation_remark FROM feedback_rel where id_person = $id_person ORDER BY period_id desc");
$sth->execute(array($id_invitee));
$result = $sth->fetchAll();
foreach ($result as $row) {
    $invitee = $row['id_invitee'];
    $remark_msgs = $row['appreciation_remark'];
    $invitee_ids = $row['id_invitee'];

    if(!in_array($invitee_ids, $invitee_id)){
        $invitee_id[] = $invitee_ids;
    }
    if(!in_array($remark_msgs, $remark_msg)){
        $remark_msg[] = $remark_msgs;
    }

//    $person = array(
//        'id_invitee' => $invitee,
//        'appreciation_remark' => $remark);
//    array_push($list, $person);
//    print_r($person);


}
foreach ($invitee_id as $id_avatar) {
     echo "<img src='https://softwareguardian.eu/talentpass/avatars/{$id_avatar}.jpeg' alt='avatars' height=\"100\" width=\"100\">" . "<br>";
    foreach ($remark_msg as $msg){
        echo $msg . "<br>";
    }
}
?>


</body>
</html>