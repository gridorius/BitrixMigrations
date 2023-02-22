<?php

class MigrationLogger
{
    protected $mainMessage;

    public function __construct($mainMessage)
    {
        $this->mainMessage = $mainMessage;
        echo $mainMessage;
    }

    public function showProgress($current, $total){
        echo "\r{$this->mainMessage} [{$current}/{$total}]";
    }

    public function close(){
        echo PHP_EOL;
    }

    public function showError($message){
        echo $message.PHP_EOL;
    }
}