<?php


class Token {
	public $type;
	public $value;
	
	function __construct($theType, $theValue) {
		$this->type = $theType;
        $this->value = $theValue;
	}
	

}	
abstract class TokenType {
	const LBRACKET = "LBRACKET";
    const RBRACKET = "RBRACKET";
    const LSQUAREBRACKET = "LSQUAREBRACKET";
    const RSQUAREBRACKET = "RSQUAREBRACKET";
    const STRINGS = "STRINGS";
    Const EQUAL = "EQUAL";
    Const LESS = "LESS";
    Const GREATER = "GREATER";
    Const IFS = "IFS";
    Const ELSES = "ELSES"; 
    Const ID = "ID";
	Const INTS = "INTS";
	Const EOF = "EOF";
    Const OTHER = "OTHER";
}

?>
