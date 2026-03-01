<?php 
include "connection.php"; 
?>
<!doctype html>
<html lang="it">
<head>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if (!isset($_SESSION['username'])): ?>
            <div id="loginScreen">
                <h1>Accedi per Giocare</h1>
                <form action="login_process.php" method="POST">
                    <input type="text" name="user" placeholder="Username" required>
                    <input type="password" name="pass" placeholder="Password" required>
                    <button type="submit">Entra</button>
                </form>
				<!-- register.php non esiste -->
                <p>Non hai un account? <a href="register.php">Registrati</a></p>
            </div>
            
        <?php else: ?>
    <div id="gameScreen">
        <h2>Benvenuto, <?php echo $_SESSION['username']; ?>!</h2>
        
        <?php
            $user = $_SESSION['username'];
            $res = $conn->query("SELECT vittorie FROM giocatori WHERE username = '$user'");
            $row = $res->fetch_assoc();
        ?>
        <p style="color: #0ff;">Vittorie nel DB: <strong><?php echo $row['vittorie']; ?></strong></p>

        <div id="startScreen">
            <input id="p1" type="hidden" value="<?php echo $_SESSION['username']; ?>">
            <select id="mode">
              <option value="pvp">Giocatore vs Giocatore</option>
              <option value="cpu">Giocatore vs CPU</option>
            </select>
            <input id="p2" placeholder="Avversario (O)">
            <button id="startBtn">Inizia Partita Neon</button>
        </div>

        <div id="actualGame" class="hidden">
            <h2 id="vs"></h2>
            <div id="status">Turno: <b id="turn">X</b></div>
            <div class="board" id="board"></div>
            <div id="score"></div>
            <button id="restartRound">Ricomincia round</button>
            <button id="resetAll">Reset totale</button>
        </div>

        <br>
        <a href="logout.php" style="color: #ff0055; text-decoration: none;">Esci (Logout)</a>
    </div>

    <script src="script.js"></script>
<?php endif; ?>
    </div>
</body>
</html>
