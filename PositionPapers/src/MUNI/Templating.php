<?php
namespace MUNI;

class Templating
{
	private $templateDir;
	
	public function __construct($templateDir)
	{
		$this->templateDir = $templateDir;
	}

    public function render($templateFile, $vars = [])
    {
        if (array_key_exists('templateFile', $vars)) {
            throw new Exception("Cannot bind variable called 'templateFile'");
        }
        extract($vars);
        ob_start();
        include($this->templateDir . DIRECTORY_SEPARATOR . $templateFile);
        return ob_get_clean();
    }
}
