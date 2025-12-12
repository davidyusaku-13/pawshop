<?php
include 'config.php';

// Use the secure logout function
logoutUser();

header('Location: login.php');
exit;
