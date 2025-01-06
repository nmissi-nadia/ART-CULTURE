<?php
session_start(); // Démarre la session


$_SESSION = [];

session_destroy();
header('Location: ./login.php');
exit();
