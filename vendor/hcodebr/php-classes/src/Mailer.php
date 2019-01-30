<?php

namespace Hcode;

/**
 * Description of Mailer
 *
 * @author elton
 */
use Rain\Tpl;

class Mailer {

    const HOST_MAIL = 'Host';
    const USER = 'Host_User';
    const PASS = 'Host_Password';
    const NAME_FROM = 'Elton John';
    const PORT = '587';

    private $Mail;

    public function __construct($toAddress, $toName, $subject, $tplName, $data = array(), $msgAlt) {

        $config = array(
            "tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views/email/",
            "cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/",
            "debug" => false
        );

        Tpl::configure($config);
        $tpl = new Tpl;
        
        foreach ($data as $key => $value) {
             $tpl->assign($key, $value);
        }
        
        $html = $tpl->draw($tplName, true);

        $this->Mail = new \PHPMailer();                              // Passing `true` enables exceptions
        //Server settings
        $this->Mail->SMTPDebug = 0;
        $this->Mail->isSMTP();                                      // Set mailer to use SMTP
        $this->Mail->Host = Mailer::HOST_MAIL;                // Specify main and backup SMTP servers
        $this->Mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->Mail->Username = Mailer::USER;    // SMTP username
        $this->Mail->Password = Mailer::PASS;             // SMTP password
        $this->Mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->Mail->Port = Mailer::PORT;                                    // TCP port to connect to
        //Recipients
        $this->Mail->setFrom(Mailer::USER, Mailer::NAME_FROM);
        $this->Mail->addAddress($toAddress, $toName);     // Add a recipient
        //Content
        $this->Mail->isHTML(true);                                  // Set email format to HTML
        $this->Mail->Subject = $subject;
        $this->Mail->msgHTML($html);
        $this->Mail->AltBody = $msgAlt;
    }

    public function sendMail() {
        try {
            $this->Mail->send();

            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $this->Mail->ErrorInfo;
        }
    }

}
