<?php

/**
 * Description of User
 *
 * @author Elton J. Toledo
 */

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Product extends Model {

    public static function listAll() {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_products ORDER BY desproduct ASC");
        return $results;
    }

    public function save() {

        $sql = new Sql();

        $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl, :desphoto)", array(
            ":idproduct" => NULL, 
            ":desproduct" => $this->getdesproduct(), 
            ":vlprice" =>    $this->getvlprice(), 
            ":vlwidth" =>    $this->getvlwidth(), 
            "vlheight" =>    $this->getvlheight(), 
            ":vllength" =>   $this->getvllength(), 
            ":vlweight" =>   $this->getvlweight(), 
            ":desurl" =>     $this->getdesurl(),
            ":desphoto" => "LAST_INSERT_ID()"
        ));

        $this->setData($results[0]);
    }

    public function get($idProduct) {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", array(
            "idproduct" => $idProduct
        ));

        $this->setData($results[0]);
    }

    public function update() {
        $sql = new Sql();

        $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl, :desphoto)", array(
            ":idproduct" => $this->getidproduct(), 
            ":desproduct" => $this->getdesproduct(), 
            ":vlprice" =>    $this->getvlprice(), 
            ":vlwidth" =>    $this->getvlwidth(), 
            "vlheight" =>    $this->getvlheight(), 
            ":vllength" =>   $this->getvllength(), 
            ":vlweight" =>   $this->getvlweight(), 
            ":desurl" =>     $this->getdesurl(),
            ":desphoto" => $this->getidproduct().".jpg"
        ));

        $this->setData($results[0]);
    }

    public function delete() {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", array(
            ":idproduct" => $this->getidproduct()
        ));
    }
    
    public function getValues()
    {
        $this->checkimage();
        $value = parent::getValues();

        return $value;
    }
    public function checkimage()
    {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . 
        DIRECTORY_SEPARATOR . "site" .
        DIRECTORY_SEPARATOR . "img".
        DIRECTORY_SEPARATOR . "products".
        DIRECTORY_SEPARATOR . 
        $this->getidproduct() . ".jpg"))
        {
          $url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";
        }else{
          $url = "/res/site/img/product.jpg"; 
        }

        return $this->setdesphoto($url);
    }

    public function setPhoto($file)
    {
       $extencion = explode('.',$file['name']);
       $extencion = end($extencion);

       $addressImage = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . 
       DIRECTORY_SEPARATOR . "site" .
       DIRECTORY_SEPARATOR . "img".
       DIRECTORY_SEPARATOR . "products".
       DIRECTORY_SEPARATOR . 
       $this->getidproduct() . ".jpg";

       switch($extencion){
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
           break;
            case 'gif':
                $image = imagecreatefromgif($file['tmp_name']);
           break;
            case 'png':
                $image = imagecreatefrompng($file['tmp_name']);
           break;
       }

       imagejpeg($image, $addressImage);
       imagedestroy($image);
       $this->checkimage();
    }
}
