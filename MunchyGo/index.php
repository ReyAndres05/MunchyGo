<?php  
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Comida rápido a domicilio cerca de ti | MunchyGo </title>
  <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <style>
  :root {
    --color-primario: #ff4d00;
    --color-secundario: #e50914;
    --color-oscuro: #1f1f1f;
    --color-claro: #ffffff;
  }
  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: var(--color-claro);
  }
  .navbar {
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 1rem 0;
  }
  .navbar-brand {
    font-weight: 700;
    color: var(--color-primario);
    font-size: 1.5rem;
  }
  .btn-accent {
    background-color: var(--color-primario);
    color: var(--color-claro);
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .btn-accent:hover {
    background-color: var(--color-secundario);
    transform: translateY(-2px);
    color: var(--color-claro);
  }
  .hero {
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
    url('https://images.unsplash.com/photo-1561758033-d89a9ad46330?auto=format&fit=crop&w=1950&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    height: 100vh;
    display: flex;
    align-items: center;
    color: var(--color-claro);
    text-align: center;
  }
  .hero h1 {
    font-size: 4rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
  }
  .hero p {
    font-size: 1.5rem;
    margin-bottom: 2rem;
  }
  .search-box {
    max-width: 600px;
    margin: 2rem auto;
    padding: 1rem 2rem;
    border-radius: 50px;
    position: relative;
    background-color: white;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  .search-box i.bi-geo-alt-fill {
    color: var(--color-primario);
    font-size: 1.3rem;
  }
  .search-box input {
    flex: 1;
    border: none;
    outline: none;
    padding: 0.8rem 1rem;
    border-radius: 50px;
  }
  .btn-location {
    background-color: var(--color-primario);
    color: white;
    border-radius: 30px;
    padding: 0.6rem 1rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
  }
  .btn-location:hover {
    background-color: var(--color-secundario);
    color: white;
  }
  .btn-accent {
    text-decoration: none;
  }
  .join-munchygo {
    padding: 5rem 0;
    background-color: #f8f9fa;
  }
  .join-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  .join-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }
  .join-card img {
    height: 250px;
    object-fit: cover;
  }
  .join-card .card-body {
    padding: 2rem;
  }
  .promo-section {
    background: linear-gradient(45deg, var(--color-primario), var(--color-secundario));
    color: var(--color-claro);
    padding: 5rem 0;
    text-align: center;
  }
  .footer {
    background-color: var(--color-oscuro);
    color: var(--color-claro);
    padding: 4rem 0 2rem;
  }
  .footer-links h5 {
    color: var(--color-primario);
    margin-bottom: 1.5rem;
  }
  .footer-links ul {
    list-style: none;
    padding: 0;
  }
  .footer-links a {
    color: #aaa;
    text-decoration: none;
    transition: all 0.3s ease;
    display: block;
    margin-bottom: 0.8rem;
  }
  .footer-links a:hover {
    color: var(--color-claro);
    transform: translateX(5px);
  }
  .social-icons {
    margin-top: 2rem;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 1rem;
  }
  .social-icons a {
    color: var(--color-claro);
    font-size: 1.5rem;
    transition: all 0.3s ease;
  }
  .social-icons a:hover {
    color: var(--color-primario);
    transform: translateY(-3px);
  }
  .location-alert {
    position: fixed;
    top: 100px;
    right: 20px;
    background: white;
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    z-index: 9999;
    display: none;
    max-width: 350px;
    animation: slideInRight 0.4s ease;
  }
  .location-alert.show {
    display: block;
  }
  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(100px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
  .location-alert .close-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #999;
  }
  .location-alert h6 {
    color: var(--color-primario);
    margin-bottom: 1rem;
    font-weight: 700;
  }
</style>
</head>
<body>
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="fas fa-hamburger"></i> MunchyGo</a>
      <div class="ms-auto">
        <?php if (!isset($_SESSION['usuario'])): ?>
          <a href="usuarios/login.php" class="btn btn-accent">Ingresar</a>
        <?php else: ?>
          <a href="pasos/paso3.php" class="btn btn-accent">Entrar</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  
  <div class="location-alert" id="locationAlert">
    <button class="close-btn" onclick="closeLocationAlert()">&times;</button>
    <h6><i class="bi bi-geo-alt-fill me-2"></i>Tu ubicación actual</h6>
    <p><strong>Dirección:</strong> Barrancabermeja, Santander...</p>
  </div>

  <section class="hero" data-aos="fade-up">
    <div class="container">
    <h1>Tu comida favorita a domicilio</h1>
    <p class="lead">Descubre los mejores restaurantes y ofertas exclusivas a un clic de distancia</p>
    <div class="search-box" data-aos="fade-up" data-aos-delay="200"><i class="bi bi-geo-alt-fill"></i>
      <input type="text" class="form-control" placeholder="¿Dónde quieres recibir tu entrega?">
      <a href="javascript:void(0)" class="btn-location" onclick="useMyLocation()"><i class="bi bi-crosshair"></i> Usa tu ubicación</a>
    </div>
  </section>

  <section class="join-munchygo py-5">
    <div class="container">
      <h2 class="text-center mb-5" data-aos="fade-up">Únete a la familia <span style="color: var(--color-primario)">MunchyGo</span></h2>
      <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="join-card card h-100">
            <img src="https://plus.unsplash.com/premium_photo-1661954531673-440d23a6eb79?fm=jpg&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8bHV4dXJ5JTIwcmVzdGF1cmFudHxlbnwwfHwwfHx8MA%3D%3D&ixlib=rb-4.1.0&q=60&w=3000" class="card-img-top" alt="Restaurante">
            <div class="card-body">
              <h5 class="card-title fw-bold">Haz crecer tu negocio</h5>
              <p class="card-text">Aumenta tus órdenes y llega a más clientes e impulsar más ventas uniéndote a MunchyGo.</p>
              <a href="restaurante/registro.php" class="btn btn-accent w-100">Conviértete en socio</a>
            </div>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="join-card card h-100">
            <img src="https://thelogisticsworld.com/wp-content/uploads/2023/09/servicio-entrega-comida-ciclista-que-entrega-comida-clientes-bicicleta-conceptos-sobre-transporte-entrega-comida-tecnologia-828x548.jpg" class="card-img-top" alt="Repartidor">
            <div class="card-body">
              <h5 class="card-title fw-bold">Conviértete en un MunchyRider</h5>
              <p class="card-text">Genera ingresos extra siendo parte de nuestro equipo de repartidores.</p>
              <?php if (!isset($_SESSION['repartidor'])): ?>
                <a href="repartidor/registro.php" class="btn btn-accent w-100">¡Únete ahora!</a>
                <?php else: ?>
                  <a href="repartidor/panel.php" class="btn btn-accent w-100">Ingresar</a>
                  <?php endif; ?>
                </div>
            </div>
        </div>
  </section>

  <section class="promo-section" data-aos="fade-up">
    <div class="container">
      <h2 class="display-4 mb-4">GoClub ofrece entregas por menos</h2>
      <p class="lead mb-4">GoClub te da entregas por $0,5% de reembolso en pedidos para llevar y beneficios exclusivos. Pruébalo gratis por 30 días.</p>
      <a href="https://play.google.com/store/games?hl=es_US&pli=1" class="btn btn-light btn-lg">Obten GoClub</a>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-4 footer-links">
          <h5>Sobre MunchyGo</h5>
          <ul>
            <li><a href="#">Quiénes somos</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Carreras</a></li>
            <li><a href="#">Lugares de entrega</a></li>
            <li><a href="#">Privacidad</a></li>
            <li><a href="#">Accesibilidad</a></li>
            <li><a href="./restaurante/gestion_restaurantes.php">Central de MunchyGo</a></li>
          </ul>
        </div>
        <div class="col-md-4 footer-links">
          <h5>Permítenos ayudarte</h5>
          <ul>
            <li><a href="#">Información de la cuenta</a></li>
            <li><a href="#">Términos y condiciones</a></li>
            <li><a href="#">Centro de ayuda</a></li>
          </ul>
        </div>
        <div class="col-md-4 footer-links">
          <h5>Síguenos</h5>
          <div class="social-icons">
            <a href="https://www.facebook.com/"><i class="fab fa-facebook"></i></a>
            <a href="https://x.com/?lang=en"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
            <a href="https://www.linkedin.com/"><i class="fab fa-linkedin"></i></a>
          </div>
        </div>
      </div>
      <hr class="mt-4 mb-4" style="border-color: #333;">
      <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> MunchyGo. Todos los derechos reservados.</p>
    </div>
  </footer>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
    function useMyLocation() {
      const locationAlert = document.getElementById('locationAlert');
      locationAlert.classList.add('show');
      setTimeout(() => {
        locationAlert.classList.remove('show');
      }, 2000);
    }
    function closeLocationAlert() {
      document.getElementById('locationAlert').classList.remove('show');
    }
  </script>
</body>
</html>