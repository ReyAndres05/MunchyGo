<?php
session_start();
include "../db.php";
if (isset($_SESSION['usuario'])) {
    header("Location: ../pasos/paso3.php");
    exit();
}
$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['clave'])) {
    $usuario = trim($_POST['usuario']);
    $clave   = trim($_POST['clave']);
    if (is_numeric($usuario)) {
        $sql = "SELECT id_usuario, contraseña FROM Usuario WHERE telefono = ?";
    } else {
        $sql = "SELECT id_usuario, contraseña FROM Usuario WHERE correo = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows == 1) {
        $usuarioData = $result->fetch_assoc();
        if ($clave === $usuarioData['contraseña']) {
            $_SESSION['usuario'] = [
                'id' => $usuarioData['id_usuario']
            ];
            header("Location: ../pasos/paso3.php");
            exit;
        } else {
            $err = "";
        }
    } else {
        $err = "";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>MunchyGo</title> 
  <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
  <style>
  * { box-sizing: border-box; }

  body {
    margin: 0;
     font-family: 'Segoe UI', sans-serif;
    background-color: #fff;
  }
  header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    z-index: 100;
    text-align: center;
    padding: 10px 0;
  }
  header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 800;
    color: #ff4d4f;
    letter-spacing: 0.5px;
  }
  .container {
    display: flex;
    height: 100vh;
    margin-top: 0;
  }
  .left-section {
    flex: 1;
    background: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
    position: relative;
  }
  .left-section::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.55);
  }
  .left-content {
    position: relative;
    z-index: 1;
  }
  .left-content h1 {
    font-size: 42px;
    font-weight: 800;
    margin-bottom: 15px;
  }
  .left-content p {
    font-size: 20px;
    font-weight: 500;
    margin-top: 0;
  }
  .left-content small {
    display: block;
    margin-top: 15px;
    font-size: 13px;
    color: #ddd;
  }
  .right-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 40px;
    position: relative;
  }
  .right-section h2 {
    font-size: 26px;
    margin-bottom: 30px;
    color: #333;
  }
  .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 280px;
    padding: 13px;
    border-radius: 25px;
    margin: 10px 0;
    font-size: 16px;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: 0.2s;
  }
  .btn i { margin-right: 10px; font-size: 18px; }
  .btn-google { background-color: #4285F4; color: white; }
  .btn-existing {
    background-color: transparent;
    border: 2px solid #00C853;
    color: #00C853;
    transition: 0.3s;
  }
  .btn-existing:hover {
    background-color: #00C853;
    color: #fff;
  }
  .btn:hover { transform: scale(1.03); }
  .modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  .modal-content {
    background-color: #fff;
    margin: 8% auto;
    padding: 15px 25px;
    border-radius: 12px;
    width: 340px;
    position: relative;
    box-shadow: 0 5px 25px rgba(0,0,0,0.3);
    text-align: left;
    animation: slideUp 0.4s ease;
  }
  @keyframes slideUp {
    from { transform: translateY(60px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .cerrar {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 22px;
    cursor: pointer;
    color: #666;
  }
  .modal h3 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
  }
  .btn-popup {
    display: block;
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: none;
    border-radius: 30px;
    font-size: 16px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
  }
  .btn-popup i { margin-right: 8px; }
  .btn-celular {
    background-color: #00c853;
    color: white;
  }
  .btn-correo {
    background-color: #e8f5e9;
    color: #00c853;
    border: 1px solid #00c853;
  }
  .btn-popup:hover {
    transform: scale(1.03);
  }
  .loading {
    display: none;
    text-align: center;
    margin-top: 20px;
  }
  .spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .error-message {
    background: #fee;
    border: 1px solid #fcc;
    color: #c33;
    padding: 12px;
    border-radius: 8px;
    margin-top: 20px;
    display: none;
    animation: shake 0.5s;
  }
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
  }
  .error-message.show {
    display: block;
  }
</style>
</head>
<body>><header><h1>MunchyGo</h1></header>
<div class="container">
  <div class="left-section">
    <div class="left-content">
      <h1>Regístrate ya y<br>ahórrate los costos de envío</h1>
      <p>Envíos gratis durante las primeras semanas<br>pagando con tarjeta.</p>
      <small>*Válido para nuevos usuarios</small>
    </div>
  </div>

  <div class="right-section">
    <h2>Regístrate o ingresa para continuar</h2>
    <button id="loginButton" class="btn btn-google"><i class="bi bi-google"></i> Continúa con Google</button>
    <div class="loading" id="loading">
      <div class="spinner"></div>
      <p style="margin-top: 10px; color: #666;">Iniciando sesión...</p>
    </div>
    <div class="error-message" id="errorMessage"></div>
    <button class="btn btn-existing" onclick="abrirModal()">Ya tengo cuenta</button>
  </div>
</div>

<div id="miModal" class="modal">
  <div class="modal-content">
    <span class="cerrar" onclick="cerrarModal()">&times;</span>
    <h2>Iniciar sesión</h2>
    <button class="btn-popup btn-celular" onclick="window.location.href='login.php'"><i class="bi bi-telephone-fill"></i> Continuar con tu teléfono</button>
    <button class="btn-popup btn-correo" onclick="window.location.href='login.php'"><i class="bi bi-envelope-fill"></i> Continuar con tu correo</button>
  </div>
</div>

<script>
  function abrirModal() {
    document.getElementById("miModal").style.display = "block";
  }
  function cerrarModal() {
    document.getElementById("miModal").style.display = "none";
  }
  window.onclick = function(event) {
    const modal = document.getElementById("miModal");
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }

  const firebaseConfig = {
    apiKey: "AIzaSyAlRmmJPmIXu6hw7KTEFbJm6EbCETuiCFk",
    authDomain: "hola-94fbf.firebaseapp.com",
    projectId: "hola-94fbf",
    storageBucket: "hola-94fbf.firebasestorage.app",
    messagingSenderId: "403376704207",
    appId: "1:403376704207:web:3a166ec6b6589eba7585ba",
    measurementId: "G-V047DGB0DT"
  };

  firebase.initializeApp(firebaseConfig);
  const loginButton = document.getElementById('loginButton');
  const errorMessage = document.getElementById('errorMessage');
  const loading = document.getElementById('loading');

  loginButton.addEventListener('click', () => {
    loginButton.style.display = 'none';
    loading.style.display = 'block';
    errorMessage.classList.remove('show');

    const provider = new firebase.auth.GoogleAuthProvider();
    firebase.auth().signInWithPopup(provider)
      .then(result => {
        const user = result.user;
        fetch("google.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            nombre: user.displayName,
            correo: user.email
          })
        })
        .then(res => res.json())
        .then(data => {
          if (data.ok) {
            window.location.href = "../pasos/paso3.php";
          } else {
            loading.style.display = 'none';
            loginButton.style.display = 'flex';
            errorMessage.textContent = "Error al iniciar sesión: " + (data.error || "Desconocido");
            errorMessage.classList.add('show');
          }
        })
        .catch(err => {
          loading.style.display = 'none';
          loginButton.style.display = 'flex';
          errorMessage.textContent = "Error al iniciar sesión";
          errorMessage.classList.add('show');
        });
      })
      .catch(error => {
        loading.style.display = 'none';
        loginButton.style.display = 'flex';
        errorMessage.textContent = "Error al iniciar sesión";
        errorMessage.classList.add('show');
      });
  });
</script>
</body>
</html>