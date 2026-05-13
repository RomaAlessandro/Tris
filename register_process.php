<?php
require_once 'connection.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass']; 

    $check = $conn->query("SELECT * FROM giocatori WHERE username = '$user'");

    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username già esistente!"]);
    } else {
        $sql = "INSERT INTO giocatori (username, password, vittorie, sconfitte, pareggi) VALUES ('$user', '$pass', 0, 0, 0)";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Registrazione riuscita! Verrai reindirizzato al login..."]);
        } else {
            echo json_encode(["success" => false, "message" => "Errore: " . $conn->error]);
        }
    }
    exit;
}
?>