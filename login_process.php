<?php
require_once 'connection.php';
require_once 'security_helper.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_SERVER['REMOTE_ADDR'];

    if (!checkRateLimit($conn, $ip)) {
        echo json_encode(["success" => false, "message" => "Troppi tentativi! Riprova tra 2 minuti."]);
        exit;
    }

    // Caso 1: Verifica del Codice OTP (Secondo Fattore)
    if (isset($_POST['otp_code']) && isset($_POST['pending_user'])) {
        $otp = $conn->real_escape_string($_POST['otp_code']);
        $user = $conn->real_escape_string($_POST['pending_user']);

        $sql = "SELECT id, username, code_2fa, expires_2fa FROM giocatori WHERE username = '$user'";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // Verifica se il codice coincide e non è scaduto
            if ($row['code_2fa'] === $otp && strtotime($row['expires_2fa']) > time()) {
                // Login completato con successo!
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // Pulizia codici monouso e tentativi IP
                $conn->query("UPDATE giocatori SET code_2fa = NULL, expires_2fa = NULL WHERE id = " . $row['id']);
                clearAttempts($conn, $ip);

                echo json_encode(["success" => true, "step" => "completed"]);
            } else {
                registerAttempt($conn, $ip);
                echo json_encode(["success" => false, "message" => "Codice OTP errato o scaduto!"]);
            }
        }
        exit;
    }

    // Caso 2: Verifica primo step (Username e Password)
    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass'];

    $sql = "SELECT * FROM giocatori WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Genera OTP di 6 cifre
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        // Scadenza tra 5 minuti
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Salva nel database
        $conn->query("UPDATE giocatori SET code_2fa = '$otp', expires_2fa = '$expires' WHERE id = " . $row['id']);
        
        // Per farti testare subito il codice in localhost senza configurare email, 
        // rimandiamo il codice indietro nel JSON così puoi leggerlo in un alert o vederlo a schermo!
        echo json_encode([
            "success" => true, 
            "step" => "2fa_required", 
            "username" => $user,
            "debug_code" => $otp // <--- Rimuovere in produzione! Serve per testare subito.
        ]);
    } else {
        registerAttempt($conn, $ip);
        echo json_encode(["success" => false, "message" => "Username o password errati!"]);
    }
    exit;
}
?>