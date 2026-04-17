<?php
session_start();
include "../db.php";
if (!isset($_SESSION['usuario'])) {
  header("Location: ../usuarios/login.php"); exit;
}

$usuarioId = $_SESSION['usuario']['id'];

$sql = "SELECT p.id_pedido, p.fecha_creacion, p.estado, p.fecha_entrega_estimada, p.fecha_entrega_real, r.direccion, r.nombre AS restaurante
        FROM Pedidos p
        JOIN Detalle_Pedido dp ON dp.id_pedido = p.id_pedido
        JOIN Comida c ON c.id_comida = dp.id_comida
        JOIN Restaurantes r ON r.id_restaurante = c.id_restaurante
        WHERE p.id_usuario = ?
        GROUP BY p.id_pedido
        ORDER BY p.fecha_creacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MunchyGo</title>
    <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #ff4d00;
        --secondary-color: #e50914;
        --background-color: #f8f9fa;
        --white: #ffffff;
    }
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: var(--background-color);
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;     
    }
    .order-card {
        background: var(--white);
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        transition: transform 0.3s ease;
        border-top: 1px solid #f0f0f0;
        margin-top: 1rem;
        padding-top: 1rem;
    }
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    .order-number {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    .order-status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .order-details {
        margin-bottom: 15px;
    }
    .order-details p {
        margin: 5px 0;
        color: var(--text-color);
    }
    .order-items {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
    }
    .order-total {
        font-weight: 600;
        color: var(--primary-color);
        text-align: right;
        margin-top: 10px;
    }
    .estado-badge {
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .estado-pendiente { background: #e3f2fd; color: #0d47a1; }
    .estado-preparacion { background: #fff3cd; color: #856404; }
    .estado-camino { background: #d4edda; color: #155724; }
    .estado-entregado { background: #c3e6cb; color: #1e7e34; }
</style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-1">Tu Historial de Pedidos</h1>
        <p class="text-center mb-4">Revisa tus pedidos anteriores y repite tus favoritos.</p>
        <?php if ($result->num_rows === 0): ?>
            <div class="text-center p-5 bg-white rounded shadow-sm"><i class="fas fa-receipt fa-3x mb-3 text-secondary"></i>
            <p class="mb-3 fs-5">Aún no has realizado ningún pedido.</p>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): 
                $pedidoId = $row['id_pedido'];
                $sqlDetalle = "SELECT c.nombre, dp.cantidad, dp.subtotal 
                FROM Detalle_Pedido dp 
                JOIN Comida c ON c.id_comida = dp.id_comida 
                WHERE dp.id_pedido = ?";
                $stmtDetalle = $conn->prepare($sqlDetalle);
                $stmtDetalle->bind_param("i", $pedidoId);
                $stmtDetalle->execute();
                $resDetalle = $stmtDetalle->get_result();
                $total = 0;
                
                $estado = strtolower($row['estado']);
                $estado_clase = match($estado) {
                    'pendiente' => 'estado-pendiente',
                    'en preparación' => 'estado-preparacion',
                    'en camino' => 'estado-camino',
                    'entregado' => 'estado-entregado',
                    default => ''
                };
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-number">Pedido #<?= $pedidoId ?></span>
                        <span class="order-status estado-badge <?= $estado_clase ?>"><?= htmlspecialchars($row['estado']) ?></span>
                    </div>
                    <div class="order-details">
                        <p><strong>Restaurante:</strong> <?= htmlspecialchars($row['restaurante']) ?></p>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($row['direccion']) ?></p>
                        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></p>
                    </div>
                    <div class="order-items">
                        <h6 class="mb-3">Productos:</h6>
                        <?php while ($detalle = $resDetalle->fetch_assoc()): 
                            $total += $detalle['subtotal'];
                        ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= (int)$detalle['cantidad'] ?> x <?= htmlspecialchars($detalle['nombre']) ?></span>
                            <span>$<?= number_format($detalle['subtotal'], 0) ?></span>
                        </div>
                        <?php endwhile; ?>
                        <div class="order-total">
                            Total: $<?= number_format($total, 0) ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>