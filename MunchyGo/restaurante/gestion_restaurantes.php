<?php
session_start();
include("../db.php");

$mensaje = "";
$tipo_mensaje = "success";
$editando = false;
$restaurante_editar = null;

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM restaurantes WHERE id_restaurante = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Restaurante eliminado con éxito.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar el restaurante.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $sql = "SELECT * FROM restaurantes WHERE id_restaurante = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $restaurante_editar = $result->fetch_assoc();
        $editando = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($nombre) || empty($direccion) || empty($telefono)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        $_SESSION['tipo_mensaje'] = "danger";
    } else {
        if ($id > 0) {
            $sql = "UPDATE restaurantes SET nombre=?, direccion=?, telefono=? WHERE id_restaurante=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nombre, $direccion, $telefono, $id);
            $mensaje_exito = "Restaurante actualizado con éxito.";
        } else {
            $sql = "INSERT INTO restaurantes (nombre, direccion, telefono) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nombre, $direccion, $telefono);
            $mensaje_exito = "Restaurante agregado con éxito.";
        }

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = $mensaje_exito;
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al procesar la solicitud.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
}

$restaurantes = $conn->query("SELECT * FROM restaurantes ORDER BY nombre");
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
        --bg-light: #f9f9f9;
        --input-border: #e0e0e0;
    }
    body {
        background-color: var(--bg-light);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding-top: 2rem;
    }
    .container {
        max-width: 1200px;
        margin: auto;
    }
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        background: #fff;
        margin-bottom: 2rem;
    }
    .btn-munchygo {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .btn-munchygo:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        color: white;
    }
    .btn-cancel {
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }
    .form-control {
        border: 1px solid var(--input-border);
        border-radius: 10px;
        padding: 12px;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: var(--primary-color);
    }
    .header-icon {
        font-size: 1.8rem;
        color: var(--primary-color);
    }
    .restaurant-list {
        margin-top: 2rem;
    }
    .restaurant-item {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        border: 1px solid var(--input-border);
    }
    .restaurant-item:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .back-link {
        position: fixed;
        top: 20px;
        left: 20px;
        color: black;
        text-decoration: none;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        z-index: 1000;
        padding: 10px 20px;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
    }
    .alert-editing {
        background-color: #fff3cd;
        border-color: #ffecb5;
        color: #856404;
    }
</style>
</head>
<body>
    <a href="../index.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0"><i class="fa-solid fa-utensils header-icon me-2"></i> 
                        <?= $editando ? 'Editar' : 'Agregar' ?> Restaurante
                        </h4>
                    </div>
                    
                    <?php if ($editando): ?>
                        <div class="alert alert-editing" role="alert"><i class="fa-solid fa-info-circle me-2"></i>
                            Editando: <strong><?= htmlspecialchars($restaurante_editar['nombre']) ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                            <i class="fa-solid <?= $tipo_mensaje == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i>
                            <?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <?php if ($editando): ?>
                            <input type="hidden" name="id" value="<?= $restaurante_editar['id_restaurante'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del restaurante</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-store"></i></span>
                                <input type="text" name="nombre" class="form-control" 
                                value="<?= $editando ? htmlspecialchars($restaurante_editar['nombre']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                                <input type="text" name="direccion" class="form-control" 
                                value="<?= $editando ? htmlspecialchars($restaurante_editar['direccion']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="tel" name="telefono" class="form-control" 
                                value="<?= $editando ? htmlspecialchars($restaurante_editar['telefono']) : '' ?>" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-munchygo w-100 mb-2"><i class="fa-solid <?= $editando ? '' : '' ?> me-1"></i> 
                            <?= $editando ? 'Actualizar' : 'Agregar' ?> Restaurante
                        </button>
                        <?php if ($editando): ?>
                            <a href="<?= strtok($_SERVER['REQUEST_URI'], '?') ?>" class="btn btn-cancel w-100"> Cancelar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card p-4">
                    <h4 class="mb-4"><i class="fa-solid fa-list header-icon me-2"></i> Restaurantes Registrados</h4>
                    <div class="restaurant-list">
                        <?php if ($restaurantes->num_rows > 0): ?>
                            <?php while($rest = $restaurantes->fetch_assoc()): ?>
                                <div class="restaurant-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1"><?= htmlspecialchars($rest['nombre']) ?></h5>
                                            <p class="mb-1 text-muted">
                                                <i class="fa-solid fa-location-dot me-1"></i> 
                                                <?= htmlspecialchars($rest['direccion']) ?>
                                            </p>
                                            <p class="mb-0 text-muted">
                                                <i class="fa-solid fa-phone me-1"></i> 
                                                <?= htmlspecialchars($rest['telefono']) ?>
                                            </p>
                                        </div>
                                        <div>
                                            <a href="?editar=<?= $rest['id_restaurante'] ?>" 
                                               class="btn btn-sm btn-outline-primary me-1"
                                               title="Editar restaurante">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmarEliminacion(<?= $rest['id_restaurante'] ?>, 
                                                    '<?= htmlspecialchars($rest['nombre'], ENT_QUOTES) ?>')"
                                                    title="Eliminar restaurante">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fa-solid fa-info-circle me-2"></i>No hay restaurantes registrados aún.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        function confirmarEliminacion(id, nombre) {
            if (confirm('¿Estás seguro de que deseas eliminar el restaurante "' + nombre + '"?\n\nEsta acción no se puede deshacer.')) {
                window.location.href = '?eliminar=' + id;
            }
        }
    </script>
</body>
</html>