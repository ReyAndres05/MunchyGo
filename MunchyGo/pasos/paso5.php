<?php
session_start();
include "../db.php";

if (!isset($_POST['platos']) || !isset($_SESSION['usuario'])) { 
    header("Location: paso4.php"); 
    exit; 
}

$platos = $_POST['platos'];
$usuarioId = $_SESSION['usuario']['id'];
$restId = $_SESSION['restaurante_id'];
$result = $conn->query("SELECT 1 FROM usuario WHERE id_usuario = $usuarioId");
if ($result->num_rows === 0) {
    die("Error: El usuario no existe en la base de datos.");
}
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
    margin: 0;
    padding: 1rem 1.5rem 2rem;
    color: var(--text);
  }
  .form-box {
    max-width: 700px;
    background: white;
    margin: auto;
    padding: 1.5rem 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }
  h2 {
    font-weight: 700;
    text-align: center;
    color: var(--text);
    margin-bottom: 1.5rem;
    font-size: 2rem;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
  }
  thead th {
    border-bottom: 2px solid var(--primary);
    padding-bottom: 10px;
    font-weight: 700;
    color: var(--primary);
    text-align: left;
  }
  tbody td {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
  }
  tfoot td {
    font-weight: 700;
    font-size: 1.2rem;
    color: var(--primary);
    padding-top: 12px;
    text-align: right;
  }
  h3 {
    margin-bottom: 0.8rem;
    font-weight: 600;
    color: var(--text);
  }
  label {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    display: block;
    cursor: pointer;
  }
  input[type="radio"] {
    margin-right: 8px;
    cursor: pointer;
  }
  button[type="submit"] {
    background: var(--primary);
    border: none;
    color: white;
    padding: 0.7rem 2rem;
    font-size: 1.1rem;
    border-radius: 30px;
    font-weight: 700;
    cursor: pointer;
    display: block;
    margin-top: 1rem;
    transition: background-color 0.3s ease;
    width: 100%;
    max-width: 300px;
  }
  button[type="submit"]:hover {
    background: var(--secondary);
  }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Resumen de pedido</h2>
    <table>
      <thead>
        <tr>
          <th>Plato</th>
          <th>Cantidad</th>
          <th>Precio Unitario</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $total = 0;
      foreach ($platos as $p) {
          list($id_comida, $precio) = explode('|', $p);
          $cantidad = 1;
          $subtotal = $precio * $cantidad;
          $total += $subtotal;
          
          $resComida = $conn->query("SELECT nombre FROM Comida WHERE id_comida=$id_comida");
          $nombreComida = $resComida->fetch_assoc()['nombre'] ?? "Plato";

          echo "<tr>
          <td>" . htmlspecialchars($nombreComida) . "</td>
          <td>$cantidad</td>
          <td>\$$precio</td>
          <td>\$$subtotal</td></tr>";
          echo "<input type='hidden' name='platos[]' value='$id_comida|$precio'>";
        }
        
        $_SESSION['total_pedido'] = $total;
      ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3">Total:</td>
          <td><strong>$<?= $total ?></strong></td>
        </tr>
      </tfoot>
    </table>

    <h3>Método de pago:</h3>
    <form method="POST" action="paso6.php">
      <?php
      foreach ($platos as $p) {
          echo "<input type='hidden' name='platos[]' value='" . htmlspecialchars($p) . "'>";
      }
      ?>
      <label><input type="radio" name="metodo_pago" value="tarjeta" required /> Tarjeta</label>
      <label><input type="radio" name="metodo_pago" value="efectivo" /> Efectivo</label>
      <button type="submit">Confirmar pedido</button>
    </form>
  </div>
</body>
</html>