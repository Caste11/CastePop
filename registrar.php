<?php 
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/config.php';

$error='';

if($_SERVER['REQUEST_METHOD']=='POST'){

    //Limpiamos los datos
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);
    $nombre = htmlspecialchars($_POST['nombre']);
    $telefono = htmlspecialchars($_POST['telefono']);
    $poblacion = htmlspecialchars($_POST['poblacion']);


    //Conectamos con la BD
    $connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_BD);
    $conn = $connexionDB->getConnexion();

    
    $usuariosDAO = new UsuariosDAO($conn);

    //Compruebo si la contraseña tiene menos de 4 caracteres y si tiene más te verifica que el email introducido ni esta registrado
    if(strlen($password) <4){
        $error = "La contraseña debe de tener al menos 4 caracteres";
    }else{

      if($usuariosDAO->getByEmail($email) != null){
        $error = "Ya hay un usuario con ese email";
    }
    else{
        //Insertamos en la BD
        $usuario = new Usuario();
        $usuario->setEmail($email);
        //encriptamos el password
        $passwordCifrado = password_hash($password,PASSWORD_DEFAULT);
        $usuario->setPassword($passwordCifrado);
        $usuario->setNombre($nombre);
        $usuario->setTelefono($telefono);
        $usuario->setPoblacion($poblacion);
        $usuario->setSid(sha1(rand() + time()),true); 

       
        //Si me inseta el usuario me redirige al index y sino me salta un fallo
        if($usuariosDAO->insert($usuario)){
            header("location: index.php");
            die();
        }else{
            $error = "No se ha podido insertar el usuario";
        }
    }
    }

    
    
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar</title>
    <link rel="stylesheet" href="./Style/estilos.css">
</head>
<body>

<div class="formulario">  
<form class="form" action="registrar.php" method="post">
       <p class="form-title">Registrate</p>
       <?= $error ?>
        <div class="input-container">
          <input type="email" name="email" placeholder="Introduce tu email" required value="<?= isset($email) ? $email : '' ?>"> 
          <span>
          </span>
      </div>
      <div class="input-container">
          <input type="password" name="password" placeholder="Intorducce la password" required value="<?= isset($password) ? $password : '' ?>"> 
        </div>
        <div class="input-container">
          <input type="text" name="nombre" placeholder="Introduce tu nombre" required value="<?= isset($nombre) ? $nombre : '' ?>"> 
        </div>
        <div class="input-container">
          <input type="text" name="telefono" placeholder="Introduce tu teléfono" required value="<?= isset($telefono) ? $telefono : '' ?>"> 
        </div>
        <div class="input-container">
          <input type="text" name="poblacion" placeholder="Introduce la Población" required value="<?= isset($poblacion) ? $poblacion : '' ?>"> 
        </div>
         <button type="submit" class="submit" value="registrar">
        Resgistrarse
      </button>

      <p class="signup-link">
        Si ya tienes cuenta Incia Sesion
        <a href="login.php">Iniciar Sesión</a> <br>
        <a href="index.php">Volver al principio</a>
      </p>
</form>
</div>

</body>
</html>