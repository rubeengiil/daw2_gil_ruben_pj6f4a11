// Funció de Login (Funcionalitat 3)
function login() {
    const form = document.getElementById("loginForm");
    const result = document.getElementById("result");
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();

    // Validació de camps buits des del client 
    if (username === "" || password === "") {
        result.className = "msg-box msg-error";
        result.style.display = "block";
        result.textContent = "Error: Please enter username and password.";
        return;
    }

    const dadesFormulari = new FormData(form);

    fetch("../server/login.php", {
        method: "POST",
        body: dadesFormulari
    })
        .then(resposta => resposta.json())
        .then(dades => {
            if (dades.success) {
                result.className = "msg-box msg-success";
                result.style.display = "block";
                result.textContent = "Login successful! Redirecting...";
                setTimeout(() => {
                    window.location.href = "operations_menu.html";
                }, 1000);
            } else {
                result.className = "msg-box msg-error";
                result.style.display = "block";
                result.textContent = "Error: Invalid credentials. Returning to Home...";
                setTimeout(() => {
                    window.location.href = "index.html";
                }, 2000);
            }
        })
        .catch(() => {
            result.className = "msg-box msg-error";
            result.style.display = "block";
            result.textContent = "Connection error with server.";
        });
}
