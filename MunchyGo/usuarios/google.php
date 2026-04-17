<?php
session_start();
include "../db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$nombre = $data["nombre"] ?? "";
$correo = $data["correo"] ?? "";

if (!$correo) {
  echo json_encode(["error" => "Correo no recibido"]);
  exit;
}

$verifica = $conn->prepare("SELECT id_usuario, nombre, correo FROM Usuario WHERE correo = ?");
$verifica->bind_param("s", $correo);
$verifica->execute();
$result = $verifica->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
  $sql = "INSERT INTO Usuario (nombre, correo, contraseña) VALUES (?, ?, '')";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $nombre, $correo);
  $stmt->execute();
  $id = $stmt->insert_id;
} else {
  $id = $usuario['id_usuario'];
}

$_SESSION['usuario'] = [
  "id" => $id,
  "nombre" => $nombre,
  "correo" => $correo
];

echo json_encode(["ok" => true]);
?>