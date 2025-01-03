<?php
session_start();
session_unset();
session_destroy();

// Redirect to the publicHome page
header("Location: publicHome.html");
exit();
?>
