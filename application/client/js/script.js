// Blossom Flora - script.js

// 1. Funció de Login 
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
            result.textContent = "Login successful! Redirecting to Operations Menu...";
            setTimeout(() => {
                window.location.href = "operations_menu.html";
            }, 1000);
        } else {
            //Mostra missatge d'error i torna a l'inici
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

// Activar o desactivar la quantitat de cada flor segons el checkbox 
function toggleProduct(checkbox, inputId) {
    const input = document.getElementById(inputId);
    input.disabled = !checkbox.checked;
    if (checkbox.checked) {
        if (!input.value || parseInt(input.value) <= 0) {
            input.value = 1;
        }
        input.focus();
    }
}

// Validar i enviar comanda de flors
function submitOrder() {
    const form = document.getElementById("orderForm");
    const result = document.getElementById("result");

    const orderId = document.getElementById("order_id").value.trim();
    const customerName = document.getElementById("customer_name").value.trim();
    const customerAddress = document.getElementById("customer_address").value.trim();
    const customerEmail = document.getElementById("customer_email").value.trim();
    const customerPhone = document.getElementById("customer_phone").value.trim();

    // Validació de camps buits al client
    if (orderId === "" || customerName === "" || customerAddress === "" || customerEmail === "" || customerPhone === "") {
        result.className = "msg-box msg-error";
        result.style.display = "block";
        result.textContent = "Error: Please complete all customer identification fields.";
        return;
    }

    // Comprovar que almenys una flor té quantitat > 0
    const enabledInputs = form.querySelectorAll('input[type="number"]:not([disabled])');
    let hasProducts = false;
    enabledInputs.forEach(input => {
        if (parseInt(input.value) > 0) {
            hasProducts = true;
        }
    });

    if (!hasProducts) {
        result.className = "msg-box msg-error";
        result.style.display = "block";
        result.textContent = "Error: Please select at least one flower variety with quantity.";
        return;
    }

    const dadesFormulari = new FormData(form);

    // Enviament a PHP via POST 
    fetch("../server/create_order.php", {
        method: "POST",
        body: dadesFormulari
    })
    .then(resposta => resposta.json())
    .then(dades => {
        if (dades.success) {
            // Visualització del preu amb IVA del 21% calculat pel servidor
            result.className = "msg-box msg-success";
            result.style.display = "block";
            result.innerHTML = "<strong>Order Registered Successfully!</strong><br>" +
                               "Order Code: " + dades.order_id + "<br>" +
                               "Subtotal: " + dades.subtotal + " €<br>" +
                               "VAT (21%): " + dades.vat_amount + " €<br>" +
                               "<strong>Total Price (with 21% VAT): " + dades.total_with_tax + " €</strong>";

            // Reiniciar formulari
            form.reset();
            document.querySelectorAll('.product-item input[type="number"]').forEach(input => {
                input.disabled = true;
            });
        } else {
            result.className = "msg-box msg-error";
            result.style.display = "block";
            result.textContent = "Order Error: " + (dades.message || "Could not register order.");
        }
    })
    .catch(() => {
        result.className = "msg-box msg-error";
        result.style.display = "block";
        result.textContent = "Connection error with order processing server.";
    });
}

// 4. Validació del camp de cerca per ID d'una comanda 
function validateSearchOrder() {
    const input = document.getElementById("search_order_id");
    const result = document.getElementById("result");
    if (!input || input.value.trim() === "") {
        if (result) {
            result.className = "msg-box msg-error";
            result.style.display = "block";
            result.textContent = "Error: Please enter an order identification code.";
        }
        return false;
    }
    return true;
}
