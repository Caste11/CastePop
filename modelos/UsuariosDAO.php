<?php 

class UsuariosDAO{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getById($id):Usuario|null{
        if(!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('i',$id);
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        //Si ha encontrado algún resultado devolvemos un objeto de la clase Mensaje, sino null
        if($result->num_rows >= 1){
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        }
        else{
            return null;
        }
    }

    public function getByEmail($email){
        if (!$stmt = $this->conn->prepare("SELECT * FROM  usuarios WHERE email = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >=1) {
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        }else{
            return null;
        }
    }

    public function getAll():array {
        if(!$stmt = $this->conn->prepare("SELECT * FROM usuarios"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        $array_mensajes = array();
        
        while($usuario = $result->fetch_object(Usuario::class)){
            $array_usuarios[] = $usuario;
        }
        return $array_usuarios;
    }

    function delete($id):bool{
        if (!$stmt = $this->conn->prepare("DELETE FROM Usuarios WHERE id = ?")) {
            echo "Error en la SQL: " .$this->conn->error;
        }
        $stmt->bind_param('i', $id); //Asociamos la variable al ?
        $stmt->execute(); //Ejecutamos la SQL

        //comprobamos si ha borrado o no algun registro
        if ($stmt->affected_rows == 1) {
            return true;
        }else{
            return false;
        }
    }

    function insert(Usuario $usuario): int|bool{
        if (!$stmt = $this->conn->prepare("INSERT INTO usuarios (sid, email, password, nombre, telefono, poblacion) VALUES(?,?,?,?,?,?)")) {
            die("Error al preparar la consulta insert: " . $this->conn->error);
        }
        $sid = $usuario->getSid();
        $email = $usuario->getEmail();
        $password = $usuario->getPassword();
        $nombre = $usuario->getNombre();
        $telefono = $usuario->getTelefono();
        $poblacion = $usuario->getPoblacion();
        $stmt->bind_param('ssssss', $sid,$email,$password,$nombre,$telefono,$poblacion);

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }else{
            return false;
        }
    }

    public function getBySid($sid):Usuario|null {
        if(!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE sid = ?"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('s',$sid);
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        //Si ha encontrado algún resultado devolvemos un objeto de la clase Mensaje, sino null
        if($result->num_rows >= 1){
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        }
        else{
            return null;
        }
    } 

    
}

?>