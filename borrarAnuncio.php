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

$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_BD);
$conn = $connexionDB->getConnexion();

//Creamos el objeto MensajesDAO para acceder a BBDD a través de este objeto
$anuncioDAO = new AnuncioDAO($conn);

//Obtener el mensaje
$idAnuncio = htmlspecialchars($_GET['id']);
$anuncio = $anuncioDAO->getById($idAnuncio);

//Para coger los objetos de Fotos Anuncio DAO
$fotosAnuncioDAO = new FotosAnuncioDAO($conn);

//La ruta completa de la foto del anuncio
$imagen = $anuncio->getFoto();
$ruta = "./fotosAnuncio/";
$rutaCompleta = $ruta . $imagen;

//comrpbamos que mensaje pertenece al usuario conectado
if ($_SESSION['id'] == $anuncio->getIdUsuario()) {
    $fotosAnun = $fotosAnuncioDAO->getFotosByIdAnuncio($idAnuncio);

        // Eliminar los archivos de la carpeta fotosAnuncios menos la principal
        foreach ($fotosAnun as $foto) {
            $fotos = $foto->getFoto();
            
                $rutaCompleta2 = $ruta . $fotos;
                
                if (file_exists($rutaCompleta2)) {
                    if (unlink($rutaCompleta2)) {
                    } else {
                        $_SESSION['error'] = "Error al intentar eliminar el archivo.";
                    }
                } else {
                    $_SESSION['error'] = "El archivo no existe en la ruta proporcionada.";
                }
            
        }

    
    unlink($rutaCompleta);
    $anuncioDAO->delete($idAnuncio);
    header("Location: misAnuncios.php");
}else{
    $_SESSION['error'] = "No puedes borrar este mensaje";
}
