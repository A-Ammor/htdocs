<?php
session_start();
require_once 'Db.php';


$username = strip_tags($_POST['username']);
$password = strip_tags($_POST['password']);
$department = strip_tags($_POST['department']);
$department_id = strip_tags($_POST['department_id']);

$stmt = $pdo->prepare('SELECT mng_username, mng_password FROM department WHERE mng_username = ? AND department_name = ? AND department_id = ?');
$stmt->execute(array($username, $department, $department_id));

if($stmt) {
    $result = $stmt->fetch();

    $encrypted_password = $result['mng_password'];

    if(password_verify($password, $encrypted_password)) {

        $_SESSION['username'] = $username;
        $_SESSION['department'] = $department;
        $_SESSION['department_id'] = $department_id;

        $json = array('success' => TRUE);

    } else {
        $json = array('success' => FALSE);
    }

} else {
    $json = array('success' => FALSE);
}

echo json_encode($json);
