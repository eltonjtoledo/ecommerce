<?php

/**
 * Description of User
 *
 * @author Elton J. Toledo
 */

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\User;
use \Hcode\Mailer;

class User extends Model {

    const SESSION = "User";
    const SECRET = 'aplicacaocomerce'; //Senha de Cryptografia Do Login *****PRECISA SER ALTERADA
    const SECRET_IV = 'aplicacaocomerce'; //Senha de Cryptografia Do Login *****PRECISA SER ALTERADA

    public static function login($login, $senha) {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN" => $login
        ));

        if (count($results) === 0) {
            throw new \Exception("Usuario inexistente ou senha inválida");
        }

        $data = $results[0];

        if (password_verify($senha, $data['despassword']) === true) {
            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();

            return $user;
        } else {
            throw new \Exception("Usuario inexistente ou senha inválida");
        }
    }

    public static function logout() {
        unset($_SESSION[User::SESSION]);
    }

    public static function verifyLogin($inadmin = true) {
        if (
                !isset($_SESSION[User::SESSION]) ||
                !$_SESSION[User::SESSION] ||
                !(int) $_SESSION[User::SESSION]["iduser"] > 0 ||
                (bool) $_SESSION[User::SESSION]['inadmin'] !== $inadmin
        ) {
            header("Location: /admin/login");
            exit;
        }
    }

    public static function listAll() {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson ASC");
        return $results;
    }

    public function save() {

        $sql = new Sql();

        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function get($iduser) {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            "iduser" => $iduser
        ));

        $this->setData($results[0]);
    }

    public function update() {
        $sql = new Sql();

        $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser" => $this->getiduser(),
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function delete() {
        $sql = new Sql();
        $sql->query("CALL sp_users_delete(:iduser)", array(
            ":iduser" => $this->getiduser()
        ));
    }

    public static function getForgot($email) {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", array(
            ":email" => $email
        ));

        if (count($results) == 0):
            throw new \Exception("Não foi possivel recuperar a senha!");
        else:
            $data = $results[0];

            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser" => $data['iduser'],
                ":desip" => $_SERVER['REMOTE_ADDR']
            ));

            if (count($results2) == 0):
                throw new \Exception("Não foi possivel recuperar a senha!");
            else:
                $dataRecovery = $results2[0];
                $code = base64_encode(openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', User::SECRET, 0, User::SECRET_IV));
                
                $link = "http://ecommerce.com/admin/forgot/reset?code=$code";
                
                $mailer = new Mailer($data['desemail'], $data['desperson'], 'Redefinir senha Hcoce Store', 'forgot', array(
                    'name' => $data['desperson'],
                    'link' => $link
                ), "Para visualizar Esta Mensagens Acesse https://ecommerce.com");
                
                $mailer->sendMail();
            endif;
        endif;
    }
    
    public static function validForgotDecrypt($code){
      $idRecovery = openssl_decrypt(base64_decode($code), 'AES-128-CBC', User::SECRET, 0, User::SECRET_IV);
      
      $sql = new Sql();
      $results = $sql->select("select * from tb_userspasswordsrecoveries a inner join tb_users b using(iduser) inner join tb_persons c using(idperson) where a.idrecovery = :idRecovery and dtrecovery is null and date_add(a.dtregister, interval 1 hour) >= now();", array(
          ':idRecovery' => $idRecovery
      ));
      
      if(count($results) == 0):
          throw new \Exception("Não foi possivel recuperar a senha.");
      else:
          return $results[0];
      endif;
    }

    public static function setForgotUsed($idrecovery) {
        $sql = new Sql();
        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery",array(':idrecovery' => $idrecovery));
        
    }
    
    public function setPassword($password){
        $sql = new Sql();
        $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
            ':password' => $password,
            ':iduser' => $this->getiduser()
        ));
    }
}
