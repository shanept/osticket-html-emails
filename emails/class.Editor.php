<?php
class EditorField extends FormField
{
    static $widget = EditorWidget::class;

    function getConfigurationOptions()
    {
        return [
            'cols'  =>  new TextboxField(array(
                'id'=>1, 'label'=>__('Width').' '.__('(chars)'), 'required'=>false, 'default'=>40)),
            'rows'  =>  new TextboxField(array(
                'id'=>2, 'label'=>__('Height').' '.__('(rows)'), 'required'=>false, 'default'=>20)),
            'class' =>  new TextboxField(array(
                'id'=>3, 'label'=>__('Class'), 'required'=>false, 'default'=>'',
                'hint'=>__('Classes added to Editor'))),
            'placeholder' => new TextboxField(array(
                'id'=>4, 'label'=>__('Placeholder'), 'required'=>false, 'default'=>'',
                'hint'=>__('Text shown in before any input from the user'),
                'configuration'=>array('size'=>40, 'length'=>40,
                    'translatable'=>$this->getTranslateTag('placeholder')),
            )),
        ];
    }
}

class EditorWidget extends Widget
{
    function render($options = array())
    {
        $config = $this->field->getConfiguration();
        $class  = '';
        $attrs  = [];

        if (isset($config['placeholder']))
            $attrs[] = 'placeholder="' . Format::htmlchars($config['placeholder']) . '"';
        if (isset($config['class']))
            $class = "{$class} {$config['class']}";

        // The mode is required by Ace editor to specify what language we are using
        $mode = strtolower($config['mode'] ?: 'html');
        if (!in_array($mode, ['html', 'css', 'javascript']))
            $mode = 'html';

        // Now to set up a few other required attributes
        $attrs[] = "id=\"{$this->id}\"";
        $attrs[] = "name=\"{$this->name}\"";
        $attrs   = implode(' ', $attrs);

        // And some final stuff
        $value = Format::htmlchars($this->value);
        $id    = Format::htmlchars($this->id);
        $min   = isset($config['min']) ? (int) $config['min'] : 5;
        $max   = isset($config['max']) ? (int) $config['max'] : 20;
    ?>
        <style type="text/css">
            #ace-<?php echo $id; ?>.ace_editor {
                text-rendering: geometricPrecision;
                font-family: monospace !important;
                font-size: 12px;
                letter-spacing: initial;
            }
            #ace-<?php echo $id; ?>.ace_editor div + div {
                padding-left: 0;
            }
            #<?php echo $id; ?> {
                display: none;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function() {
                var editor = ace.edit($("#ace-<?php echo $id; ?>").get(0), {
                    mode: "ace/mode/<?php echo $mode; ?>",
                    minLines: <?php echo $min; ?>,
                    maxLines: <?php echo $max; ?>,
                });

                $("#<?php echo $id; ?>").val(editor.getValue());
                editor.on("change", function() {
                    $("#<?php echo $id; ?>").val(editor.getValue());
                });
            });
        </script>
        <span style="display:inline-block;width:500px;min-height:100px;">
            <div class="<?php echo $class; ?>" id="ace-<?php echo $id; ?>" style="width:500px;min-height:100px;"><?php echo $value; ?></div>
            <textarea <?php echo $attrs; ?>></textarea>
        </span>
    <?php
    }
}
