<?php

namespace app\core\form;

use app\core\Model;

class Form
{
    public static function startForm($action='',$method="POST") : Form
    {
       echo sprintf('<form action"%s" method="%s">', $action,$method);
        return new Form();
    }

    public static function endForm()
    {
        echo '</form>';
    }

    public function inputField(Model $model, $attribute)
    {
        return new InputField($model, $attribute);
    }
}