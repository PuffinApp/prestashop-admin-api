<?php

namespace Lollilop\Classes\Controller;

abstract class RestController extends \ModuleFrontController {

    public function __construct()
    {
        parent::__construct();

        $this->id_lang = (int)\Tools::getValue('id_lang', 1);
        $this->id_currency = (int)\Tools::getValue('id_currency', 1);
        $this->context->language = new \Language((int)$this->id_lang);
        $this->context->currency = new \Currency($this->id_currency);
        if (!$this->context->cart) 
            $this->context->cart = new \Cart();
    }

    public function init() {
        header('Content-Type:' . "application/json");

        parent::init();
    }

    public function run() {
        $request_method = $_SERVER['REQUEST_METHOD'];
    
        $this->dispatch($request_method);
    }

    private function dispatch($method) {
        switch ($method) {
            case "GET":
                $this->processGet();
                break;
            case "POST":
                $this->processPost();
                break;
            case "PUT":
                $this->processPut();
                break;
            case "DELETE":
                $this->processDelete();
                break;
        }
    }

    public function return404() {
        header("HTTP/1.0 404 Not Found");

        die();
    }
    
    protected function processGet() {}
    protected function processPost() {}
    protected function processPut() {}
    protected function processDelete() {}
}