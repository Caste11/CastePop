<?php 
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/Anuncio.php';
require_once 'modelos/AnuncioDAO.php';
require_once 'modelos/funciones.php';
require_once 'modelos/config.php';
require_once 'modelos/FotosAnuncio.php';
require_once 'modelos/FotosAnuncioDAO.php';

//Creamos la conexión utilizando la clase que hemos creado
$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_BD);
$conn = $connexionDB->getConnexion();

//Creamos el objeto MensajeDAO para acceder a BBDD a través de este objeto
$anuncioDAO = new AnuncioDAO($conn);
$idAnuncio = htmlspecialchars($_GET['id']);
$anuncio = $anuncioDAO->getById($idAnuncio);
//Creamos el objeto UsuarioDAO para acceder a BBDD y poder coger los datos del usuario
$usuarioDAO = new UsuariosDAO($conn);
$idUsuario = $anuncio->getIdUsuario();
$usuario = $usuarioDAO->getById($idUsuario);
//Creamos el objeto de FotosAnuncioDAO para acceder a BBDD y poder coger todas las fotos
$fotosAnuncioDAO = new FotosAnuncioDAO($conn);
$fotos = $fotosAnuncioDAO->getFotosByIdAnuncio($idAnuncio); 



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncios</title>
    <style>
        .ver_anuncio{
            margin: 10px auto;
            padding: 5px;
            width: 80%;
            text-align: center;
        }
        .titulo{
            font-size: 2.5em;
        }
        .texto{
            font-size: 2em;
        }
        body{
            background-image: url(img/fondoxp.png);
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .volveratras a{
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="ver_anuncio">
    <?php if( $anuncio!= null): ?>
    <strong class="titulo">Titulo:</strong> <?= $anuncio->getTitulo() ?> <br>
    <strong class="titulo">Foto Principal</strong> <br>
    <img src="fotosAnuncio/<?= $anuncio->getFoto() ?>" alt=""> <br>

    <strong class="titulo">Resto de las fotos</strong><br>
    <!--Metemos todo el array con el resto de las fotos-->
    <?php foreach($fotos as $foto):?>

        <div class="anuncio">
          
        <img src="fotosAnuncio/<?= $foto->getFoto() ?>" alt=""> <br>
            
        </div>
    <?php endforeach;?>

    <strong class="descripcion">Descripcion:</strong> <?= $anuncio->getDescripcion() ?> <br>
    <strong class="precio">Precio:</strong> <?= $anuncio->getPrecio() ?> <br>
    <strong class="nombre">Nombre: </strong> <?= $usuario->getNombre() ?> <br>
    <strong class="email">Email: </strong> <?= $usuario->getEmail() ?> <br>
    <strong class="telefono">Telefono: </strong> <?= $usuario->getTelefono() ?> <br>
<?php else: ?>
    <strong>Mensaje con id <?= $id ?> no encontrado</strong>
<?php endif; ?>
<button class="volveratras"><a href="index.php">Volver al listado de mensajes</a></button>
    </div>
</body>
</html>