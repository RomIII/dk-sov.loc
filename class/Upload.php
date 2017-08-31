<?php


class Upload
{

    public $path = '';
    public $miniSize = array(100, 100);
    public $normalSize = array(500, 500);

    public function uploadFile($arrValue, $name = 'noname')
    {
        $file = $arrValue['upload_file'];
        if (preg_match("/jpg|jpeg|png|gif/i", $file['name'], $file['type'])) {
            chmod($this->path, 0777);
            $filePath = strtolower($this->path . '/tmp/' . $name . '.' . @$file['type'][1]);
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $this->resizeImg($filePath, $this->path . '/mini/' . $name . '.jpg', $this->miniSize[0], $this->miniSize[1]);
                $this->resizeImg($filePath, $this->path . '/' . $name . '.jpg', $this->normalSize[0], $this->normalSize[1]);
                unlink($filePath);
            }
            chmod($this->path . '/mini/' . $name . '.jpg', 0644);
            chmod($this->path . '/' . $name . '.jpg', 0644);
            chmod($this->path, 0755);
        }
    }

    public function resizeImg($filename, $smallimage, $w, $h)
    {
        $ratio = $w / $h;
        $size_img = getimagesize($filename);
        if (($size_img[0] < $w) && ($size_img[1] < $h)) {
            copy( $filename, $smallimage );
            return true;
        }
        $src_ratio = $size_img[0] / $size_img[1];
        if ($ratio < $src_ratio) {
            $h = $w / $src_ratio;
        } else {
            $w = $h * $src_ratio;
        }
        $dest_img = imagecreatetruecolor($w, $h);
        if ($size_img[2] == 2) $src_img = imagecreatefromjpeg($filename);
        else if ($size_img[2] == 1) $src_img = imagecreatefromgif($filename);
        else if ($size_img[2] == 3) $src_img = imagecreatefrompng($filename);
        imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);
        if ($size_img[2] == 2) imagejpeg($dest_img, $smallimage);
        else if ($size_img[2] == 1) imagegif($dest_img, $smallimage);
        else if ($size_img[2] == 3) imagepng($dest_img, $smallimage);
        imagedestroy($dest_img);
        imagedestroy($src_img);
        return true;
    }

    public function setPath($val)
    {
        $this->path = $val;
    }

    public function setMiniSize($val)
    {
        $this->miniSize = $val;
    }

    public function setNormalSize($val)
    {
        $this->normalSize = $val;
    }

    public function showSuccess($folder,$gallery_id, $name)
    {
        echo '<li>
                <table>
                <tr>
                <td width=160><img src="/uploads/'.$folder.'/' . $gallery_id . '/' . $name . '.jpg" class="img-responsive" width="100"></td>
                <td><button type="button" photo_id="' . $name . '" id="' . $gallery_id . '" class="close remove-button form-control-static" aria-label="Close" onClick="javascript: return false;"><span aria-hidden="true">&times;</span></button></td>
                </tr>
                </table>
                 </li>';
    }
}

