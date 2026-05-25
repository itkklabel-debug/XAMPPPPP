<?php
session_start();
session_unset();
session_destroy();
header('Location: /XAMPPPPP/login.php');
exit;
