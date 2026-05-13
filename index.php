<?php include "connection.php"; ?>
<!doctype html>
<html lang="it">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Tic Tac Toe Neon</title>
</head>
<body>
    <div class="container">
        <?php if (!isset($_SESSION['username'])): ?>
            <div id="loginScreen">
                <h1>Accedi</h1>
                <form id="loginForm">
                    <input type="text" name="user" placeholder="Username" required>
                    <input type="password" name="pass" placeholder="Password" required>
                    <button type="submit">Entra</button>
                </form>
                <p id="loginMsg" style="color: #ff4444; margin-top:10px;"></p>
                <p>Non hai un account? <a href="register.php">Registrati</a></p>
            </div>
            
            <script>
            document.getElementById('loginForm').onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('login_process.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if(data.success) location.reload();
                    else document.getElementById('loginMsg').textContent = data.message;
                });
            };
            </script>
            
        <?php else: ?>
            <div id="gameScreen">
                <h2>Benvenuto, <?php echo $_SESSION['username']; ?>!</h2>
                <?php
                    $user = $_SESSION['username'];
                    $res = $conn->query("SELECT vittorie, sconfitte, pareggi FROM giocatori WHERE username = '$user'");
                    $row = $res->fetch_assoc();
                ?>
                <div id="userStats" style="color: #0ff; margin-bottom: 15px;">
                    V: <strong id="stat-vittorie"><?php echo $row['vittorie']; ?></strong> | 
                    S: <strong id="stat-sconfitte"><?php echo $row['sconfitte']; ?></strong> | 
                    P: <strong id="stat-pareggi"><?php echo $row['pareggi']; ?></strong>
                </div>

                <div id="startScreen">
                    <input id="p1" type="hidden" value="<?php echo $_SESSION['username']; ?>">
                    <select id="mode">
                      <option value="pvp">Giocatore vs Giocatore</option>
                      <option value="cpu">Giocatore vs CPU</option>
                    </select>
                    <input id="p2" placeholder="Avversario (O)">
                    <button id="startBtn">Inizia Partita</button>
                </div>

                <div id="actualGame" class="hidden">
                    <h2 id="vs"></h2>
                    <div id="status">Turno: <b id="turn">X</b></div>
                    <div class="board" id="board"></div>
                    <div id="score"></div>
                    <button id="restartRound">Ricomincia round</button>
                    <button id="resetAll">Home</button>
                </div>
                <br>
                <a href="logout.php" style="color: #ff0055;">Esci (Logout)</a>
            </div>
            <script src="script.js"></script>
        <?php endif; ?>
    </div>
</body>
</html>