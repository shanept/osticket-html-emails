<?php

require_once(INCLUDE_DIR . 'class.plugin.php');
require_once(INCLUDE_DIR . 'class.message.php');
require_once(__DIR__ . '/class.Editor.php');

class EmailPluginConfig extends PluginConfig
{

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate()
    {
        if (!method_exists('Plugin', 'translate')) {
            return [
                function ($x) {
                    return $x;
                },
                function ($x, $y, $n) {
                    return $n != 1 ? $y : $x;
                }
            ];
        }
        return Plugin::translate('closer');
    }

    /**
     * Build an Admin settings page.
     *
     * {@inheritdoc}
     *
     * @see PluginConfig::getOptions()
     */
    function getOptions()
    {
        list ($__, $_N) = self::translate();

        return [
            'config' => new SectionBreakField(['label' => $__('HTML Emails')]),
            'html-enabled' => new BooleanField([
                'label'   => __('Enable HTML Emails'),
                'hint'    => __('Emails can be configured without being enabled'),
                'default' => false,
            ]),
            'css-stylesheet' => new EditorField([
                'label' => $__('Stylesheet'),
                'hint'  => $__('Specifies the CSS stylesheet to be included with the email'),
                'configuration' => [
                    'mode' => 'css',
                ],
            ]),
            'msg-head' => new EditorField([
                'label' => $__('Message Head'),
                'hint'  => $__('Specifies additional tags to be inserted into the head'),
                'configuration' => [
                    'placeholder' => '&lt;meta charset=&#34;UTF-8&#34; /&gt;',
                    'mode'        => 'html',
                ],
                'default' => "<meta charset=\"UTF-8\" />\n" .
                             "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n" .
                             "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n",
            ]),
            'structure' => new EditorField([
                'label' => $__('Message Template'),
                'hint'  => $__('Specifies the HTML body structure surrounding the email contents'),
                'configuration' => [
                    'mode' => 'html',
                ],
                'default' => '%{body}',
            ]),
        ];
    }
}
