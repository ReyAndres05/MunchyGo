<?php
session_start();
include "../db.php";
if (isset($_SESSION['repartidor'])) {
    header("Location: panel.php");
    exit();
}
$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['clave'])) {
    $usuario = trim($_POST['usuario']);
    $clave = trim($_POST['clave']);
    if (is_numeric($usuario)) {
        $sql = "SELECT id_repartidor, contraseña FROM repartidor WHERE telefono = ?";
    } else {
        $sql = "SELECT id_repartidor, contraseña FROM repartidor WHERE correo = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows == 1) {
        $repartidorData = $result->fetch_assoc();
        if ($clave === $repartidorData['contraseña']) {
            $_SESSION['repartidor'] = [
                'id' => $repartidorData['id_repartidor']
            ];
            header("Location: panel.php");
            exit;
        } else {
            $err = "Contraseña incorrecta.";
        }
    } else {
        $err = "El correo electrónico que ingresaste no está conectado a una cuenta.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MunchyGo</title>
    <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #ff4d00;
        --secondary-color: #e50914;
        --background-color: #f8f9fa;
        --text-color: #333;
        --white: #ffffff;
    }
    body {
        font-family: 'Segoe UI', sans-serif;
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .login-container {
        max-width: 415px;
        width: 100%;
        padding: 2.5rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    .form-floating {
        margin-bottom: 1rem;
        position: relative;
    }
    .form-control {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(255, 77, 0, 0.15);
    }
    .btn-login {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        color: var(--white);
        padding: 12px;
        border-radius: 10px;
        width: 100%;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 77, 0, 0.3);
    }
    .error-message {
        background-color: #ffe5e5;
        color: #d63031;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 1rem;
        text-align: center;
    }
    .register-link {
        text-align: center;
        margin-top: 1rem;
        color: var(--text-color);
    }
    .register-link a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }
    .register-link a:hover {
        color: var(--secondary-color);
    }
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
    }
    .toggle-password:hover {
        color: var(--primary-color);
    }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center mb-1">Iniciar Sesión</h1>
        <?php if (!empty($err)): ?>
            <div class="error-message"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>
        <p class="text-center text-muted mb-4">Inicia sesión para acceder a tus entregas y ganancias</p>
        <form method="POST">
            <div class="form-floating">
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Correo electrónico o teléfono" required>
                <label for="usuario">Correo electrónico o teléfono</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="clave" name="clave" placeholder="Contraseña" required>
                <label for="clave">Contraseña</label>
                <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <button type="submit" class="btn btn-login">Iniciar sesión</button>
            <p class="register-link"><a href="">¿Olvidaste tu contraseña?</a></p>
        </form>
    </div>

    <script>
    document.getElementById("togglePassword").addEventListener("click", function() {
        const input = document.getElementById("clave");
        const icon = this;
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
    </script>
</body>
</html>