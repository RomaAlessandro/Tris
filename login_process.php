<!-- Nulla da dire ci stai ancora lavorando aspetto che tu sia in fase piu' avanzata -->
<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Usiamo $conn perché è il nome della variabile definita nel  connection.php
    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass'];

    try {
        // Cerchiamo l'utente con i nuovi nomi delle colonne
        $sql = "SELECT * FROM giocatori WHERE username = '$user' AND password = '$pass'";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // Salviamo i dati nella sessione
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Se tutto va bene, torniamo alla pagina principale del gioco
            header("Location:index.php"); 
            exit();
        } else {
            // Se i dati sono sbagliati
            header("Location: index.php?error=1");
            exit();
        }
    } catch (Exception $e) {
        die("Errore nel sistema: " . $e->getMessage());
    }
}
?>