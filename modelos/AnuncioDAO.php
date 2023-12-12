<?php 

class AnuncioDAO{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getById($id):Anuncio|null{
        //$this->conn->prepare() devuelve un objeto de la clase mysqli_stmt
       if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios WHERE id = ?")) {
        echo "Error en la SQL: " . $this->conn->error;
       }
       //Asociar las variables a las interrogaciones (parámetros)
       $stmt->bind_param('i',$id);
       //Ejecutamos la SQL
       $stmt->execute();
       //Obtener el objeto mysql_result
       $result = $stmt->get_result();

       //Si ha encontrado algún resultado devolvemos un objeto de la clase Anuncio, sino null
       if ($result->num_rows == 1) {
        $anuncio = $result->fetch_object(Anuncio::class);
        return $anuncio;
       }else {
        return null;
       }
      
    }

    /**
     * Para coger el Id del usuario para mostrar el aununcio de dicho usuario */ 

    public function getByIdUsuario($idUsuario):array{
      //$this->conn->prepare() devuelve un objeto de la clase mysqli_stmt
     if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios WHERE idUsuario = ?")) {
      echo "Error en la SQL: " . $this->conn->error;
     }
     //Asociar las variables a las interrogaciones (parámetros)
     $stmt->bind_param('i',$idUsuario);
     //Ejecutamos la SQL
     $stmt->execute();
     //Obtener el objeto mysql_result
     $result = $stmt->get_result();

    $array_anuncios = array();

    while($anuncio = $result->fetch_object(Anuncio::class)){
         $array_anuncios[] = $anuncio;
    }
    return $array_anuncios;
    
  }

    /**
     * Obtiene todos los anuncios de la tabla anuncios
     */

     public function getAll():array{
        //$this->conn->prepare() devuelve un objeto de la clase mysqli_stmt
       if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios ORDER BY fecha_creacion DESC")) {
        echo "Error en la SQL: " . $this->conn->error;
       }
       //Ejecutamos la SQL
       $stmt->execute();
       //Obtener el objeto mysql_result
       $result = $stmt->get_result();

      $array_anuncios = array();

      while($anuncio = $result->fetch_object(Anuncio::class)){
           $array_anuncios[] = $anuncio;
      }
      return $array_anuncios;
    }

    /**
     * Borra el anuncio de la tabla anuncios del id pasado por parámetro
     * @return true se ha borrado el anuncio y false si no lo ha borrado (por que no existia)
     */
    function delete($id):bool{
        if (!$stmt = $this->conn->prepare("DELETE FROM anuncios WHERE id = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las varialbes a las interrogaciones (parametros)
        $stmt->bind_param('i',$id);
        //Ejecutamos la SQL
        $stmt->execute();
        //Comprobamos si ha borrado algún regristo o no
        if ($stmt->affected_rows == 1) {
            return true;
        }else{
            return false;
        }
    }

     /**
     * Inserta en la base de datos el anuncio que recibe como paŕametro
     * @return idAnuncio Devuvle el id autonumérico que se le ha asignado al anuncio o false en caso de error
     */

     function insert($anuncio):int|bool{
        if(!$stmt = $this->conn->prepare("INSERT INTO anuncios (precio, titulo, descripcion, foto, idUsuario) VALUES (?,?,?,?,?) ")){
          die("Error al prepar la consulta insert: " . $this->conn->error);
        }
        $precio = $anuncio->getPrecio();
        $titulo = $anuncio->getTitulo();
        $descripcion = $anuncio->getDescripcion();
        $foto = $anuncio->getFoto();
        $idUsuario = $anuncio->getIdUsuario();
        $stmt->bind_param('dsssi',$precio,$titulo,$descripcion,$foto,$idUsuario);
        if($stmt->execute()){
          return $stmt->insert_id;
        }else{
          return false;
        }
        
        
      }

      function update($anuncio){
        if(!$stmt = $this->conn->prepare("UPDATE anuncios SET titulo=?, descripcion=?,precio=?,foto=? WHERE id=?")){
            die("Error al preparar la consulta update: " . $this->conn->error );
        }
        $precio = $anuncio->getPrecio();
        $titulo = $anuncio->getTitulo();
        $descripcion = $anuncio->getDescripcion();
        $foto = $anuncio->getFoto();
        $id = $anuncio->getId();
        $stmt->bind_param('ssisi',$titulo,$descripcion,$precio,$foto,$id);
        return $stmt->execute();

    }
      

      public function getTituloAnuncio($titulo):array{
        if (!$stmt= $this->conn->prepare("SELECT * FROM anuncios WHERE titulo LIKE ? ORDER BY fecha_creacion DESC")) {
          die("Error al preparar la consulta getTituloAnuncio " .$this->conn->error);
        }
        $titulo = '%'.$titulo.'%';
        $stmt->bind_param('s',$titulo);
        $stmt->execute();
        $result = $stmt->get_result();

        $listAnuncios = array();
        while($anuncio = $result->fetch_object(Anuncio::class)){
          $listAnuncios[]= $anuncio;
        }
        return $listAnuncios;
      }
}

?>