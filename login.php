<?php 
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/config.php';

$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_BD);
$conn = $connexionDB->getConnexion();

$error = '';

if($_SERVER['REQUEST_METHOD']=='POST'){

$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars($_POST['password']);


$usuariosDAO = new UsuariosDAO($conn);
if ($usuario = $usuariosDAO->getByEmail($email)) {
    if (password_verify($password, $usuario->getPassword())) {
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['id'] = $usuario->getId();
        $_SESSION['nombre'] = $usuario->getNombre();
        
        setcookie('sid',$usuario->getSid(),time()+7*24*60*60,'/');
        header('location: index.php');
        die();
    }else{
     $error = "Email o password incorrectos";
    }
}else{
  $error = "Contraseña e Usuario incorrectos";
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Style/estilos.css">
</head>
<body>
  <div class="formulario">
    <form class="form" method="post">
       <p class="form-title">Inicia Sesión</p>
       <p><?= $error ?></p>
        <div class="input-container">
          <input type="email" name="email" placeholder="Introduce tu email">
          <span>
          </span>
      </div>
      <div class="input-container">
          <input type="password" name="password" placeholder="Introduce tu password">
      </div>
         <input type="submit" class="submit" value="Iniciar Sesión">

      <span>
    </span>

      <p class="signup-link">
        No tienes cuenta Registrate
        <a href="registrar.php">Resgistrarse</a> <br>
        <a href="index.php">Volver al principio</a>
      </p>
   </form>
   </div>
</body>
</html>