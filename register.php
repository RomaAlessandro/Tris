<?php include "connection.php"; ?>
<!doctype html>
<html lang="it">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Registrazione Neon</title>
</head>
<body>
    <div class="container">
        <div id="loginScreen"> <h1>Registrazione</h1>
            <form action="register_process.php" method="POST">
                <input type="text" name="user" placeholder="Scegli Username" required>
                <input type="password" name="pass" placeholder="Scegli Password" required>
                <button type="submit">Crea Account</button>
            </form>
            <p>Hai già un account? <a href="index.php">Accedi qui</a></p>
            <?php if(isset($_GET['error'])): ?>
                <p style="color: #ff4444;">Username già esistente!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>