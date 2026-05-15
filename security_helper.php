<?php
// security_helper.php

/**
 * Verifica se l'IP ha superato il limite di tentativi
 */
function checkRateLimit($conn, $ip) {
    $limit = 5; // Massimo 5 tentativi
    $minutes = 2; // In un arco di 2 minuti
    
    // Pulizia automatica: elimina i record più vecchi di 2 minuti
    $conn->query("DELETE FROM login_attempts WHERE last_attempt < (NOW() - INTERVAL $minutes MINUTE)");

    // Controlla lo stato attuale per questo IP
    $stmt = $conn->prepare("SELECT attempts FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['attempts'] >= $limit) {
            return false; // Troppi tentativi, blocca l'operazione
        }
    }
    return true; // Può procedere
}

/**
 * Registra o incrementa un tentativo fallito
 */
function registerAttempt($conn, $ip) {
    $sql = "INSERT INTO login_attempts (ip_address, attempts) VALUES (?, 1) 
            ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ip);
    $stmt->execute();
}

/**
 * Resetta i tentativi (da usare dopo un'operazione riuscita)
 */
function clearAttempts($conn, $ip) {
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
}
?>