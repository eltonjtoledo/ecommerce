<?php
/**
 * @author Elton J. Toledo
 */

Namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Category{
       
    public static function listAll() {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
        return $results;
    }

    public static function save($data = array())
    {
       $sql = new Sql;
       $sql->query("INSERT INTO tb_categories(descategory)VALUES(:category)", array(
           ':category' => $data['descategory']
       ));
       Category::updateFile();  
    }

    public function getCategory($idCategory)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
            ":idcategory" => $idCategory
        ));
        return $results[0];
    }

    public static function Update(int $idCategory, $data = array())
    {
        $sql = new Sql;
        $result = $sql->query('Call sp_categories_save(:idcategory, :dtcategory)', array(
            ':idcategory' => $idCategory,
            'dtcategory' => $data['descategory']
        ));
        Category::updateFile();
    }

    public static function delete($idCategory)
    {
        $sql = new Sql;
        $result = $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", array(
            ':idcategory' => $idCategory
        ));
        Category::updateFile();
    }

    public static function updateFile()
    {
        $categories = Category::listAll();
        $html = [];
        foreach ($categories as $row) {
           array_push($html, '<li><a href="/categoria/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR . "views". DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));
    }

}