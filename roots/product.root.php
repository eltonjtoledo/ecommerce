<?php
use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
use Hcode\Model\Product;

$app->get("/admin/products", function()
{
    User::verifyLogin();

    $products = Product::listAll();

    $page = new PageAdmin();
    $page->setTpl("products", [
        'products' => $products
    ]);
});

$app->get("/admin/products/create", function()
{
    User::verifyLogin();

    $page = new PageAdmin();
    $page->setTpl("products-create");
});

$app->post("/admin/products/create", function()
{
    User::verifyLogin();
    $products = new Product();
    $product = $_POST;

    $product['desurl'] = str_replace(' ', '-',strtolower($product['desproduct']));
    $products->setData($product);
    $products->save();

    header("Location: /admin/products");
    exit;
});

$app->get("/admin/products/update/:idproduct", function($idproduct)
{
    User::verifyLogin();
    $products = new Product();
    $products->get((int)$idproduct);

    $page = new PageAdmin();
    $page->setTpl("products-update", [
        'product' => $products->getValues()
    ]);

});

$app->post("/admin/products/update/:idproduct", function($idproduct)
{
    User::verifyLogin();
    
    $products = new Product();
    $products->get((int)$idproduct);
    $product = $_POST;

    $product['desurl'] = str_replace(' ', '-',strtolower($product['desproduct']));
    $products->setData($product);
    $products->update();
    $products->setPhoto($_FILES['file']);

    header("Location: /admin/products");
    exit;
});

$app->get("/admin/products/delete/:idproduct", function($idproduct){
    User::verifyLogin();
    $products = new Product();
    $products->get((int)$idproduct);
    $products->delete();
    header("Location: /admin/products");
    exit;
});