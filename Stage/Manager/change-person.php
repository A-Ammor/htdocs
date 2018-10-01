<?php
session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['department']) || !isset($_SESSION['department_id'])) {
    exit;
}
$_SESSION['person_id'] = strip_tags($_POST['id']);