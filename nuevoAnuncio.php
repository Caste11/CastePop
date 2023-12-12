<?php 
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/Anuncio.php';
require_once 'modelos/AnuncioDAO.php';
require_once 'modelos/config.php';
require_once 'modelos/funciones.php';
require_once 'modelos/FotosAnuncio.php';
require_once 'modelos/FotosAnuncioDAO.php';

if(!isset($_SESSION['email'])){
    header("location: index.php");
    guardarAnuncio("No puedes insertar mensajes si no estás indentificado");
    die();
}

$error ='';

//Creamos la conexión utilizando la clase que hemos creado
$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_BD);
$conn = $connexionDB->getConnexion();

//Instanciamos UsuariosDAO y cogemos todos los usuarios
$usuariosDAO = new UsuariosDAO($conn);
$usuarios = $usuariosDAO->getAll();


if($_SERVER['REQUEST_METHOD']=='POST'){

    //Limpiamos los datos que vienen del usuario
    $precio = htmlspecialchars($_POST['precio']);
    $titulo = htmlspecialchars($_POST['titulo']);
    $descripcion =  htmlspecialchars($_POST['descripcion']);

    //Instanciamos los anuncios
    $anunciosDAO = new AnuncioDAO($conn);
    $anuncios = new Anuncio();
    
    
   

    //Validamos los datos
    if(empty($precio) || empty($titulo) || empty($descripcion)){
        $error = "Los campos son obligatorios";
    }
    else{
        //El codigo para meter varias fotos
        $arrfotos = array();
        $arrfotosTemporales = array();
        $arrfotoshash = array();

        if ($_FILES['fotos']['error'][0] == UPLOAD_ERR_NO_FILE) {
            $error = "Tienes que añadir una foto";
        } else if (count($_FILES['fotos']['name']) > 10) {
            $error = "El tope máximo de fotos es de 10";
        } else {
            $cant_fotos = count($_FILES['fotos']['name']);
    
            for ($i = 0; $i < $cant_fotos; $i++) {
                $arrfotos[] = $_FILES['fotos']['name'][$i];
                $arrfotosTemporales[] = $_FILES['fotos']['tmp_name'][$i];
            }
        }
    
        foreach ($arrfotos as $i => $foto) {
        
            // Comprobamos que la extensión de los archivos introducidos son válidas
            $extension = pathinfo($foto, PATHINFO_EXTENSION);
            if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'webp' && $extension != 'png') {
                $error = "Tu foto no tiene el formato adecuado, tiene que ser jpg, jpeg, png o webp";
            } else {
                // Copiamos la foto al disco
                // Calculamos un hash para el nombre del archivo
                $foto = uniqid(true) . '.' . $extension;
    
                // Si existe un archivo con ese nombre volvemos a calcular el hash
                while (file_exists("fotosAnuncio/$foto")) {
                    $foto = uniqid(true) . '.' . $extension;
                }

                if ($i == 0) {
                    $anuncios->setFoto($foto);
                }
                //Movemos la foto a la carpeta
                foreach ($arrfotosTemporales as $j => $fotoTemporal) {
                    if ($i == $j) {
                        if (!move_uploaded_file($fotoTemporal, "fotosAnuncio/$foto")) {
                            die("Error la foto no se se ha copiado en la carpeta fotosAnuncio");
                        }
                    }
                }
    
                $arrfotoshash[] = $foto;
            }
        }

        //Creamos el objeto AnunciosDAO para acceder a BBDD a través de este objeto
        $anuncios->setPrecio($precio);
        $anuncios->setTitulo($titulo);
        $anuncios->setDescripcion($descripcion);
        $anuncios->setIdUsuario($_SESSION['id']); //Cogemos el id de la session del momento
        $idA = $anunciosDAO->insert($anuncios);

        if ($idA != null) {
            $fotosAnuncioDAO = new FotosAnuncioDAO($conn);
            $fotosAnuncioDAO->insert($idA, $arrfotoshash);
        }else{
            $error = 'No se ha podido insertar las fotos de dicho anuncio';
        }

        header('location: index.php');
        die();
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Introduce tu Anuncio</title>

    <link rel="stylesheet" href="Style/estilos.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>

</head>
<body>
    <?= $error ?>
    <div class="formulario">
    <form class="form" method="post" action="nuevoAnuncio.php" enctype="multipart/form-data" id="nuevoanuncio">
       <p class="form-title">Introduce Un nuevo Anuncio</p>
       <p><?= $error ?></p>
        <div class="input-container">
        <input type="text" name="titulo" placeholder="Titulo">
          <span>
          </span>
      </div>
      <div class="input-container">
        <input type="text" step="any" name="precio" placeholder="Precio">
      </div>
      <div class="input-container">
      
      <div id="descripcion">
            <p><strong>Descripcion</strong></p>
        </div>
            <input type="hidden" id="descripcion" name="descripcion">

      </div>

      <div class="input-container">
      <input type="file" name="fotos[]" accept="image/jpeg, image/gif, image/webp, image/png" multiple>
      </div>

         <input type="submit" class="submit" value="Enviar">

      <span>
    </span>

      <p class="signup-link">
        Si no quieres introducir un anuncio puedes volver al principio <br>
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