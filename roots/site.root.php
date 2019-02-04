<?php
use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
use Hcode\Model\Product;

$app->get('/', function() {
   $products = Product::listAll();

    $page = new Page();
    $page->setTpl("index", [
        "products" => $products
    ]);
});

$app->get('/categoria/:idcategory', function($idcategory) {
    $categories = new Category;

    $category = $categories->getCategory($idcategory);
    $listCategory = Category::listAll();
    
    $page = new Page();
    $page->setTpl("category",[
        "categoria" => $category,
        "products" => $listCategory
    ]);
});
