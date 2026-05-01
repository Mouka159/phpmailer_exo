<?php
session_start();
session_destroy();
header('Location: ../page/acceuil.php');
exit();
?>
