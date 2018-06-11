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
         $class  = 'richtext';
         $attrs  = [];

         if (isset($config['rows']))
             $attrs[] = "rows=\"{$config['rows']}\"";
         if (isset($config['cols']))
             $attrs[] = "cols=\"{$config['cols']}\"";
         if (isset($config['placeholder']))
             $attrs[] = 'placeholder="' . Format::htmlchars($config['placeholder']) . '"';
         if (isset($config['class']))
             $class = "{$class} {$config['class']}";

         // Now to set up a few other required attributes
         $attrs[] = "class=\"{$class}\"";
         $attrs[] = "id=\"{$this->id}\"";
         $attrs[] = "name=\"{$this->name}\"";
         $attrs   = implode(' ', $attrs);

         // And finally, the value
         $value = Format::htmlchars($this->value);
         $value = str_replace("\r\n", '<br />', $value);
         $value = str_replace("\n", '<br />', $value);
         $value = str_replace("\r", '<br />', $value);
         ?>
         <span style="display:inline-block;width:100%">
             <textarea <?php echo $attrs; ?>><?php echo $value; ?></textarea>
         </span>
         <?php
    }
}
