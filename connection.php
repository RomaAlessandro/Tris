<?php
// Credenziali per XAMPP su Windows con porta 3307
$host = "localhost"; 
$user = "root";
$pass = "root"; // Di solito vuota su XAMPP
$db   = "tictactoe_neon";

// Unico comando di connessione chiaro
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error)
{
   
    die("Connessione fallita su entrambe le porte (3306/3307): " . $conn->connect_error);
}

// Avviamo la sessione per gestire i login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
