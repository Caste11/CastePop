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

//Para que no te deje acceder al editar o borrar de otro usuario
$antiguioAnuncio = $anuncioDAO->getById($idAnuncio);
$usuarioid = $antiguioAnuncio->getIdUsuario();
if($usuarioid == $_SESSION['id']){
    $_SESSION['perfecto'] = true;
}else{
    header("Location: index.php");
    $_SESSION['erroneo'] = "No tienes derecho a editar o borrar los anuncios agenos";
    die();
}

$anuncio = $anuncioDAO->getById($idAnuncio);
//Creamos el objeto de FotosAnuncioDAO para acceder a BBDD y poder coger todas las fotos
$fotosAnuncioDAO = new FotosAnuncioDAO($conn);
$fotos = $fotosAnuncioDAO->getFotosByIdAnuncio($idAnuncio); 


if($_SESSION['perfecto']){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        //Limpiamos los datos que vienen del usuario
        $foto = '';
        $titulo = htmlspecialchars($_POST['titulo']);
        $descripcion = $_POST['descripcion'];
        //validamos los datos
        $foto = $anuncio->getFoto();
        if ( empty($titulo) || empty($descripcion)) {
            $error = "Los campos son obligatorios";
        } else {
            $anuncio->setTitulo($titulo);
            $anuncio->setDescripcion($descripcion);
            $anuncio->setId($idAnuncio);
            $anuncio->setIdUsuario($_SESSION['id']);
    
            if(empty($_FILES['foto']['name'])){
                $anuncio->setFoto($foto);
            }else{
                $foto = generarNombreArchivo($_FILES['foto']['name']);
                if(!move_uploaded_file($_FILES['foto']['tmp_name'], './fotosAnuncio/'.$foto)){
                    die();
                }
                $anuncio->setFoto($foto);
            }
            
            if ($anuncioDAO->update($anuncio)) {
                header('location: index.php');
                die();
            }
        }
    }  
}


$error ='';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Anuncio</title>

    <link rel="stylesheet" href="Style/estilos.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
</head>
<body>
    <div class="formulario">
    <form class="form" method="post" action="editarAnuncio.php?id= <?= $idAnuncio ?>" enctype="multipart/form-data" id="nuevoanuncio">
       <p class="form-title">Edita tu Anuncio</p>
       <p><?= $error ?></p>
        <div class="input-container">
        <input type="text" name="titulo" placeholder="Titulo" value="<?= $anuncio->getTitulo() ?>">
          <span>
          </span>
      </div>
      <div class="input-container">
      
      <div id="descripcion">
            <p><strong><?= $anuncio->getDescripcion() ?></strong></p>
        </div>
            <input type="hidden" id="descripcion" name="descripcion">

      </div>

      <div class="input-container">
      <?php
        $image = $anuncio->getFoto();
        $ruta = "fotosAnuncio/";

        if (!empty($image)) {
            echo '<img src="' . $ruta . $image . '" alt="Anuncio Image">';
        } else {
            echo '<p>Imagen no disponible</p>';
        }
        ?>
      </div>

      <div class="input-container">
      <input type="file" name="foto" accept="image/jpeg, image/gif, image/webp, image/png" value="<?= $anuncio->getFoto() ?>">
      </div>

         <input type="submit" class="submit" value="Enviar">

      <span>
    </span>

      <p class="signup-link">
        Si cambias de opinión siempre puedes volver para atras <br>
        <a href="index.php">Volver al principio</a>
      </p>
   </form>
   </div>

</body>
<script>
        var quill = new Quill('#descripcion', {
            theme: 'snow'
        });

        var form = document.getElementById("nuevoanuncio");
        form.onsubmit = function() {
            var texto = quill.getText();
            var name = document.querySelector('input[name=descripcion]');
            name.value = texto.trim();
            return true;
        }
    </script>
</html>