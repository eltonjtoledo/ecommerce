<?php
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
$app->get("/admin/categories", function()
{
    User::verifyLogin();
    $categories = Category::listAll();

    $page = new PageAdmin();
    $page->setTpl("categories", [
        'categories' => $categories
    ]);
});

$app->get("/admin/categories/create", function()
{
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("categories-create");
});

$app->post("/admin/categories/create",function()
{
    User::verifyLogin();
    $dados = $_POST;
    Category::save($dados);
    
    header('Location: /admin/categories');
    exit;
});

$app->get("/admin/categories/update/:idcategory", function($idcategory)
{
    User::verifyLogin();

    $categories  = new Category();
    $category = $categories->getCategory((int) $idcategory);
    $page = new PageAdmin();
    
     $page->setTpl("categories-update", [
         'category' => $category
     ]);
     exit;
});


$app->post("/admin/categories/update/:idcategory", function($idcategory)
{
    User::verifyLogin();
    $data = $_POST;
    Category::Update($idcategory,$data);

    header('Location: /admin/categories');
    exit;
});

$app->get("/admin/categories/:idcategory/delete", function($idcategory)
{
    User::verifyLogin();

    Category::delete($idcategory);
    
    header('Location: /admin/categories');
    exit;
});