<?php
session_start();
include "../db.php";

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  $correo = $_POST['correo'];
  $id_restaurante = $_POST['id_restaurante'];
  
  if (empty($nombre) || empty($correo) || empty($id_restaurante)) {
    $mensaje = "Todos los campos son obligatorios. Por favor, completa todos los campos.";
} else {

    $verifica = $conn->prepare("SELECT id_repartidor FROM repartidor WHERE correo = ?");
    $verifica->bind_param("s", $correo);
    $verifica->execute();
    $verifica->store_result();
    
    if ($verifica->num_rows > 0) {
      $mensaje = "Este correo ya está registrado.";
    } else {
      $sql = "INSERT INTO repartidor (nombre, correo, id_restaurante)
              VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssi", $nombre, $correo, $id_restaurante);

      if ($stmt->execute()) {
        $mensaje = "Tu solicitud ha sido recibida y está en proceso de revisión. Pronto recibirás un correo Electronico a tu cuenta
        con la confirmación de tu registro o los siguientes pasos, según si cumples con los requisitos establecidos.";
      } else {
        $mensaje = "Completa todos los campos requeridos." . $conn->error;
      }
    }
  }
}

$res = $conn->query("SELECT id_restaurante, nombre FROM Restaurantes");
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
    .registration-container {
        max-width: 460px;
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
    .btn-register {
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
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 77, 0, 0.3);
    }
    .login-link {
        text-align: center;
        margin-top: 1rem;
        color: var(--text-color);
    }
    .login-link a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }
    .login-link a:hover {
        color: var(--secondary-color);
    }
    .subtle-text {
        font-size: 12px;
        color: rgba(0, 0, 0, 0.5);
    }
    .alert {
        text-align: center;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }
</style>
</head>
<body>
    <div class="registration-container">
        <h1 class="text-center mb-1">Únete a nuestro equipo</h1>
        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo (strpos($mensaje, 'error') === false) ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        <p class="text-center text-muted mb-4">Regístrate como repartidor</p>

        <form method="POST">
            <div class="form-floating">
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                <label for="nombre">Nombre completo</label>
            </div>
            <div class="form-floating">
                <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo" required>
                <label for="correo">Correo electrónico</label>
            </div>
            <div class="form-floating">
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required onchange="validarEdad()">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const today = new Date();
                    const day = String(today.getDate()).padStart(2, '0');
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    document.getElementById('fecha_nacimiento').setAttribute('max', maxDate);
                });
                
                function validarEdad() {
                    const fecha = document.getElementById('fecha_nacimiento').value;
                    if (!fecha) return false;
                    const hoy = new Date();
                    const cumple = new Date(fecha);
                    const edad = hoy.getFullYear() - cumple.getFullYear();
                    const mes = hoy.getMonth() - cumple.getMonth();
                    if (mes < 0 || (mes === 0 && hoy.getDate() < cumple.getDate())) {
                        edad--;
                    }
                    if (edad < 18) {
                        alert("Debes de tener mas de 18 para trabajar con nosotros");
                        return false;
                    }
                    return true;
                }
                </script>
            </div>
            <div class="form-floating">
                <input type="file" class="form-control" id="documento" name="documento" accept=".pdf,.jpg,.jpeg,.png" required>
                <label for="documento">Documento de identidad</label>
                <small class="subtle-text">Acepta PDF, JPG, JPEG, PNG.</small>
            </div>
            <div class="form-floating">
                <select class="form-control" id="id_restaurante" name="id_restaurante" required>
                    <option value="">Selecciona un restaurante</option>
                    <?php while($r = $res->fetch_assoc()): ?>
                        <option value="<?= $r['id_restaurante'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
                <label for="id_restaurante">Restaurante</label>
            </div>
            <button type="submit" class="btn btn-register">Solicitar Cuenta</button>
        </form>
        <p class="login-link">¿Ya eres repartidor? <a href="login.php">Inicia sesión</a></p>
    </div>
</div>
</body>
</html>