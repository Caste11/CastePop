<?php 

class FotosAnuncioDAO{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getFotosByIdAnuncio($idAnuncio):array{ //Seleccionamos todas las fotos de fotosAnuncio cuando el id del anuncio sea x
        //$this->conn->prepare() devuelve un objeto de la clase mysqli_stmt
       if (!$stmt = $this->conn->prepare("SELECT * FROM fotoAnuncio WHERE idAnuncio = ?")) {
        echo "Error en la SQL: " . $this->conn->error;
       }
       //Asociar las variables a las interrogaciones (parámetros)
       $stmt->bind_param('i',$idAnuncio);
       //Ejecutamos la SQL
       $stmt->execute();
       //Obtener el objeto mysql_result
       $result = $stmt->get_result();
  
      $array_fotosanuncios = array();
  
      while($foto = $result->fetch_object(fotosAnuncio::class)){
           $array_fotosanuncios[] = $foto;
      }
      return $array_fotosanuncios;
      
    }

    
    public function insert(int $idAnuncio, array $fotos):bool {
    if (!$stmt = $this->conn->prepare("INSERT INTO fotoAnuncio (foto, idAnuncio) VALUES (?,?)")) {
        die("Error al preparar la consulta insert: " . $this->conn->error );}

        foreach ($fotos as $foto) {
            $stmt->bind_param('si', $foto, $idAnuncio);
            $stmt->execute();
        }

        if ($this->conn->affected_rows == count($fotos)){
            return true;
        } else {
            return false;
        }
    }
}

?>