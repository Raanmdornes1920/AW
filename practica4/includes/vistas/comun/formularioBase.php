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

        $opciones = array_merge($opcionesPorDefecto, $opciones);

        $this->action         = $opciones['action'];
        $this->method         = $opciones['method'];
        $this->enctype        = $opciones['enctype'];
        $this->urlRedireccion = $opciones['urlRedireccion'];

        if (!$this->action)
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

        if (strcasecmp('GET', $this->method) == 0)
        {
            $datos = &$_GET;
        }

        $this->errores = [];

        if (!$this->formularioEnviado($datos))
        {
            return $this->generaFormulario();
        }

        $this->procesaFormulario($datos);

        $esValido = count($this->errores) === 0;

        if (! $esValido )
        {
            return $this->generaFormulario($datos);
        }

        if ($this->urlRedireccion !== null)
        {
            header("Location: {$this->urlRedireccion}");

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

            $html .= '</ul>';
        }

        return $html;
    }

    protected static function generarError($campo, $errores)
    {
        return isset($errores[$campo]) ? "<span class=\"form-field-error\">{$errores[$campo]}</span>": '';
    }

    protected static function generaErroresCampos($campos, $errores)
    {
        $erroresCampos = [];

        foreach($campos as $campo)
        {
            $erroresCampos[$campo] = self::generarError($campo, $errores);
        }

        return $erroresCampos;
    }


    //endregion

    //region Métodos abstractos

    abstract protected function generaCamposFormulario(&$datos);

    abstract protected function procesaFormulario(&$datos);

    //endregion
}
?>