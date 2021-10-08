<?php


class tokenizerPHP {
	
	public $charArr;
	public $i;
	
	function __construct($s)
	{
		$this->charArr = str_split($s);
		$this->i = 0;
	}
	
	
	function nextToken()
    {
        // skip blanklike characters
        while ($this->i < count($this->charArr) && strpos(" \n\t\r", $this->charArr[$this->i]) > -1)
        {
            $this->i++;
        }
        if ($this->i >= sizeof($this->charArr))
        {
            $token = new Token(TokenType::EOF, "");
			return $token;
            
        }

        // check for INT
        $inputString = "";
        while ($this->i < count($this->charArr) && strpos("0123456789", $this->charArr[$this->i]) > -1)
        {
            $inputString .= $this->charArr[$this->i++];
        }
        if ("" !== $inputString)
        {
            $value = $inputString;
			$token = new Token(TokenType::INTS, $value);
            return $token;
            
        }

        // check for ID or reserved word
        while ($this->i < count($this->charArr) && strpos("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_", $this->charArr[$this->i]) > -1)
        {
            $inputString .= $this->charArr[$this->i++];
        }
        if ("" !== $inputString)
        {
            if ("if" === $inputString)
            {
                $token = new Token(TokenType::IFS, "");
                return $token;
            }
            if ("else" === $inputString)
            {
                $token = new Token(TokenType::ELSES, "");
                return $token;
            }
            $value = $inputString;
			$token = new Token(TokenType::ID, $value);
            return $token;
            
        }

        switch ($this->charArr[$this->i++])
        {
            case "{":
				$token = new Token(TokenType::LBRACKET, "{");
				return $token;
                
            case "}":
			$token = new Token(TokenType::RBRACKET, "}");
				return $token;
                
            case "[":
				$token = new Token(TokenType::LSQUAREBRACKET, "[");
				return $token; 
                
            case "]":
				$token = new Token(TokenType::RSQUAREBRACKET, "]");
				return $token;
                
            case "<":
				$token = new Token(TokenType::LESS, "<");
				return $token;
                
            case ">":
				$token = new Token(TokenType::GREATER, ">");
				return $token;
                
            case "=":
				$token = new Token(TokenType::EQUAL, "=");
				return $token;
                
            case '"':
                $value = "";
                while ($this->i < count($this->charArr) && $this->charArr[$this->i] !== '"')
                {
                    $c = $this->charArr[$this->i++];
                    if ($this->i >= count($this->charArr))
                    {
                        $token = new Token(TokenType::OTHER, "");
                        return $token;
                    }
                    // check for escaped double quote
                    if ($c === '\\' && $this->charArr[$this->i] === '"')
                    {
                        $c = '"';
                        $this->i++;
                    }
                    $value .= $c;
                }
                $this->i++;
				$token = new Token(TokenType::STRINGS, $value);
                return $token;
                
            default:
                // OTHER should result in exception
                $token = new Token(TokenType::OTHER, "");                
				return $token;
        }        
    }
}
//$call = new tokenizerPHP();
//$call->nextToken();

?>
