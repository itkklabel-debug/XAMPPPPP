<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';
session_unset();
session_destroy();
header('Location: ' . BASE_URL . '/login.php');
exit;
