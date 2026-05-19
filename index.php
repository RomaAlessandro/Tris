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
        <input type="text" name="user" id="loginUser" placeholder="Username" required>
        <input type="password" name="pass" id="loginPass" placeholder="Password" required>
        <button type="submit">Entra</button>
    </form>

    <form id="2faForm" style="display:none; margin-top: 15px;">
        <h3 style="color: #0ff;">Autenticazione 2FA</h3>
        <p style="font-size:0.85em; color:#bbb;">Inserisci il codice di sicurezza a 6 cifre.</p>
        <input type="text" name="otp_code" placeholder="Codice OTP (6 cifre)" maxlength="6" required style="text-align:center; font-size: 1.2rem; letter-spacing: 5px;">
        <input type="hidden" name="pending_user" id="pendingUser">
        <button type="submit" style="border-color: #ff0055; color: #ff0055; text-shadow: 0 0 5px #ff0055;">Verifica Codice</button>
    </form>

    <p id="loginMsg" style="color: #ff4444; margin-top:10px; font-weight:bold;"></p>
    <p id="registerLink">Non hai un account? <a href="register.php">Registrati</a></p>
</div>

<script>
// Gestione del Primo Form (Login Standard)
document.getElementById('loginForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const msg = document.getElementById('loginMsg');
    
    fetch('login_process.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if(data.success && data.step === "2fa_required") {
            // Mostra il codice generato in un box per simulare la ricezione (visto che siamo su localhost)
            alert("🔑 [SIMULAZIONE 2FA] Il tuo codice OTP è: " + data.debug_code);
            
            // Nascondi login e mostra inserimento OTP
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerLink').style.display = 'none';
            document.getElementById('2faForm').style.display = 'block';
            document.getElementById('pendingUser').value = data.username;
            msg.textContent = ""; 
        } else {
            msg.textContent = data.message;
            msg.style.color = "#ff4444";
        }
    });
};

// Gestione del Secondo Form (Verifica OTP)
document.getElementById('2faForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const msg = document.getElementById('loginMsg');

    fetch('login_process.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            location.reload(); // Entra nel gioco!
        } else {
            msg.textContent = data.message;
            msg.style.color = "#ff4444";
        }
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