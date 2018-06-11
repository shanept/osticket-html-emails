<?php
require_once(INCLUDE_DIR . 'class.plugin.php');
require_once(__DIR__ . '/config.php');

class EmailPlugin extends plugin
{
    public $config_class = EmailPluginConfig::class;

    function bootstrap()
    {
        // Inject ourselves, if required
        if (!$this->injectSelf()) return false;

        return true;
    }

    function injectSelf()
    {
        $path = INCLUDE_DIR . 'class.mailer.php';
        $contents = file_get_contents($path);

        if ($this->isInjected($contents))       return true;
        if (!$this->insertInclude($contents))   return false;
        if (!$this->insertCall($contents))      return false;

        return !!file_put_contents($path, $contents, LOCK_EX);
    }

    function isInjected($buff)
    {
        return false !== strpos($buff, 'plugins/emails/class.HTMLEmail.php');
    }

    function insertInclude(&$buff)
    {
        // Find Position
        $pos = strpos($buff, 'include');
        if (false === $pos) return false;

        // Split buffer at position and insert include
        $l = substr($buff, 0, $pos);
        $r = substr($buff, $pos);

        $buff = $l . "include_once(INCLUDE_DIR.'plugins/emails/class.HTMLEmail.php');\n" . $r;

        return true;
    }

    function insertCall(&$buff)
    {
        // Find Position of body
        $pos = strpos($buff, '//encode the body');

        // Split the buffer and insert the function call
        $l = substr($buff, 0, $pos);
        $r = substr($buff, $pos);

        $buff = $l . "// Add email styling\n" .
                     '        if ($isHtml) $mime->setHTMLBody(HTMLEmail::format($mime->getHTMLBody()));' .
                     "\n        " . $r;

        return true;
    }
}
