<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass']; // Per ora in chiaro come il login, ma andrebbe criptata

    // Controlliamo se l'utente esiste già
    $check = $conn->query("SELECT * FROM giocatori WHERE username = '$user'");

    if ($check->num_rows > 0) {
        // Utente già presente, torna alla registrazione con errore
        header("Location: register.php?error=1");
    } else {
        // Inseriamo il nuovo giocatore
        $sql = "INSERT INTO giocatori (username, password, vittorie) VALUES ('$user', '$pass', 0)";
        
        if ($conn->query($sql)) {
            // Registrazione riuscita! Mandalo al login
            header("Location: index.php?registered=1");
        } else {
            echo "Errore durante la registrazione: " . $conn->error;
        }
    }
}
?>