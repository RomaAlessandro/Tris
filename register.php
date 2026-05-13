<?php include "connection.php"; ?>
<!doctype html>
<html lang="it">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Registrazione Neon</title>
</head>
<body>
    <div class="container">
        <div id="loginScreen"> 
            <h1>Registrazione</h1>
            <form id="registerForm">
                <input type="text" name="user" placeholder="Scegli Username" required>
                <input type="password" name="pass" placeholder="Scegli Password" required>
                <button type="submit">Crea Account</button>
            </form>
            <p id="msg" style="margin-top:15px; font-weight:bold;"></p>
            <p>Hai già un account? <a href="index.php">Accedi qui</a></p>
        </div>
    </div>

    <script>
    document.getElementById('registerForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const msg = document.getElementById('msg');

        fetch('register_process.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            msg.textContent = data.message;
            msg.style.color = data.success ? "#0ff" : "#ff4444";
            if(data.success) setTimeout(() => window.location.href = "index.php", 2000);
        });
    };
    </script>
</body>
</html>