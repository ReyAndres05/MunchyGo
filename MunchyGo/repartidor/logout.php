<?php
session_start();
if (isset($_SESSION['repartidor'])) {
    unset($_SESSION['repartidor']);
}

header("Location: ../index.php");
exit;
?>