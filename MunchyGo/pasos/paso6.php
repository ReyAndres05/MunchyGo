<?php
session_start();
include "../db.php";

if (!isset($_POST['platos']) || !isset($_POST['metodo_pago']) || !isset($_SESSION['usuario'])) {
    header("Location: paso4.php");
    exit;
}
$usuarioId = $_SESSION['usuario']['id'];
$restId = $_SESSION['restaurante_id'] ?? null;
$platos = $_POST['platos'];
$metodo_pago = $_POST['metodo_pago'];
$fecha_creacion = date('Y-m-d H:i:s');
$fecha_estimada = date('Y-m-d H:i:s', strtotime('+30 minutes'));
$estado = "Su orden fue tomada";
$total = 0;

$res = $conn->query("SELECT id_repartidor FROM Repartidor ORDER BY RAND() LIMIT 1");
$row = $res->fetch_assoc();
$id_repartidor = $row['id_repartidor'] ?? null;
if ($id_repartidor === null) {
    die("Error: No hay repartidores disponibles.");
}

$sql_pedido = "INSERT INTO Pedidos (id_usuario, id_repartidor, fecha_creacion, fecha_entrega_estimada, estado) 
               VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_pedido);
$stmt->bind_param("iisss", $usuarioId, $id_repartidor, $fecha_creacion, $fecha_estimada, $estado);
$stmt->execute();
$pedidoId = $conn->insert_id;
foreach ($platos as $p) {
    list($id_comida, $precio) = explode('|', $p);
    $cantidad = 1;
    $subtotal = $precio * $cantidad;
    $total += $subtotal;
    $conn->query("INSERT INTO Detalle_Pedido (id_pedido, id_comida, cantidad, subtotal)
                  VALUES ($pedidoId, $id_comida, $cantidad, $subtotal)");
}

$_SESSION['pedido_id'] = $pedidoId;
$_SESSION['repartidor_id'] = $id_repartidor;
$_SESSION['total_pedido'] = $total;
$fecha_pago = date('Y-m-d H:i:s');
$sql_pago = "INSERT INTO Pago (id_pedido, metodo_pago, fecha_pago) VALUES (?, ?, ?)";
$stmt_pago = $conn->prepare($sql_pago);
$stmt_pago->bind_param("iss", $pedidoId, $metodo_pago, $fecha_pago);
$stmt_pago->execute();
$nombreRepartidor = "";
$res_rep = $conn->query("SELECT nombre FROM Repartidor WHERE id_repartidor = $id_repartidor");
if ($res_rep && $res_rep->num_rows > 0) {
    $nombreRepartidor = $res_rep->fetch_assoc()['nombre'];
}
$estadoPedido = $estado;
$mensajePago = ($metodo_pago == "tarjeta") 
    ? "<p class='text-success fw-bold'>Tu pago ha sido procesado</p>"
    : "<p class='text-warning fw-bold'>Debes pagar en efectivo al recibir tu pedido</p>";
$detallePago = ($metodo_pago == "efectivo") 
    ? "<p><strong>Total: \$$total</strong></p>" 
    : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>MunchyGo</title>
  <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
  <style>
  :root {
    --primary: #ff4d00;
    --secondary: #ff8800;
    --bg: #fff9f5;
    --text: #1e1e1e;
  }
  body {
    background-color: var(--bg);
    font-family: 'Segoe UI', sans-serif;
    padding: 2rem 1rem;
    color: var(--text);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
  }
  .confirm-box {
    background: white;
    padding: 2rem 3rem;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-width: 500px;
    text-align: center;
  }
  h2 {
    color: var(--primary);
    font-weight: 700;
    margin-bottom: 1rem;
    font-size: 2rem;
  }
  p {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
  }
  .btn-group {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .btn {
    background-color: var(--primary);
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
    min-width: 140px;
  }
  .btn:hover {
    background-color: var(--secondary);
    color: white;
  }
  </style>
</head>
<body>
  <div class="confirm-box">
    <h2><i class="fas fa-check-circle"></i> ¡Pedido confirmado!</h2>
    <p>Repartidor: <strong><?= htmlspecialchars($nombreRepartidor) ?></strong></p>
    <p>Seguimiento: <strong><?= htmlspecialchars($estadoPedido) ?></strong></p>
    <?= $mensajePago ?>
    <?= $detallePago ?>
    <p>Tiempo estimado: <strong>30 minutos</strong></p>

    <div class="btn-group">
      <form method="GET" action="paso7.php">
        <input type="hidden" name="paso" value="7" />
        <button type="submit" class="btn">Calificar experiencia</button>
      </form>
      <form method="GET" action="paso3.php">
        <button type="submit" class="btn">Nuevo pedido</button>
      </form>
    </div>
  </div>
</body>
</html>