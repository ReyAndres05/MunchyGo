<?php
session_start();
include "../db.php";

$restId = $_POST['restaurante'] ?? null;
if (!$restId) { header("Location: paso3.php"); exit; }
$_SESSION['restaurante_id'] = (int)$restId;

$res = $conn->query("SELECT nombre FROM Restaurantes WHERE id_restaurante=" . ((int)$_SESSION['restaurante_id']));
$restaurante = $res->fetch_assoc();
$nombreRestaurante = htmlspecialchars($restaurante['nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>MunchyGo </title>
  <link rel="icon" type="image/png" href="https://img.icons8.com/ios-filled/50/ffffff/meal.png"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
  :root {
    --primary: #ff4d00;
    --secondary: #ff8800;
    --bg: #fffaf6;
    --text: #1e1e1e;
    --button-radius: 25px;
    --card-radius: 16px;
    --shadow-color: rgba(0, 0, 0, 0.1);
  }
  body {
    background-color: var(--bg);
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding-bottom: 100px;
  }
  .menu-container {
    max-width: 1100px;
    margin: auto;
    padding: 0 1.5rem;
  }
  .menu-title {
    font-size: 2.5rem;
    text-align: center;
    margin-top: 1rem;
    color: var(--text);
    font-weight: 700;
  }
  .menu-section {
    margin-top: 3rem;
  }
  .menu-columns {
    display: flex;
    margin-top: 2rem;
    position: relative;
  }
  .menu-column {
    flex: 1;
    padding: 0 2rem;
  }
  .menu-column.comidas {
    padding-right: 3rem;
  }
  .menu-column.bebidas {
    padding-left: 3rem;
  }
  .menu-column h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 1rem;
    border-left: 6px solid var(--primary);
    padding-left: 0.6rem;
  }
  .menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
  }
  .menu-item {
    background: #fff;
    border-radius: var(--card-radius);
    overflow: hidden;
    box-shadow: 0 4px 14px var(--shadow-color);
    transition: all 0.3s ease;
    position: relative;
  }
  .menu-item:hover {
    transform: translateY(-5px);
  }
  .menu-item img {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
  .menu-info {
    padding: 1rem;
  }
  .menu-info h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--text);
  }
  .menu-info .price {
    color: var(--primary);
    font-weight: bold;
    margin-top: 0.5rem;
  }
  .menu-select {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  .menu-select.active {
    background: var(--secondary);
  }
  .total-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--primary);
    color: white;
    padding: 1rem 1.8rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
  }
  .btn-next {
    background: white;
    color: var(--primary);
    border: none;
    padding: 0.5rem 1.2rem;
    border-radius: var(--button-radius);
    font-weight: bold;
  }
  .btn-next:hover {
    background: #f5f5f5;
  }
</style>
</head>
<body>
  <div class="menu-container">
    <h2 class="menu-title">Menú de <?= $nombreRestaurante ?></h2>
    <form method="POST" action="paso5.php" id="menuForm">
      <input type="hidden" name="restaurante" value="<?= htmlspecialchars($_SESSION['restaurante_id']) ?>">
    <?php
    $imagenes = [
      //Zirus Pizza
      1 => "https://cdn.colombia.com/gastronomia/2011/08/25/pizza-margarita-3684.webp",
      2 => "https://www.gastrolabweb.com/u/fotografias/m/2021/2/9/f1280x720-8332_140007_5050.jpg",
      3 => "https://www.hola.com/horizon/landscape/e8bb41b65869-pizzacuatroquesos-adob-t.jpg  ",
      4 => "https://cdn.unotv.com/images/2024/09/pizza-pepperoni-no-existe-italia-152140-1024x576.jpeg",
      5 => "https://www.thursdaynightpizza.com/wp-content/uploads/2022/06/veggie-pizza-side-view-out-of-oven-720x480.png",
      6 => "https://www.shutterstock.com/image-photo/poznan-pol-aug-13-2019-600nw-2464044797.jpg",
      7 => "https://image.tuasaude.com/media/article/go/jh/suco-de-laranja_67324.jpg",
      8 => "https://cdn0.celebritax.com/sites/default/files/styles/watermark_100/public/recetas/limonada.jpg",

      //Bufalo Senta´o Burge´r
      9 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQIypg93X5zaKzsbKhtkcwO_dC5uaztjBCgigl1ztmemkzqoxRE8JSVDtEdUD6vp_pLg4A&usqp=CAU  ",
      10 => "https://elsartencaliente.com/wp-content/uploads/2023/07/Imagen-Crispy-BBQ-Bacon.jpeg.png",
      11 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR5QHEtzY8kLdRaTVhrv4W3kX_iY8uwurUpFA&s",
      12 => "https://www.repan.co/wp-content/uploads/2018/03/Hamburguesa-clasica.jpg",
      13 => "https://www.saborvenezolanokendall.com/cdn/shop/products/HAMBURGESASUPERESPECIAL1-min_2400x.jpg?v=1612293575",
      14 => "https://www.shutterstock.com/image-photo/poznan-pol-aug-13-2019-600nw-2464044797.jpg",
      15 => "https://images.cookforyourlife.org/wp-content/uploads/2018/08/Fresh-Grape-Juice-1.jpg",
      16 => "https://7diasdesabor.com/wp-content/uploads/2023/08/FOTOS_WEB-BEBIDA.jpg",

      //Free Dog´s
      17 => "https://media-cdn.tripadvisor.com/media/photo-m/1280/1b/6c/70/44/colombiano-tradicional.jpg",
      18 => "https://www.azucardominomex.com/sites/azucardominomex_com/files/2022-09/600x336_HotdogMermeladaTocino.jpg",
      19 => "https://bsstatic2.mrjack.es/wp-content/uploads/2018/09/salchicha-vegana-6-980x1200.jpg",
      20 => "https://static01.nyt.com/images/2019/05/21/dining/kwr-mexican-hot-dogs/kwr-mexican-hot-dogs-videoSixteenByNineJumbo1600.jpg",
      21 => "https://www.vvsupremo.com/wp-content/uploads/2016/02/900X570_Mexican-Style-Hot-Dogs.jpg",
      22 => "https://www.shutterstock.com/image-photo/poznan-pol-aug-13-2019-600nw-2464044797.jpg",
      23 => "https://cdn0.celebritax.com/sites/default/files/styles/watermark_100/public/recetas/limonada.jpg",
      24 => "https://lasalchipaperia.com.co/wp-content/uploads/2021/05/jugo-mora.jpg",

      //Frisby
      25 => "http://wsres.vensis.com.co/web/product/Pollo-Apanado-Pollo.png",
      26 => "https://ariztia.com/wp-content/uploads/2023/11/pollo-a-la-mostaza-.jpg",
      27 => "https://tofuu.getjusto.com/orioneat-local/resized2/pQ32sRzYpCMeE2mCw-2400-x.webp",
      28 => "https://img0.didiglobal.com/static/soda_public/img_cdce90746003c481cf1106b6dac1c9b2.png",
      29 => "http://wsres.vensis.com.co/web/product/Pollo-Apanado-Familiar.png",
      30 => "https://www.shutterstock.com/image-photo/poznan-pol-aug-13-2019-600nw-2464044797.jpg",
      31 => "https://image.tuasaude.com/media/article/go/jh/suco-de-laranja_67324.jpg",
      32 => "https://cdn0.celebritax.com/sites/default/files/styles/watermark_100/public/recetas/limonada.jpg",

      //Restaurante Grand Shanghai
      33 => "https://i.blogs.es/8424ac/arroz-frito-chino/1200_900.jpg",
      34 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ4rW377VjXohuFrJeRGlJG0DUmpKK1gEiJtQ&s",
      35 => "https://www.orientalmarket.es/recetas/wp-content/uploads/2022/09/receta-foto-chop-suey-de-verduras.jpg",
      36 => "https://upload.wikimedia.org/wikipedia/commons/0/0c/%E5%85%B0%E5%B7%9E%E7%89%9B%E8%82%89%E9%9D%A2.jpg",
      37 => "https://chefeel.com/chefgeneralfiles/2022/01/patopekin-scaled-880x683.jpg",
      38 => "https://www.shutterstock.com/image-photo/poznan-pol-aug-13-2019-600nw-2464044797.jpg",
      39 => "https://image.tuasaude.com/media/article/go/jh/suco-de-laranja_67324.jpg",
      40 => "https://cdn0.celebritax.com/sites/default/files/styles/watermark_100/public/recetas/limonada.jpg",
    ];

    $comidas = [];
    $bebidas = [];
    $res = $conn->query("SELECT * FROM Comida WHERE id_restaurante=" . ((int)$_SESSION['restaurante_id']));
    while ($row = $res->fetch_assoc()) {
      if ((int)$row['id_categoria'] === 1) {
        $comidas[] = $row;
      } elseif ((int)$row['id_categoria'] === 2) {
        $bebidas[] = $row;
      }
    }
    ?>
    <div class="menu-columns">
      <div class="menu-column comidas">
        <h3>🍴 PLatos</h3>
        <div class="menu-grid">
          <?php foreach ($comidas as $row): 
          $id = (int)$row['id_comida'];
          $precio = number_format($row['precio'], 0, '.', '');
          $img = $imagenes[$id] ?? "";
          ?>
          <div class='menu-item'>
            <img src='<?= $img ?>' alt='Plato <?= $id ?>'>
            <button type='button' class='menu-select' data-id='<?= $id ?>' data-precio='<?= $precio ?>'><i class='fas fa-plus'></i></button>
            <div class='menu-info'>
              <h5><?= htmlspecialchars($row['nombre']) ?></h5>
              <div class='price'>$<?= $precio ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      
      <div class="menu-column bebidas">
        <h3>🥤 Bebidas</h3>
        <div class="menu-grid">
          <?php foreach ($bebidas as $row): 
          $id = (int)$row['id_comida'];
          $precio = number_format($row['precio'], 0, '.', '');
          $img = $imagenes[$id] ?? "";
          ?>
          <div class='menu-item'>
            <img src='<?= $img ?>' alt='Bebida <?= $id ?>'>
            <button type='button' class='menu-select' data-id='<?= $id ?>' data-precio='<?= $precio ?>'><i class='fas fa-plus'></i></button>
            <div class='menu-info'>
              <h5><?= htmlspecialchars($row['nombre']) ?></h5>
              <div class='price'>$<?= $precio ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    
    <div class="total-bar">
      <span id="total">Total actual: $0</span>
      <button type="submit" class="btn-next">Siguiente</button>
    </div>
  </form>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const selects = document.querySelectorAll(".menu-select");
  const totalText = document.getElementById("total");
  const form = document.getElementById("menuForm");
  
  let seleccionados = [];
  let total = 0;
  selects.forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const precio = parseFloat(btn.dataset.precio);
      const index = seleccionados.findIndex(p => p.id === id);
      
      if (index === -1) {
        seleccionados.push({ id, precio });
        btn.classList.add("active");
        btn.innerHTML = '<i class="fas fa-check"></i>';
      } else {
        seleccionados.splice(index, 1);
        btn.classList.remove("active");
        btn.innerHTML = '<i class="fas fa-plus"></i>';
      }
      total = seleccionados.reduce((sum, item) => sum + item.precio, 0);
      totalText.textContent = "Total actual: $" + total;
      form.querySelectorAll("input[name='platos[]']").forEach(e => e.remove());
      seleccionados.forEach(item => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "platos[]";
        input.value = item.id + "|" + item.precio;
        form.appendChild(input);
      });
    });
  });
});
</script>
</body>
</html>