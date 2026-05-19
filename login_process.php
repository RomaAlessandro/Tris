<?php
require_once 'connection.php';
require_once 'security_helper.php'; // 1. Includiamo il controllo sicurezza
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_SERVER['REMOTE_ADDR']; // Recuperiamo l'IP del client

    // 2. VERIFICA RATE LIMIT (Blocco se > 5 tentativi in 2 minuti)
    if (!checkRateLimit($conn, $ip)) {
        echo json_encode(["success" => false, "message" => "Troppi tentativi di accesso! Riprova tra 2 minuti."]);
        exit;
    }

    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass'];

    // NOTA: Se in futuro userai password_hash(), qui servirà password_verify()
    $sql = "SELECT * FROM giocatori WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        
        // SUCCESSO: Cancelliamo la cronologia degli errori per questo IP
        clearAttempts($conn, $ip); 
        
        echo json_encode(["success" => true]);
    } else {
        // FALLIMENTO: Registriamo il tentativo fallito nel DB
        registerAttempt($conn, $ip); 
        
        echo json_encode(["success" => false, "message" => "Username o password errati!"]);
    }
    exit;
}
?>