<?php

namespace Epic\Template;

class Template
{
    private $vars = array();

    public function __get($name)
    {
        return $this->vars[$name];
    }

    public function __set($name, $value)
    {
        if ($name == 'view_template_file') {
            throw new \Exception("Cannot bind variable named 'view_template_file'");
        }

        $this->vars[$name] = $value;
    }

    public function render($view_template_file)
    {
        if (array_key_exists('view_template_file', $this->vars)) {
            throw new \Exception("Cannot bind variable named 'view_template_file'");
        }

        extract($this->vars);
        ob_start(); // Créer un buffer de sortie

        include($view_template_file);

        return ob_get_clean(); // Retourne le contenu du buffer
    }
}