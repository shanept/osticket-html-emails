<?php

class HTMLEmail
{
    private static $config;

    private static $head = <<<'OUT'
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
OUT;

    static function format($body)
    {
        global $ost;

        if (!self::get('html-enabled')) return $body;

        $head     = self::get('msg-head') ?: self::$head;
        $style    = self::get('css-stylesheet') ?: '';
        $contents = self::get('structure') ?: '%{body}';

        // replace body tags
        $contents = $ost->replaceTemplateVariables($contents, [
            'body' => $body
        ]);

        return <<<FMT
<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml">
    <head>
$head
<style type="text/css">
$style
</style>
    </head>
    <body>
$contents
    </body>
</html>
FMT;
    }

    static function get($key)
    {
        return self::getconfig()[$key] ?: false;
    }

    static function getconfig()
    {
        if (is_null(self::$config)) {
            self::$config = [];

            $sql    = "SELECT `key`,`value` FROM " . TABLE_PREFIX . "config WHERE `namespace` = CONCAT_WS('.','plugin',(SELECT `id` FROM " . TABLE_PREFIX . "plugin WHERE `install_path` = 'plugins/emails'));";
            $result = db_query($sql);

            while ($row = db_fetch_array($result)) {
                self::$config[$row['key']] = $row['value'];
            }
        }

        return self::$config;
    }
}
