<?php
// Credenziali per XAMPP su Windows con porta 3307
$host = "127.0.0.1"; 
$user = "root";
$pass = ""; // Di solito vuota su XAMPP
$db   = "tictactoe_neon";
$port = 3307; 

// Unico comando di connessione chiaro
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    // Se la 3307 fallisce, facciamo un ultimo tentativo sulla standard 3306
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        die("Connessione fallita su entrambe le porte (3306/3307): " . $conn->connect_error);
    }
}

// Avviamo la sessione per gestire i login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
