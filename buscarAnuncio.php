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

$anuncioDAO = new AnuncioDAO($conn);
$titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';
$anuncioFiltro = $anuncioDAO->getTituloAnuncio($titulo);

header('location: index.php?titulo=' .urlencode($titulo));
die();

?>