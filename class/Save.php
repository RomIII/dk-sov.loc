<?php

class Save
{

    public static function number($val)
    {
        return preg_replace("/[^0-9.,]/i", "", $val);
    }

    public static function text($val)
    {

        @$val = trim($val);
        $val = strip_tags($val); //убираем HTML теги
//----$val=htmlentities($val); //преобразуем HTML символы в их сущности  ---TODO портит кирилицу
        $quotes = array("\x27", "\x22", "\x60", "*", "%", "<", ">");
        $val = str_replace($quotes, '', $val);
//---$val = preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $val) ? die('Error!') : $val;

        return $val;
    }

    public static function text_html($val)
    {
//TODO реализовать метод
        $val = trim($val);
        // $val = htmlentities($val); //преобразуем HTML теги в их сущности ---
        return $val;
    }

    //текст для поиска по сайту
    public static function textSearch($val)
    {

        @$val = trim($val);
        $val = strip_tags($val); //убираем HTML теги
//----$val=htmlentities($val); //преобразуем HTML символы в их сущности  ---TODO портит кирилицу
        $quotes = array("\x27", "\x22", "\x60", "*", "%", "<", ">");
        $val = str_replace($quotes, '', $val);
//---$val = preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $val) ? die('Error!') : $val;

        return $val;
    }

    public static function date($val){
        $months = array('', 'Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь', 'Декабрь');
        $val= explode(' ',$val);
        $d=explode('-',$val[0]);
        $val=$d[2].'.'.$d[1].'.'.$d[0];
        return $val;
    }

    public static function duration_date($d1,$d2){
        $d1=strtotime($d1);
        $d2=strtotime($d2);
        $months = array('', 'января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября', 'декабря');
        $day1=date('j',$d1);
        $mon1=date('n',$d1);

        if($d2==0)
        {
            $d="$day1 $months[$mon1]";
        }
        else
        {
            $day2=date('j',$d2);
            $mon2=date('n',$d2);
            if($mon1==$mon2){$d="$day1 - $day2 $months[$mon2]";} else {$d="$day1 $months[$mon1] - $day2 $months[$mon2]";}
        }
        return $d;
    }
    /*
     function _filter($db, $val, $sql, $length)
    {
        $val = strip_tags($val);
        $val = preg_replace('#<script***91;^>***93;*>.*?</script>#is', '', $val);
        $val = preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $val) ? die('Error!') : $val;
        $val = trim($val);
        $val = str_replace("\n", " ", $val);
        $val = str_replace("\r", "", $val);
        $val = htmlentities($val);
        $val = substr($val, 0, $length);
        if ($sql == 'str') $val = mysqli_real_escape_string($db, $val);
        else if ($sql == 'int') $val = intval($val);
        else die('Error!');
        return $val;
    }

    В своём скрипте я использую функцию, которая удаляет нежелательные мне символы из поиска:
    function strip_data($text)
{
    $quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!" );
    $goodquotes = array ("-", "+", "#" );
    $repquotes = array ("\-", "\+", "\#" );
    $text = trim( strip_tags( $text ) );
    $text = str_replace( $quotes, '', $text );
    $text = str_replace( $goodquotes, $repquotes, $text );
    $text = ereg_replace(" +", " ", $text);

    return $text;
}
 */

}