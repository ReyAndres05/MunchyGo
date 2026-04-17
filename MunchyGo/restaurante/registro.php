<?php
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_restaurante = $_POST['nombre_restaurante'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $comentarios = $_POST['comentarios'] ?? '';

    if (empty($nombre_restaurante) || empty($direccion) || empty($email) || empty($telefono)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $_SESSION['restaurante'] = [
            'nombre_restaurante' => $nombre_restaurante,
            'direccion' => $direccion,
            'email' => $email,
            'telefono' => $telefono,
            'comentarios' => $comentarios
        ];
        header("Location: confirmacion.php");
        exit;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
     <style>
    :root {
        --primary-color: #ff4d00;
        --secondary-color: #e50914;
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
        max-width: 460px;
        width: 100%;
        padding: 2.5rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    .form-control {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 0.75rem;
        margin-bottom: 1rem;
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
        border: none;
        transition: all 0.3s ease;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 77, 0, 0.3);
    }
    .form-label {
        color: var(--text-color);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .alert {
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        background-color: #f8d7da;
        color: #721c24;
        font-weight: 600;
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
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center mb-1">Registra tu Restaurante</h1>
        <p class="text-center text-muted mb-4">Únete a MunchyGo y aumenta tus ventas</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="nombre_restaurante" id="nombre_restaurante" class="form-control" placeholder="Nombre del Restaurante" required>
            </div>
            <div class="mb-3">
                <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Dirección del Restaurante" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" id="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            <div class="mb-3">
                <input type="tel" name="telefono" id="telefono" class="form-control" placeholder="Teléfono de contacto" required>
            </div>
            <div class="mb-3">
                <textarea name="comentarios" id="comentarios" class="form-control" placeholder="¿Por qué deseas trabajar con nosotros?" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-login">Registrar Restaurante</button>
        </form>
    </div>
</body>
</html>