<?php
session_start();
session_unset();
session_destroy();

// Debug: Cek apakah session sudah dihancurkan
echo "Session data cleared.";
header("Location: login.php");
exit();
?>
