<?php
session_start();

require_once 'config/database.php';
require_once 'controllers/authcontroller.php';

$db = new Database();
$conn = $db->getConnection();

$auth = new AuthController($conn);
$auth->login();