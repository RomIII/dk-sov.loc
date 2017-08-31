<?php

class Mail
{
    public $moduleName = 'mail';
    public $content;
    public $capchaKey = '';
    public $arrValue=array();

    public $to;
    public $subject;
    public $body;

    function __construct()
    {
        if ($this->capchaKey == '') $this->capchaKey = CAPTCHA_KEY;
    }

    public function showForma()
    {
        $content = '
        <form name=form1 method=post action=./' . $this->moduleName . '.php>
          <table border=0 cellspacing=3 cellpadding=3 class=text>
            <tr>
              <td width=60>Имя</td>
              <td ><input name=name type=text size=40 maxlength=40 value="';
        if(array_key_exists('name',$this->arrValue))$content.=$this->arrValue['name'];
        $content.='"></td>
            </tr>
            <tr>
              <td>обратная связь</td>
              <td><input name=contact type=text id=mail size=40 maxlength=40 value="';
        if(array_key_exists('contact',$this->arrValue))$content.=$this->arrValue['contact'];
        $content.='"></td>
            </tr>
            <tr>
              <td valign="top">текст</td>
              <td>
                <textarea name=text cols=40 rows=5>';
        if(array_key_exists('text',$this->arrValue))$content.=$this->arrValue['text'];
        $content.='</textarea> </td>
            </tr>
            <tr>
              <td></td>
              <td><div class="g-recaptcha" data-sitekey="' . $this->capchaKey . '"></div></td>
            </tr>
            <tr>
              <td >&nbsp;</td>
              <td ><input type=submit name=Submit value=Отправить></td>
            </tr>
          </table>
        </form>';
        return $content;
    }

    public function send(){
         //$from = '=?utf-8?B?'.base64_encode($from).'?=';
        $subj = '=?utf-8?B?' . base64_encode($this->subject) . '?=';
        //$subj = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subj, "w","k")).'?=';

        $head = "From: MyDmitrov.ru\n";
        $head .= "To: $this->to\n";
        $head .= "Subject: $this->subject\n";
        $head .= "X-Mailer: PHPMail Tool\n";
        $head .= "Mime-Version: 1.0\n";
        $head .= "Content-Transfer-Encoding: 8bit\n";
        $head .= "Content-Type: text/html; charset=UTF-8\n";
        $head .= "Date: " . gmdate("D, d M Y H:i:s", time()) . " +0400\n";

        mail($this->to, $this->subject, $this->body, $head);
    }



}