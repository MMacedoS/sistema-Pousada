<?php

namespace App\Utils;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerHelper
{
    private static $logger;

    // Inicializa o logger, caso ainda não tenha sido instanciado
    private static function initLogger()
    {
        if (is_null(self::$logger)) {
            self::$logger = new Logger('app_logger');
            $logFile = __DIR__ . '/../../Logs/app.log';
            self::$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));
        }
    }

    public static function logInfo($message)
    {
        self::initLogger();
        self::$logger->info($message);
    }

    public static function logError($message)
    {
        self::initLogger();
        self::$logger->error($message);
    }
}
