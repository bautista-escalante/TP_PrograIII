<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
class registrador {

    private $logger;
    public function __construct() {
        $this->logger = new Logger('registrador');
        $handler = new StreamHandler("modelo/ingreso.log", Logger::INFO);
        $output = "[%datetime%] %message%\n";
        $formatter = new LineFormatter($output, "Y-m-d H:i:s", true, true);
        
        $handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);
    }
    public function registarActividad($action) {
        $message = " [INFO] $action";
        $this->logger->info($message);
    }

    public function registrarError($error) {
        $message = " [ERROR] $error";
        $this->logger->error($message);
    }
}