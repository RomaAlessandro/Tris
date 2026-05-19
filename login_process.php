<?php
require_once 'connection.php';
require_once 'security_helper.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_SERVER['REMOTE_ADDR'];

    if (!checkRateLimit($conn, $ip)) {
        // Logghiamo anche il blocco temporaneo per flooding
        $attempted_user = isset($_POST['user']) ? $_POST['user'] : (isset($_POST['pending_user']) ? $_POST['pending_user'] : 'Sconosciuto');
        logAccess($conn, $attempted_user, $ip, 'fallimento', 'Bloccato da Rate Limiting');
        
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
                // LOGIN COMPLETATO CON SUCCESSO!
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // Pulizia codici monouso e tentativi IP
                $conn->query("UPDATE giocatori SET code_2fa = NULL, expires_2fa = NULL WHERE id = " . $row['id']);
                clearAttempts($conn, $ip);

                // --- LOG DI SUCCESSO DEFINITIVO ---
                logAccess($conn, $user, $ip, 'successo', 'Autenticazione 2FA superata');

                echo json_encode(["success" => true, "step" => "completed"]);
            } else {
                registerAttempt($conn, $ip);
                
                // --- LOG DI FALLIMENTO OTP ---
                logAccess($conn, $user, $ip, 'fallimento', 'Codice OTP errato o scaduto');
                
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
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Salva nel database
        $conn->query("UPDATE giocatori SET code_2fa = '$otp', expires_2fa = '$expires' WHERE id = " . $row['id']);
        
        // --- LOG STEP 1 SUPERATO ---
        logAccess($conn, $user, $ip, '2fa_attesa', 'Password corretta, attesa OTP');

        echo json_encode([
            "success" => true, 
            "step" => "2fa_required", 
            "username" => $user,
            "debug_code" => $otp 
        ]);
    } else {
        registerAttempt($conn, $ip);
        
        // --- LOG DI FALLIMENTO CREDENZIALI ---
        logAccess($conn, $user, $ip, 'fallimento', 'Username o password errati');
        
        echo json_encode(["success" => false, "message" => "Username o password errati!"]);
    }
    exit;
}
?>