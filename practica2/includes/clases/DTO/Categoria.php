<?php

class Categoria {
    private $id;
    private $nombre;
    private $descripcion;
    private $imagen;
    private $activa;

    public function __construct($id, $nombre, $descripcion, $imagen = 'default_cat.png', $activa = 1) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
        $this->activa = $activa;
    }

    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getImagen() { return $this->imagen; }
    public function getActiva() { return $this->activa; }
}