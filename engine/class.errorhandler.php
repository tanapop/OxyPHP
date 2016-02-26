<?php

class Errorhandler {

    private $exception;
    private $errortypes;

    public function __construct() {
        set_error_handler(array($this, "handler"), HANDLE_ERROR_TYPES);

        $this->errortypes = array(
            1 => 'Fatal Error',
            2 => 'Warning',
            4 => 'Parse Error',
            8 => 'Notice',
            16 => 'PHP Core Fatal Error',
            32 => 'PHP Core Warning',
            256 => 'Custom Fatal Error',
            512 => 'Custom Warning',
            1024 => 'Custom Notice',
            2048 => 'Strict',
            4096 => 'Recoverable Fatal Error',
            8192 => 'Deprecated',
            16384 => 'Custom Deprecated'
        );
    }

    public function handler($errno, $errstr, $errfile, $errline) {
        System::log('oxyerror', 'At ' . date("Y/m/d - H:i:s", mktime()) . ': ' . $this->errortypes[$errno] . ': ' . $errstr . ' In ' . $errfile . ' on line ' . $errline);
        $this->exception = System::loadClass($_SERVER['DOCUMENT_ROOT'] . 'engine/class.oxyerror.php', 'oxyerror', array($errfile, $errline, $this->errortypes[$errno] . ': ' . $errstr, $errno));
        throw $this->exception;
    }

}

?>