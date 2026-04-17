<?php
session_start();
include "../db.php";
if (!isset($_SESSION['usuario'])) { 
    header("Location: ../usuarios/login.php"); 
    exit; 
}
$id_usuario = $_SESSION['usuario']['id'];
$query = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario_data = $result->fetch_assoc();

if (!$usuario_data) {
    header("Location: ../usuarios/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MunchyGo </title>
    <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #FF441F;
        --secondary-color: #FF8C42;
        --text-color: #2D3436;
        --background-color: #F8F9FA;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --sidebar-bg: #ffffff;
        --sidebar-hover: #FFF5F3;
        --sidebar-active: #FF441F;
        --sidebar-text: #6B7280;
        --sidebar-text-active: #ffffff;
    }      
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        background-color: var(--background-color);
        font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        min-height: 100vh;
        color: var(--text-color);
        transition: margin-left 0.3s ease;
    }     
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: 280px;
        background: var(--sidebar-bg);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
    }
    .sidebar.collapsed {
        width: 80px;
    }
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #E5E7EB;
    }
    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.25rem;
        text-decoration: none;
        transition: opacity 0.3s;
    }
    .sidebar.collapsed .sidebar-logo span {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }
    .sidebar-logo i {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .toggle-sidebar {
        background: #F3F4F6;
        border: none;
        color: var(--text-color);
        width: 35px;
        height: 35px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        flex-shrink: 0;
    }
    .toggle-sidebar:hover {
        background: var(--primary-color);
        color: white;
    }
    .menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.875rem 1rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        transition: all 0.3s;
        position: relative;
        cursor: pointer;
    }
    .menu-item:hover {
        background: var(--sidebar-hover);
        color: var(--primary-color);
    }
    .menu-item.active {
        background: var(--sidebar-active);
        color: var(--sidebar-text-active);
    }
    .menu-item i {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
    }
    .menu-item span {
        font-size: 0.95rem;
        font-weight: 500;
        white-space: nowrap;
        transition: opacity 0.3s;
    }
    .sidebar.collapsed .menu-item span {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }
    .sidebar.collapsed .menu-item {
        justify-content: center;
        padding: 0.875rem;
    }
    .menu-badge {
        margin-left: auto;
        background: var(--primary-color);
        color: white;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .sidebar.collapsed .menu-badge {
        display: none;
    }
    .sidebar-footer {
        padding-top: 1.5rem;
        border-top: 1px solid #E5E7EB;
    }
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.875rem 1rem;
        background: #F9FAFB;
        border-radius: 10px;
        color: var(--text-color);
    }
    .user-name {
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--text-color);
    }
    .user-email {
        font-size: 0.75rem;
        color: var(--sidebar-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sidebar.collapsed .user-details {
        display: none;
    }
    .sidebar.collapsed .user-info {
        justify-content: center;
        padding: 0.875rem;
    }
    .main-content {
        margin-left: 280px;
        padding: 2rem;
        transition: margin-left 0.3s ease;
        min-height: 100vh;
    }
    .main-content.expanded {
        margin-left: 80px;
    }
    .restaurant-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 30px var(--shadow-color);
    }       
    .restaurant-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
        margin-top: 2rem;
    }       
    .restaurant-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        background: white;
        box-shadow: 0 4px 20px var(--shadow-color);
    }       
    .restaurant-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }        
    .restaurant-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        object-fit: contain;
        transition: transform 0.3s ease;
    }       
    .restaurant-card:hover .restaurant-image {
        transform: scale(1.05);
    }       
    .restaurant-info {
        padding: 1.5rem;
        background: white;
    }       
    .restaurant-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--text-color);
    }       
    .restaurant-rating {
        color: #FFD700;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }        
    .restaurant-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }        
    .restaurant-card input[type="radio"]:checked + label {
        border: 3px solid var(--primary-color);
        box-shadow: 0 0 0 3px rgba(255, 68, 31, 0.2);
    }
    .page-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        color: var(--text-color);
    }
    .btn-action {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        min-width: 150px;
        margin: 0.5rem;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 68, 31, 0.3);
        color: white;
    }
    .fixed-buttons {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 15px;
        z-index: 999;
    }
    .modal-perfil {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        justify-content: center;
        align-items: center;
    }
    .modal-perfil.active {
        display: flex;
    }
    .modal-content-perfil {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        position: relative;
    }
    .modal-header-perfil {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }
    .modal-header-perfil h3 {
        margin: 0;
        color: var(--primary-color);
        font-size: 1.5rem;
        font-weight: 700;
    }
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #999;
        cursor: pointer;
        transition: color 0.3s;
    }
    .close-modal:hover {
        color: var(--primary-color);
    }
    .form-group-perfil {
        margin-bottom: 1.5rem;
    }
    .form-group-perfil label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-color);
    }
    .form-group-perfil input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    .form-group-perfil input:focus {
        outline: none;
        border-color: var(--primary-color);
    }
    .form-group-perfil input:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }
    .btn-guardar {
        width: 100%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 0.9rem;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-guardar:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 68, 31, 0.3);
    }
    .alert-perfil {
        padding: 0.75rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        display: none;
    }
    .alert-perfil.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-perfil.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert-perfil.active {
        display: block;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.mobile-open {
            transform: translateX(0);
        }
        .main-content {
            margin-left: 0;
        }
    }
</style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="../index.php" class="sidebar-logo"><i class="fas fa-hamburger"></i><span>MunchyGo</span></a>
            <button class="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-chevron-left" id="toggleIcon"></i>
            </button>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="menu-item" onclick="abrirModalPerfil(); return false;">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="../usuarios/historial.php" class="menu-item">
                <i class="fas fa-history"></i>
                <span>Historial</span>
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-heart"></i>
                <span>Favoritos</span>
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
            <a href="../usuarios/logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <i class="fas fa-user-circle" style="font-size: 2rem;"></i>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($usuario_data['nombre']); ?></div>
                    <div class="user-email"><?php echo htmlspecialchars($usuario_data['correo']); ?></div>
                </div>
            </div>
        </div>
    </aside>

    <div class="modal-perfil" id="modalPerfil">
        <div class="modal-content-perfil">
            <div class="modal-header-perfil">
                <h3><i class="fas fa-user-edit"></i> Mi Perfil</h3>
                <button class="close-modal" onclick="cerrarModalPerfil()">&times;</button>
            </div>
            <div class="alert-perfil" id="alertPerfil"></div>
            <form id="formPerfil">
                <div class="form-group-perfil">
                    <label><i class="fas fa-user"></i> Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario_data['nombre']); ?>">
                </div>
                
                <div class="form-group-perfil">
                    <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                    <input type="email" id="correo" value="<?php echo htmlspecialchars($usuario_data['correo']); ?>" disabled>
                </div>
                
                <div class="form-group-perfil">
                    <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario_data['direccion'] ?? ''); ?>" placeholder="Ingresa tu dirección">
                </div>
                
                <div class="form-group-perfil">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario_data['telefono'] ?? ''); ?>" placeholder="Ingresa tu teléfono">
                </div>
                
                <button type="submit" class="btn-guardar"> Guardar Cambios</button>
            </form>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="container">
            <div class="restaurant-container">
                <h2 class="page-title">Escoge un restaurante</h2>
                <form method="POST" action="paso4.php">
                    <div class="restaurant-grid">
                        <?php
                        $res = $conn->query("SELECT * FROM Restaurantes");
                        while ($row = $res->fetch_assoc()) {
                            $imagenes = [
                                1 => 'https://imgs.search.brave.com/nTUabmF8HJsKH3eFAK13ye6A4h41DjW_qMgyJ_otnXU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9mb25k/ZWJ1Y2FuZXJvLmNv/bS9pbWFnZXMvY29u/dmVuaW9zL1ppcnVz/X3BpenphLmpwZw',
                                2 => 'https://imgs.search.brave.com/zRi2gPeFF00mRe4kVC3pTJleTYfss6qyNPdCaSMtIl8/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9maWxl/cy5haXFmb21lLmNv/bS9yZXN0YXVyYW50/ZXMvYXZhdGFyLzE0/ZDQxNTdhLTZkZjEt/NGVjZS05YWRlLWVm/M2QyODE0MzA0ZS9y/ZXN0YXVyYW50ZV8y/MDI0MDUzMTA4Mzg1/NS5qcGVn',
                                3 => 'https://imgs.search.brave.com/35qv9O_YjXcgrId8eWoJ66OKIGf-xME52-oQxgeF4U4/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pLnBp/bmltZy5jb20vb3Jp/Z2luYWxzLzA1L2Y5/LzBlLzA1ZjkwZTFh/Mzk1MThmMDFhOWZk/YTIwY2U1NWY5ZGM3/LmpwZw',
                                4 => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQm_RUNHFNMdwJRFCGGaBCj1FtvIUa3OGElRA&s',
                                5 => 'https://media.istockphoto.com/id/1172163837/vector/sushi-initial-o-design-inspiration.jpg?s=612x612&w=0&k=20&c=0f9KMZ5J4ZhpWrvLQnQNrr85mBNudIsmi8rqAM8z3XU=',                                               
                            ];
                            $imagen = $imagenes[$row['id_restaurante']] ?? $imagenes[1]; 
                            echo "<div class='restaurant-card'>
                                <input type='radio' name='restaurante' id='rest_{$row['id_restaurante']}' value='{$row['id_restaurante']}' required>
                                <label for='rest_{$row['id_restaurante']}' style='display:block;'>
                                    <img src='{$imagen}' alt='{$row['nombre']}' class='restaurant-image'>
                                    <div class='restaurant-info'>
                                        <div class='restaurant-name'>" . htmlspecialchars($row['nombre']) . "</div>
                                        <div class='restaurant-rating'>
                                            <i class='fas fa-star'></i>
                                            <i class='fas fa-star'></i>
                                            <i class='fas fa-star'></i>
                                            <i class='fas fa-star'></i>
                                            <i class='far fa-star'></i>
                                        </div>
                                        <small class='text-muted'>" . htmlspecialchars($row['direccion']) . "</small>
                                    </div>
                                </label>
                            </div>";
                        }
                        ?>
                    </div>
                    <div class="fixed-buttons">
                        <button type="submit" class="btn-action">Siguiente</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleIcon = document.getElementById('toggleIcon');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        if (sidebar.classList.contains('collapsed')) {
            toggleIcon.classList.remove('fa-chevron-left');
            toggleIcon.classList.add('fa-chevron-right');
        } else {
            toggleIcon.classList.remove('fa-chevron-right');
            toggleIcon.classList.add('fa-chevron-left');
        }
    }
    function abrirModalPerfil() {
        document.getElementById('modalPerfil').classList.add('active');
    }

    function cerrarModalPerfil() {
        document.getElementById('modalPerfil').classList.remove('active');
    }
    document.getElementById('modalPerfil').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalPerfil();
        }
    });
    document.getElementById('formPerfil').addEventListener('submit', function(e) {
        e.preventDefault();
        const nombre = document.getElementById('nombre').value;
        const direccion = document.getElementById('direccion').value;
        const telefono = document.getElementById('telefono').value;
        const alertPerfil = document.getElementById('alertPerfil');
         
        fetch('actualizar_perfil.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `nombre=${encodeURIComponent(nombre)}&direccion=${encodeURIComponent(direccion)}&telefono=${encodeURIComponent(telefono)}`
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);
            
            alertPerfil.className = 'alert-perfil active ' + (data.success ? 'success' : 'error');
            alertPerfil.textContent = data.message;
            if (data.debug) {
                console.error('Debug info:', data.debug);
            }
            
            if (data.success) {
                document.querySelector('.user-name').textContent = nombre;
                
                setTimeout(() => {
                    alertPerfil.classList.remove('active');
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error en fetch:', error);
            alertPerfil.className = 'alert-perfil active error';
            alertPerfil.textContent = 'Error al actualizar el perfil: ' + error.message;
        });
    });
    </script>
</body>
</html>