<?php
session_start();
include "../db.php";

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión.',
        'debug' => $conn->connect_error
    ]);
    exit;
}

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Id de usuario no encontrado. Por favor, inicia sesión de nuevo.'
    ]);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$id_usuario = $_SESSION['usuario']['id'];
if (empty($nombre) || empty($direccion) || empty($telefono)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son requeridos y no pueden estar vacíos.'
    ]);
    exit;
}

$query = "UPDATE usuario SET nombre = ?, direccion = ?, telefono = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al iniciar sesión.',
        'debug' => $conn->error
    ]);
    exit;
}

$stmt->bind_param("sssi", $nombre, $direccion, $telefono, $id_usuario);
if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Perfil actualizado correctamente.'
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el perfil.',
        'debug' => $stmt->error
    ]);
}
$stmt->close();
$conn->close();
?> 