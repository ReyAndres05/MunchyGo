<?php
session_start();
include "../db.php";

$usuarioId = $_SESSION['usuario']['id'] ?? null;
$id_restaurante = $_SESSION['restaurante_id'] ?? null;
$id_repartidor = $_SESSION['repartidor_id'] ?? null;

$gracias = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['puntuacion'])) {
    $puntuacion = (int)$_POST['puntuacion'];
    $comentario = $_POST['comentario'] ?? '';

    if (!$usuarioId || !$id_restaurante || !$id_repartidor) {
        $error = "Faltan datos para guardar la calificación.";
    } else {
        $sql = "INSERT INTO Calificacion (id_usuario, id_restaurante, id_repartidor, puntuacion, comentario) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiis", $usuarioId, $id_restaurante, $id_repartidor, $puntuacion, $comentario);

        if ($stmt->execute()) {
            $gracias = true;
        } else {
            $error = $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>MunchyGo </title>
  <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
  <style>
  body {
    background-color: #fff9f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 2rem;
  }
  .form-box {
    max-width: 480px;
    margin: 0 auto;
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }
  .link-back {
    display: inline-block;
    margin-bottom: 1rem;
    color: #ff4d00;
    font-weight: 600;
    text-decoration: none;
  }
  label {
    font-weight: 600;
    margin-bottom: 0.3rem;
    display: block;
  }
  input[type=number], textarea {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    resize: vertical;
  }
  button {
    background-color: #ff4d00;
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    width: 100%;
    font-size: 1.1rem;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #e04400;
  }
  .btn-secondary {
    background-color: #3498db;
    margin-top: 1rem;
  }
  .btn-secondary:hover {
    background-color: #2980b9;
  }
  .error-message {
    color: #d33;
    font-weight: 600;
    margin-bottom: 1rem;
  }
  .success-message {
    color: #28a745;
    font-weight: 700;
    text-align: center;
    margin-bottom: 1.5rem;
  }
</style>
</head>
<body>
  <div class="form-box">
    <?php if ($gracias): ?>
      <h2 class="success-message">¡Gracias por tu calificación!</h2>
      <form method="GET" action="paso3.php">
        <button type="submit">Volver al menú</button>
      </form>
    <?php else: ?>
      <?php if ($error): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <h2>Califica tu experiencia</h2>
      <form method="POST" novalidate>
        <label for="puntuacion">Puntuación (1 a 5):</label>
        <input type="number" id="puntuacion" name="puntuacion" min="1" max="5" required>

        <label for="comentario">Comentario (opcional):</label>
        <textarea id="comentario" name="comentario" rows="4" placeholder="Cuéntanos cómo fue tu experiencia..."></textarea>
        <button type="submit">Enviar calificación</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>