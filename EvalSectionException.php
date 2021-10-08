<?php

class EvalSectionException extends Exception {
	
	function __construct($m) {
		echo PHP_EOL . "Parsing or execution Exception: " . $m . PHP_EOL;
	}
	
}

?>
