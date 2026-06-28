<?php

require_once ROOT_PATH . 'models/Enums/LogType.php';

class LogController
{
    private string $logFilePath;
    public function __construct()
    {
        $this->logFilePath = ROOT_PATH . 'log.log';
    }

    public function log(LogType $type, string $message, ?int $userId): void
    {
        $dir = dirname($this->logFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $userField = $userId ? "USER_ID: " . $userId : "GUEST";

        $date = (new DateTime())->format('Y-m-d H:i:s');
        $logLine = sprintf("[%s] [%s] [%s] || %s\n", $date, strtoupper($type->value), $userField, $message);

        file_put_contents($this->logFilePath, $logLine, FILE_APPEND | LOCK_EX);
    }
}