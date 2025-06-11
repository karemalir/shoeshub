<?php
require 'config.php';

$count = 0;
if (!empty($_SESSION['cart'])) {
    $count = array_sum($_SESSION['cart']);
}

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>