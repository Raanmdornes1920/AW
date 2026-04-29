<?php

abstract class formularioBase
{
    //region Campos protegidos

    protected $formId;

    protected $method;

    protected $action;

    protected $enctype;

    protected $urlRedireccion;

    protected $errores;

    //endregion

    //region Constructores

    public function __construct($formId, $opciones = array())
    {
        $this->formId = $formId;

        $opcionesPorDefecto = array('action' => null, 'method' => 'POST', 'enctype' => null, 'urlRedireccion' => null);
<<<<<<< HEAD
        
=======

>>>>>>> angela
        $opciones = array_merge($opcionesPorDefecto, $opciones);

        $this->action         = $opciones['action'];
        $this->method         = $opciones['method'];
        $this->enctype        = $opciones['enctype'];
        $this->urlRedireccion = $opciones['urlRedireccion'];

<<<<<<< HEAD
        if (!$this->action) 
=======
        if (!$this->action)
>>>>>>> angela
        {
            $this->action = htmlspecialchars($_SERVER['REQUEST_URI']);
        }
    }

    //endregion

    public function gestiona()
    {
        $datos = &$_POST;
        // echo "<pre>";
        // print_r($_FILES); // Esto te dirá qué está llegando realmente
        // echo "</pre>";
        // die();
<<<<<<< HEAD
        
        if (strcasecmp('GET', $this->method) == 0) 
        {
            $datos = &$_GET;
        }
        
        $this->errores = [];

        if (!$this->formularioEnviado($datos)) 
=======

        if (strcasecmp('GET', $this->method) == 0)
        {
            $datos = &$_GET;
        }

        $this->errores = [];

        if (!$this->formularioEnviado($datos))
>>>>>>> angela
        {
            return $this->generaFormulario();
        }

        $this->procesaFormulario($datos);
<<<<<<< HEAD
        
        $esValido = count($this->errores) === 0;

        if (! $esValido ) 
=======

        $esValido = count($this->errores) === 0;

        if (! $esValido )
>>>>>>> angela
        {
            return $this->generaFormulario($datos);
        }

<<<<<<< HEAD
        if ($this->urlRedireccion !== null) 
        {
            header("Location: {$this->urlRedireccion}");
    
=======
        if ($this->urlRedireccion !== null)
        {
            header("Location: {$this->urlRedireccion}");

>>>>>>> angela
            exit();
        }
    }

    //region Métodos privados

    private function formularioEnviado(&$datos)
    {
        return isset($datos['formId']) && $datos['formId'] == $this->formId;
    }

    private function generaFormulario(&$datos = array())
    {
        $htmlCamposFormularios = $this->generaCamposFormulario($datos);

        $enctypeAtt = $this->enctype != null ? "enctype=\"{$this->enctype}\"" : '';

        $htmlForm = <<<EOS
        <form method="{$this->method}" action="{$this->action}" id="{$this->formId}" {$enctypeAtt}>
                <input type="hidden" name="formId" value="{$this->formId}" />
                $htmlCamposFormularios
        </form>
        EOS;

        return $htmlForm;
    }

    //endregion

    //region Métodos protegidos

    protected static function generaListaErroresGlobales($errores = array())
    {
        $html = '';
<<<<<<< HEAD
        
        $keys = array_filter(array_keys($errores), function($v) 
        {
            return is_numeric($v);
        });
        
        if (count($keys) > 0) 
        {
            $html = '<ul class="errores">';
            
            foreach($keys as $key) 
            {
                $html .= "<li>{$errores[$key]}</li>";
            }
            
=======

        $keys = array_filter(array_keys($errores), function($v)
        {
            return is_numeric($v);
        });

        if (count($keys) > 0)
        {
            $html = '<ul class="errores">';

            foreach($keys as $key)
            {
                $html .= "<li>{$errores[$key]}</li>";
            }

>>>>>>> angela
            $html .= '</ul>';
        }

        return $html;
    }

    protected static function generarError($campo, $errores)
    {
        return isset($errores[$campo]) ? "<span class=\"form-field-error\">{$errores[$campo]}</span>": '';
    }

<<<<<<< HEAD
    protected static function generaErroresCampos($campos, $errores) 
    {
        $erroresCampos = [];

        foreach($campos as $campo) 
        {
            $erroresCampos[$campo] = self::generarError($campo, $errores);
        }
        
        return $erroresCampos;
    }
    
    
=======
    protected static function generaErroresCampos($campos, $errores)
    {
        $erroresCampos = [];

        foreach($campos as $campo)
        {
            $erroresCampos[$campo] = self::generarError($campo, $errores);
        }

        return $erroresCampos;
    }


>>>>>>> angela
    //endregion

    //region Métodos abstractos

    abstract protected function generaCamposFormulario(&$datos);
<<<<<<< HEAD
    
=======

>>>>>>> angela
    abstract protected function procesaFormulario(&$datos);

    //endregion
}
?>