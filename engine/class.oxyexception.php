<?php

class Oxyexception extends Exception {

    public function __construct($file, $line, $message, $code, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->file = $file;
        $this->line = $line;
    }

}

?>
