<?php
session_start();
include "../db.php";
if (!isset($_SESSION['repartidor']) || !isset($_SESSION['repartidor']['id'])) {
    header("Location: login.php"); exit;
}
$id_repartidor = $_SESSION['repartidor']['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'], $_POST['estado'])) {
    $id_pedido = (int)$_POST['id_pedido'];
    $estado = trim($_POST['estado']);
    
    try {
        if ($estado == 'entregado') {
            $stmt = $conn->prepare("UPDATE Pedidos SET estado=?, fecha_entrega_real=NOW() WHERE id_pedido=? AND id_repartidor=?");
        } else {
            $stmt = $conn->prepare("UPDATE Pedidos SET estado=? WHERE id_pedido=? AND id_repartidor=?");
        }
        $stmt->bind_param("sii", $estado, $id_pedido, $id_repartidor);
        $stmt->execute();
    } catch (Exception $e) {
        $error = "Error al actualizar el pedido: " . $e->getMessage();
    }
}
$sql = "SELECT p.id_pedido, u.nombre AS cliente, u.direccion, p.estado, p.fecha_creacion, pa.metodo_pago, r.nombre AS restaurante,
        CASE WHEN pa.metodo_pago = 'efectivo' THEN SUM(dp.subtotal) ELSE 0 END as total_pagar
        FROM Pedidos p
        INNER JOIN Usuario u ON p.id_usuario = u.id_usuario
        LEFT JOIN Pago pa ON p.id_pedido = pa.id_pedido 
        LEFT JOIN Detalle_Pedido dp ON p.id_pedido = dp.id_pedido
        LEFT JOIN Comida c ON dp.id_comida = c.id_comida
        LEFT JOIN Restaurantes r ON c.id_restaurante = r.id_restaurante
        WHERE p.id_repartidor = ? AND c.id_restaurante = (SELECT id_restaurante FROM repartidor WHERE id_repartidor = ?)
        GROUP BY p.id_pedido
        ORDER BY p.fecha_creacion DESC";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_repartidor, $id_repartidor);
        $stmt->execute();
        $pedidos = $stmt->get_result();
    } catch (Exception $e) {
        die("Error al obtener pedidos: " . $e->getMessage());
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
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
        background-color: var(--background-color);
        min-height: 100vh;
        padding-top: 20px;
    }
    .navbar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 1rem 0;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
    .navbar-brand {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.5rem;
        text-decoration: none;
    }
    .pedido-card {
        background: var(--white);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .pedido-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }
    .pedido-id {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    .pedido-info {
         margin: 1rem 0;
    }
    .pedido-info p {
        margin: 0.5rem 0;
        color: var(--text-color);
    }
    .estado-select {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-right: 1rem;
        transition: all 0.3s ease;
    }
    .estado-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(255, 77, 0, 0.15);
    }
    .btn-actualizar {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        color: var(--white);
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-actualizar:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 77, 0, 0.3);
    }
    .logout-btn {
        background: transparent;
        color: var(--text-color);
        border: 1px solid #ddd;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    .logout-btn:hover {
        background: #f1f1f1;
        border-color: #ccc;
        color: var(--text-color);
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
    <nav class="navbar">
        <div class="container">
            <a href="../index.php" class="navbar-brand"><i class="fas fa-motorcycle"></i> MunchyGo Delivery</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>
    </nav>
    <div class="container" style="margin-top: 80px;">
        <h2 class="mb-4">Panel de Control - Repartidor</h2>
        <?php while($p = $pedidos->fetch_assoc()): 
            $estado_clase = match($p['estado']) {
                'pendiente' => 'estado-pendiente',
                'en preparación' => 'estado-preparacion',
                'en camino' => 'estado-camino',
                'entregado' => 'estado-entregado',
                default => ''
            };
        ?>
        <div class="pedido-card">
            <div class="pedido-header">
                <span class="pedido-id">Pedido #<?= $p['id_pedido'] ?></span>
                <span class="estado-badge <?= $estado_clase ?>"><?= htmlspecialchars($p['estado']) ?></span>
            </div>
            <div class="pedido-info">
                <p><i class="fas fa-store me-2"></i> <strong>Restaurante:</strong> <?= htmlspecialchars($p['restaurante']) ?></p>
                <p><i class="fas fa-user me-2"></i> <strong>Cliente:</strong> <?= htmlspecialchars($p['cliente']) ?></p>
                <p><i class="fas fa-map-marker-alt me-2"></i> <strong>Dirección:</strong> <?= htmlspecialchars($p['direccion']) ?></p>
                <p><i class="fas fa-credit-card me-2"></i> <strong>Método de pago:</strong> <?= htmlspecialchars($p['metodo_pago'] ?? 'No especificado') ?></p>
                <?php if($p['metodo_pago'] == 'efectivo'): ?>
                    <p><i class="fas fa-money-bill me-2"></i> <strong>Total a cobrar:</strong> $<?= number_format($p['total_pagar'], 0) ?></p>
                <?php endif; ?>
            </div>
            
            <form method="POST" class="d-flex align-items-center">
                <input type="hidden" name="id_pedido" value="<?= $p['id_pedido'] ?>">
                <select name="estado" class="estado-select flex-grow-1">
                    <option value="pendiente" <?= $p['estado']=="pendiente"?"selected":"" ?>>Pendiente</option>
                    <option value="en preparación" <?= $p['estado']=="en preparación"?"selected":"" ?>>En preparación</option>
                    <option value="en camino" <?= $p['estado']=="en camino"?"selected":"" ?>>En camino</option>
                    <option value="entregado" <?= $p['estado']=="entregado"?"selected":"" ?>>Entregado</option>
                </select>
                <button type="submit" class="btn-actualizar"><i class="fas fa-sync-alt me-2"></i>Actualizar Estado</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>