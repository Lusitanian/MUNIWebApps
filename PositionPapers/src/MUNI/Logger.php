<?php
namespace MUNI;

class Logger
{
    private $logFile;

    public function __construct($logFile)
    {
        $this->logFile = $logFile;
        if (false === touch($logFile)) {
            throw new \RuntimeException("Could not open log file $logFile");
        }
    }

    public function log($message)
    {
        $message = '['.date('UTC').'] '.$message;
        file_put_contents($this->logFile, $message."\r\n", FILE_APPEND);
    }
}
