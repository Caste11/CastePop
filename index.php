<?php 
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/Anuncio.php';
require_once 'modelos/AnuncioDAO.php';
require_once 'modelos/funciones.php';
require_once 'modelos/config.php';

$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_BD);
$conn = $connexionDB->getConnexion();


//Si existe la cookie y no ha iniciado sesión, le iniciamos sesión de forma automática
if( !isset($_SESSION['email']) && isset($_COOKIE['sid'])){
    //Nos conectamos para obtener el id 
    $usuariosDAO = new UsuariosDAO($conn);
    //cogemos la coockie
    if($usuario = $usuariosDAO->getBySid($_COOKIE['sid'])){
        //Inicio sesión
        $_SESSION['email']=$usuario->getEmail();
        $_SESSION['id']=$usuario->getId();
    }
    
}

//Instanciamos AnuncioDAO para poder utilizarlo
$anuncioDAO = new AnuncioDAO($conn);

//Esto es para el buscador
if (isset($_GET['titulo'])) {
    $palabraBuscar = htmlspecialchars($_GET["titulo"]);
    $anuncios = $anuncioDAO->getTituloAnuncio($palabraBuscar); 
}else{
    $anuncios = $anuncioDAO->getAll();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CastePop</title>
    <link rel="stylesheet" href="Style/estilos.css">
</head>

<body>
    <header>
     <h1>CasteP <img class="logo" src="img/logo.png" alt=""> p</h1>

    <!--Este formulario es para el buscador -->
     <form action="buscarAnuncio.php" method="get">

        <input type="search" class="buscador" name="titulo">
        <button type="submit" name="buscador" class="botonbuscar">Buscar</button>

     </form>
        <nav>
            <ul>
                <li><a href="index.php">Anuncios</a></li>
                <li><a href="misAnuncios.php">Mis Anuncios</a></li>
                <li><a href="login.php">Login</a></li>

                <!-- Cuando inicias sesión te muestra el crear un anuncio -->
                <?php if (isset($_SESSION['email'])):?>
                    <li><a href="nuevoAnuncio.php">Insertar Anuncio</a></li>
                <?php endif;?>

                <li><a href="registrar.php">Resgistrarse</a></li>
            </ul>
        </nav>

        <!-- Te comprueba si tienes sesión iniciada y te muestra un mensaje de bienvenida personalizado para cada persona que entre-->
        <?php if (isset($_SESSION['email'])): ?>

        <div class="login">
            <h2 class="inicio sesion"> Bienvenido <?= $_SESSION['nombre'] ?></h2>
            <button class="CerrarSesion"><a href="logout.php">Cerrar Sesión</a></button>
        </div>

        <?php endif;?>

    </header>

    <main>
        <!-- Contenido principal de la página -->
        <h2>Productos Destacados</h2> 
<section class="destacados">
    <!-- Este es el código par mostrar los anuncios en la página principal-->
    <?php foreach($anuncios as $anun): ?>
    <div class="card">
        <a href="verAnuncio.php?id=<?=$anun->getId()?>">
        <div class="content">
            <div class="foto">
                <!-- Para mostrar la foto -->
                <?php 
                    $imagen = $anun->getFoto();
                    $ruta = "./fotosAnuncio/";
                    $rutaCompleta = $ruta . $imagen;
                    if (!empty($rutaCompleta)) {
                        echo "<img src='$rutaCompleta' width='350px'>";
                    }else{
                        echo "Imagen no existe";
                    }
                ?>
            </div>
             <!-- Mostrar el resto del anuncio -->
            <div class="titulo"><?= $anun->getTitulo() ?></div>
            <div class="precio"><?= $anun->getPrecio() ?> €</div>
        </div>
        </a> 
  </div>
    <?php endforeach;?>
</section>
</main>
</body>

</html>
