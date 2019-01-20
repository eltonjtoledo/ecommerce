<?php

/**
 * Description of Page
 *
 * @author Elton J. Toledo
 */

namespace Hcode;

use Rain\Tpl;

class Page {

    private $tpl;
    private $options = [];
    private $defaults = [
          'header' => true,
        'footer' => true,
        "data" => []
    ];

    public function __construct($opts = array(), $tpl_dir = "/views/") {
        $this->options = array_merge($this->defaults, $opts);

// config
        $config = array(
            "tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . $tpl_dir,
            "cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/",
            "debug" => false
        );

        Tpl::configure($config);
        $this->tpl = new Tpl;

        $this->setData($this->options["data"]);
        ($this->options['header'] === true ? $this->tpl->draw("header") : '');
        
    }

    public function setTpl($name, $data = array(), $returnHtml = false) {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHtml);
    }

    public function setData($data = array()) {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }

    public function __destruct() {
        ($this->options['footer'] === true ? $this->tpl->draw("footer"): '');
    }

    public function setSingleTpl($name, $data = array(), $returnHtml = false) {
       $this->setData($data);
        return $this->tpl->draw($name, $returnHtml);
    }

}
