<?php
require_once 'connection.php';
require_once 'security_helper.php'; // Carica le funzioni di sicurezza
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_SERVER['REMOTE_ADDR']; // Identifica l'utente tramite IP

    // --- 1. PROTEZIONE ANTI-FLOOD ---
    if (!checkRateLimit($conn, $ip)) {
        echo json_encode(["success" => false, "message" => "Troppi tentativi! Riprova tra 2 minuti."]);
        exit;
    }

    // --- 2. VALIDAZIONE CAPTCHA ---
    $user_captcha = isset($_POST['captcha_input']) ? (int)$_POST['captcha_input'] : 0;
    $correct_captcha = isset($_SESSION['captcha_result']) ? (int)$_SESSION['captcha_result'] : -1;

    if ($user_captcha !== $correct_captcha) {
        registerAttempt($conn, $ip); // FALLITO: Segnala il tentativo
        echo json_encode(["success" => false, "message" => "CAPTCHA errato! Riprova."]);
        exit;
    }
    unset($_SESSION['captcha_result']);

    // --- 3. VALIDAZIONE DATI ---
    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass']; 
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#!$])[A-Za-z\d@#!$]{8,12}$/';

    if (!preg_match($pattern, $pass)) {
        registerAttempt($conn, $ip); // FALLITO: Password non valida
        echo json_encode(["success" => false, "message" => "La password non rispetta i criteri!"]);
        exit;
    }

    $check = $conn->query("SELECT * FROM giocatori WHERE username = '$user'");

    if ($check->num_rows > 0) {
        registerAttempt($conn, $ip); // FALLITO: Username già preso
        echo json_encode(["success" => false, "message" => "Username già esistente!"]);
    } else {
        $sql = "INSERT INTO giocatori (username, password, vittorie, sconfitte, pareggi) VALUES ('$user', '$pass', 0, 0, 0)";
        if ($conn->query($sql)) {
            clearAttempts($conn, $ip); // SUCCESSO: Pulisci la cronologia tentativi
            echo json_encode(["success" => true, "message" => "Registrazione riuscita!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Errore nel database."]);
        }
    }
    exit;
}
?>