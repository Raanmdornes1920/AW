<?php

class Categoria {
    private $id;
    private $nombre;
    private $descripcion;
    private $imagen;

    public function __construct($id, $nombre, $descripcion, $imagen = 'default_cat.png') {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
    }

    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getImagen() { return $this->imagen; }
}