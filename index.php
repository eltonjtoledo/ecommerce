<?php

session_start();
require_once("vendor/autoload.php");

use Slim\Slim;
use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
use Hcode\Model\Product;

$app = new Slim;

$app->config('debug', true);

$app->get('/', function() {

    $page = new Page();
    $page->setTpl("index");
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

$app->get('/admin', function() {

    User::verifyLogin();

    $page = new PageAdmin();
    $page->setTpl("index");
});

$app->get('/admin/login', function() {

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);
    $page->setTpl("login");
});

$app->get('/admin/logout', function() {
    User::logout();
    header("Location: /admin/login");
    exit;
});

$app->post('/admin/login', function() {
    User::login($_POST['deslogin'], $_POST["despassword"]);

    header("Location: /admin");
    exit;
});

$app->get('/admin/users/:iduser/delete', function($iduser) {
    User::verifyLogin();
    
    $user = new User();
    $user->get((int)$iduser);
    
    $user->delete();
        
    header("Location: /admin/users");
    exit;
});

$app->get('/admin/users', function() {
    User::verifyLogin();
   $users = User::listAll();
    $page = new PageAdmin();
    $page->setTpl("users", array(
        "users"=>$users
    ));
});


$app->get('/admin/users/create', function() {
    User::verifyLogin();

    $page = new PageAdmin();
    $page->setTpl("users-create");
});

$app->get('/admin/users/:iduser', function($iduser) {
    User::verifyLogin();
    $user = new User();
    
    $user->get((int)$iduser);
    
    $page = new PageAdmin();
    $page->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
    
});

$app->post("/admin/users/create", function() {
    User::verifyLogin();
    $user = new User();
    
    $_POST['inadmin'] = (isset($_POST['inadmin']) ? $_POST['inadmin'] : 0);
    $_POST['despassword'] = password_hash($_POST['despassword'], PASSWORD_DEFAULT, ["cost" => 12]);
    
    $user->setData($_POST);;
    $user->save();
    
    header("Location: /admin/users");
    exit;
});

$app->post('/admin/users/:iduser', function($iduser) {
    User::verifyLogin();
    
    $user = new User();
    
    $_POST['inadmin'] = (isset($_POST['inadmin']) ? $_POST['inadmin'] : 0);
    
    $user->get((int)$iduser);
    $user->setData($_POST);
    $user->update();  
    
    header("Location: /admin/users");
    exit;
});

$app->get("/admin/forgot", function() {
    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);
    $page->setTpl("forgot");
});

$app->post("/admin/forgot", function(){
   $user = User::getForgot($_POST['email']);
   
   header("Location: /admin/forgot/sent");
   exit;
});

$app->get("/admin/forgot/sent", function() {
    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);
    $page->setTpl("forgot-sent");
});

$app->get('/admin/forgot/reset', function(){
    $user = User::validForgotDecrypt($_GET['code']);
    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);
    $page->setTpl("forgot-reset", array(
        "name"=>$user['desperson'],
        "code"=>$_GET['code']
    ));
});

$app->post('/admin/forgot/reset', function()
{
    $forgot = User::validForgotDecrypt($_POST['code']);
    
    User::setForgotUsed($forgot['idrecovery']);
    
    $user = new User();
    
    $user->get((int)$forgot['iduser']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT, ["cost" => 12]);
    
    $user->setPassword($password);
    
    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);
    
    $page->setTpl("forgot-reset-success");
});

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

$app->run();
?>