<?php
class Producto {
    private $id;
    private $id_categoria;
    private $nombre;
    private $descripcion;
    private $precio_base;
    private $iva;
    private $disponible;
    private $ofertado;
    private $cocinable; 
    private $nombre_categoria; 
    private $imagenes = [];

    public function __construct($id, $id_cat, $nom, $desc, $pb, $iva, $disp, $ofert, $cocinable = 1, $cat_nom = '', $imagenes = []) {
        $this->id = $id;
        $this->id_categoria = $id_cat;
        $this->nombre = $nom;
        $this->descripcion = $desc;
        $this->precio_base = $pb;
        $this->iva = $iva;
        $this->disponible = $disp;
        $this->ofertado = $ofert;
        $this->cocinable = $cocinable; 
        $this->nombre_categoria = $cat_nom;
        $this->imagenes = is_array($imagenes) ? $imagenes : [$imagenes];    
    }

    public function getId() { return $this->id; }
    public function getIdCategoria() { return $this->id_categoria; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getPrecioBase() { return $this->precio_base; }
    public function getIva() { return $this->iva; }
    public function getDisponible() { return $this->disponible; }
    public function getOfertado() { return $this->ofertado; }
    public function getCocinable() { return $this->cocinable; } 
    public function getNombreCategoria() { return $this->nombre_categoria; }
    
    // MÉTODOS DE IMÁGENES BLINDADOS
    public function getImagen() { 
        if (empty($this->imagenes)) return 'default.png';
        
        $img = $this->imagenes;
        
        while (is_array($img)) {
            $img = !empty($img) ? array_values($img)[0] : 'default.png';
        }
        
        return (string) $img;
    }
    
    public function getImagenesArray() { 
        return is_array($this->imagenes) ? $this->imagenes : []; 
    }

    public function getPrecioFinal() {
        return $this->precio_base * (1 + ($this->iva / 100));
    }
}
?>