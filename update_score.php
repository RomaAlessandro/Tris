<?php
include "connection.php";
if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    // Aumenta di 1 le vittorie dell'utente loggato
    $conn->query("UPDATE giocatori SET vittorie = vittorie + 1 WHERE username = '$user'");
}
?>