<?php
include 'core/UserAuth.php';

$auth = new UserAuth(null);
$auth->logout();
header('Location: login.php');
exit;
?>
