<?php
session_start();
unset($_SESSION['department']);
unset($_SESSION['username']);
unset($_SESSION['department_id']);
unset($_SESSION['person_id']);
header("Location: login.php");