<?php

class FormBilder
{
    private $output = '';
    private $options;
    private $level;

    public function __construct($action, $withFile = false, $class = false, $id = false)
    {
        $this->buildStart($action, $withFile = $withFile, $class = $class, $id = $id);
    }

    public function buildStart($action, $withFile, $class, $id)
    {
        $this->output .= '<form action="' . $action . '" method="POST" class="form" ';
        if ($withFile) $this->output .= 'enctype="multipart/form-data"';
        $this->output .= '>';
        if ($withFile) $this->output .= ' <input type="file" name="upload_file">';
        if ($class) {
            if (file_exists('../uploads/' . $class . '/mini/' . $id . '.jpg')) $this->output .= '<img src="/uploads/' . $class . '/mini/' . $id . '.jpg?' . time() . '" >';
        }
    }

    public function buildForm($formItems)
    {
        If (!is_array($formItems)) throw new Exception("передан не массив");
        foreach ($formItems as $value) {
            if (is_array($value)) {
                $method = $value['type'];
                $this->output .= $this->$method($value);
            }
        }
    }

    public function drawForm()
    {
        $this->output .= '<br>
        <input type="submit" value="сохранить">
        </form>';
        return $this->output;
    }

    public function text($arrValue)
    {
        if (!isset($arrValue['id'])) $arrValue['id'] = $arrValue['name'];
        $template = '
        <label for="' . $arrValue['id'] . '">' . $arrValue['title'] . '</label>
        <input type="' . $arrValue['type'] . '" name="' . $arrValue['name'] . '"  value="' . $arrValue['value'] . '" id="' . $arrValue['id'] . '">';
        $this->output .= $template;
    }

    public function  textarea($arrValue)
    {
        if (!isset($arrValue['id'])) $arrValue['id'] = $arrValue['name'];
        $template = '
        <label for="' . $arrValue['id'] . '">' . $arrValue['title'] . '</label>
        <textarea name="' . $arrValue['name'] . '" id="' . $arrValue['id'] . '">';
        if (isset($arrValue['value'])) $template .= $arrValue['value'];
        $template .= '</textarea>';
        $this->output .= $template;
    }

    public function hidden($arrValue)
    {
        $template = '
        <input type="' . $arrValue['type'] . '" name="' . $arrValue['name'] . '" value="' . $arrValue['value'] . '">';
        $this->output .= $template;
    }

    public function ckeditor($arrValue)
    {
        $template = '<script src="./ckeditor/ckeditor.js"></script>';
        $template .= '<label>' . $arrValue['title'] . '</label>
        <textarea name="' . $arrValue['name'] . '" ';
        if (isset($arrValue['id'])) {$template .= ' id="' . $arrValue['id'] . '" ';}
        else {$template .= ' id="' . $arrValue['name'] . '" ';}
        $template .= '>';
        if (isset($arrValue['value'])) $template .= $arrValue['value'];
        $template .= '</textarea>';
        $template .= '<script>CKEDITOR.replace(' . $arrValue['name'] . ');</script>';
        $this->output .= $template;
    }


    public function date($arrValue)
    {
        if (!isset($arrValue['id'])) $arrValue['id'] = $arrValue['name'];
        $template = '
        <label for="' . $arrValue['id'] . '">' . $arrValue['title'] . '</label>
        <input type="' . $arrValue['type'] . '" name="' . $arrValue['name'] . '"  value="' . $arrValue['value'] . '" id="' . $arrValue['id'] . '" class="datepicker" >';
        $this->output .= $template;
    }

    public function select($val)
    {
        if (!isset($val['id'])) $val['id'] = $val['name'];
        $template = '
        <label for="' . $val['id'] . '">' . $val['title'] . '</label>
        <select name="' . $val['name'] . '" id="' . $val['id'] . '" class="form-control">
        <option value="0">-----</option>';
        $this->selectItem($val['selectItems'], $val['value']);
        $template .= $this->options;
        $template .= '</select>';
        $this->output .= $template;
    }

    /**
     * option для select
     * @param $arr
     * @param $value
     */
    public function selectItem($arr, $value)
    {
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                if (isset($val['id'])) {
                    $this->options .= '
            <option value="' . $val['id'] . '"';
                    if ($val['id'] == $value) {
                        $this->options .= ' selected ';
                    }
                    $this->options .= '>' . str_repeat('-', $this->level) . ' ' . @$val['name'] . '</option>';
                }

            }
        }
    }

}