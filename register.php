<?php include "connection.php"; ?>
<!doctype html>
<html lang="it">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Registrazione Neon</title>
    <style>
        /* Stili per il feedback visivo immediato */
        .requirement { transition: all 0.3s ease; margin: 5px 0; font-size: 0.85em; }
        .invalid { color: #ff4444; text-shadow: 0 0 5px #ff4444; }
        .valid { color: #0ff; text-shadow: 0 0 8px #0ff; }
        
        #captcha-container {
            margin: 15px 0; 
            border: 1px solid #0ff; 
            padding: 10px; 
            border-radius: 5px; 
            background: rgba(0, 255, 255, 0.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="loginScreen"> 
            <h1>Registrazione</h1>
            <form id="registerForm">
                <input type="text" name="user" placeholder="Scegli Username" required>
                
                <input type="password" id="pass" name="pass" placeholder="Scegli Password" maxlength="12" required>
                
                <div id="psw-feedback" style="text-align: left; margin: 10px auto; width: 80%;">
                    <div id="char-length" class="requirement invalid">● 8-12 caratteri</div>
                    <div id="char-upper" class="requirement invalid">● Almeno una maiuscola</div>
                    <div id="char-lower" class="requirement invalid">● Almeno una minuscola</div>
                    <div id="char-num" class="requirement invalid">● Almeno un numero</div>
                    <div id="char-special" class="requirement invalid">● Un carattere speciale (@, #, !, $)</div>
                </div>

                <div id="captcha-container">
                    <label for="captcha_input" style="display: block; margin-bottom: 5px; font-size: 0.9em;">Verifica Umana:</label>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <img src="captcha_gen.php" id="captcha-img" alt="CAPTCHA" style="border: 1px solid #0ff; border-radius: 3px;">
                        <button type="button" onclick="refreshCaptcha()" style="padding: 5px; width: auto; border: 1px solid #ff0055; color: #ff0055; background: transparent; cursor: pointer;">🔄</button>
                    </div>
                    <input type="text" name="captcha_input" id="captcha_input" placeholder="Risultato?" required style="width: 50%; margin-top: 10px;">
                </div>

                <button type="submit" id="regBtn" disabled>Crea Account</button>
            </form>
            <p id="msg" style="margin-top:15px; font-weight:bold;"></p>
            <p>Hai già un account? <a href="index.php">Accedi qui</a></p>
        </div>
    </div>

    <script>
    const passInput = document.getElementById('pass');
    const regBtn = document.getElementById('regBtn');

    // Funzione per aggiornare l'immagine del CAPTCHA
    function refreshCaptcha() {
        document.getElementById('captcha-img').src = 'captcha_gen.php?' + Math.random();
    }

    // Regole di validazione password
    const requirements = {
        "char-length": (v) => v.length >= 8 && v.length <= 12,
        "char-upper": (v) => /[A-Z]/.test(v),
        "char-lower": (v) => /[a-z]/.test(v),
        "char-num": (v) => /\d/.test(v),
        "char-special": (v) => /[@#!$]/.test(v)
    };

    // Controllo password in tempo reale
    passInput.addEventListener('input', () => {
        const val = passInput.value;
        let isAllValid = true;

        for (const id in requirements) {
            const isValid = requirements[id](val);
            const el = document.getElementById(id);
            
            if (isValid) {
                el.classList.remove('invalid');
                el.classList.add('valid');
                if (el.innerText.startsWith("●")) {
                    el.innerText = "✓ " + el.innerText.substring(2);
                }
            } else {
                el.classList.remove('valid');
                el.classList.add('invalid');
                if (el.innerText.startsWith("✓")) {
                    el.innerText = "● " + el.innerText.substring(2);
                }
                isAllValid = false;
            }
        }

        // Attiva/Disattiva bottone invio
        regBtn.disabled = !isAllValid;
        regBtn.style.opacity = isAllValid ? "1" : "0.5";
        regBtn.style.cursor = isAllValid ? "pointer" : "not-allowed";
    });

    // Gestione invio modulo con Fetch
    document.getElementById('registerForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const msg = document.getElementById('msg');

        fetch('register_process.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            msg.textContent = data.message;
            msg.style.color = data.success ? "#0ff" : "#ff4444";
            
            if(data.success) {
                setTimeout(() => window.location.href = "index.php", 2000);
            } else {
                // Se errore (es. captcha errato), resetta captcha e campo input
                refreshCaptcha();
                document.getElementById('captcha_input').value = "";
            }
        })
        .catch(error => {
            msg.textContent = error;
            msg.style.color = "#ff4444";
        });
    };
    </script>
</body>
</html>