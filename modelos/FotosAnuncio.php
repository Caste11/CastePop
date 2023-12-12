<?php 

class fotosAnuncio{
    private $foto;
    private $idAnuncio;

    

    /**
     * Get the value of foto
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set the value of foto
     */
    public function setFoto($foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get the value of idAnuncio
     */
    public function getIdAnuncio()
    {
        return $this->idAnuncio;
    }

    /**
     * Set the value of idAnuncio
     */
    public function setIdAnuncio($idAnuncio): self
    {
        $this->idAnuncio = $idAnuncio;

        return $this;
    }
}

?>