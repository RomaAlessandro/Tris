<?php
	session_start();
	session_destroy(); // Distrugge la sessione attuale
	header("Location:index.php"); // Ti riporta al login
	exit();
?>
