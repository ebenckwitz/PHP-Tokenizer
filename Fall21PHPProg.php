<?php

include 'tokenizerPHP.php';
include 'tokenPHP.php';
include 'EvalSectionException.php';

class Fall21PHPProg
{
    public $map;
    public $currentToken;// = new tokenPHP();
    public $result;
    public $oneIndent = "   ";
    public $value;
	public $t;// = new tokenizerPHP();

    function main()
    {
        $html = file_get_contents('http://localhost/fall21Testing.txt');  
        $header = "<html>" . PHP_EOL . "  <head>" . PHP_EOL . "    <title>CS 4339/5339 PHP assignment</title>" . PHP_EOL . "  </head>" . PHP_EOL . "  <body>" . PHP_EOL . "    <pre>";
        $footer = "    </pre>" . PHP_EOL . "  </body>" . PHP_EOL . "</html>";

        echo $header . PHP_EOL;
		$this->t = new tokenizerPHP($html);
		$this->currentToken= $this->t->nextToken();
        $section = 0;

        // Loop through all sections, for each section printing result
        // If a section causes exception, catch and jump to next section
        while ($this->currentToken->type !== TokenType::EOF)
        {
            echo "section " . ++$section. PHP_EOL;
            try
            {
                $this->evalSection();
                echo "Section result:" .PHP_EOL;
                echo $this->result . PHP_EOL;                
            }
            catch(EvalSectionException $ex)
            {
                // skip to the end of section
                while ($this->currentToken->type !== TokenType::RSQUAREBRACKET && $this->currentToken->type !== TokenType::EOF)
                {
                    $this->currentToken = $this->t->nextToken();
                }
                $this->currentToken = $this->t->nextToken();
            }
        }

        echo $footer . PHP_EOL;

    }

    function evalSection()
    {
        //global $charArr, $i, $map, $result, $currentToken, $t;
        // <section> ::= [ <statement>* ]
        $this->result = "";
		$this->map = array();
        if ($this->currentToken->type !== TokenType::LSQUAREBRACKET)
        {
            throw new EvalSectionException("A section must start with \"[\"" .PHP_EOL);
        }
        echo "[" . PHP_EOL;
        $this->currentToken = $this->t->nextToken();
        while ($this->currentToken->type !== TokenType::RSQUAREBRACKET && $this->currentToken->type !== TokenType::EOF)
        {
			$this->evalStatement($this->oneIndent, true);
        }
        echo "]" . PHP_EOL;
        $this->currentToken = $this->t->nextToken();
    }

    function evalStatement($indent, $exec) 
    {
        // exec it true if we are executing the statements in addition to parsing
        // <statement> ::= STRING | <assignment> | <conditional>
        switch ($this->currentToken->type)
        {
            case "ID":
                $this->evalAssignment($indent, $exec);
				break;
            case "IFS":
                $this->evalConditional($indent, $exec);
				break;
            case "STRINGS":
                if ($exec)
                {
                    $this->result .= $this->currentToken->value . PHP_EOL;
                }
                echo $indent . "\"" . $this->currentToken->value . "\"" . PHP_EOL;
                $this->currentToken = $this->t->nextToken(); 
				break;
            default:
                throw new EvalSectionException("invalid statement" .PHP_EOL);
        }
    }

    function evalAssignment($indent, $exec) 
    {
        //global  $value, $map, $currentToken, $t;
        // <assignment> ::= ID '=' INT
        // we know charArr[$i] is ID
        $key = $this->currentToken->value;
        echo $indent . $key;
        $this->currentToken = $this->t->nextToken();
        if ($this->currentToken->type !== TokenType::EQUAL)
        {
            throw new EvalSectionException("equal sign expected" .PHP_EOL);
        }
        echo "=";
        $this->currentToken = $this->t->nextToken();
        if ($this->currentToken->type !== TokenType::INTS)
        {
            throw new EvalSectionException("integer expected" .PHP_EOL);
        }
		$val = intval($this->currentToken->value);
        echo $val . PHP_EOL;
        $this->currentToken = $this->t->nextToken();
       /* echo "Current Token" . $this->currentToken->type . PHP_EOL;
		echo "Should exec" . $exec . PHP_EOL;
		echo "Key: " . $key . PHP_EOL;*/
		if ($exec)
        {
		   $this->map[$key] = $val;
        }
		//print_r($map);
    }

    function evalConditional($indent, $exec)
    {
        //global $charArr, $i, $currentToken, $t;
        // <conditional> ::= 'if' <condition> '{' <statement>* '}' [ 'else' '{'
        // We know charArr[$i] is "if"
        echo $indent . "if ";
        $this->currentToken = $this->t->nextToken();
		$trueCondition = $this->evalCondition($exec);

        if ($this->currentToken->type !== TokenType::LBRACKET)
        {
            throw new EvalSectionException("left bracket extected" .PHP_EOL);
        }
        echo " {" .PHP_EOL;
        $this->currentToken = $this->t->nextToken();
        while ($this->currentToken->type !== TokenType::RBRACKET && $this->currentToken->type !== TokenType::EOF)
        {
            if ($trueCondition)
            {
                $this->evalStatement($indent . $this->oneIndent, $exec);
            }
            else
            {
                $this->evalStatement($indent . $this->oneIndent, false);
            }
        }
        if ($this->currentToken->type === TokenType::RBRACKET)
        {
            echo $indent . "}" . PHP_EOL;
            $this->currentToken = $this->t->nextToken();
        }
        else
        {
            throw new EvalSectionException("right bracket expected" .PHP_EOL);
        }
        if ($this->currentToken->type === TokenType::ELSES)
        {
            echo $indent . "else";
            $this->currentToken = $this->t->nextToken();
            if ($this->currentToken->type !== TokenType::LBRACKET)
            {
                throw new EvalSectionException("left bracket expected" .PHP_EOL);
            }
            echo " {" . PHP_EOL;
            $this->currentToken = $this->t->nextToken();
            while ($this->currentToken->type !== TokenType::RBRACKET && $this->currentToken->type !== TokenType::EOF)
            {
                if ($trueCondition)
                {
                    $this->evalStatement($indent . $this->oneIndent, false);
                }
                else
                {
                    $this->evalStatement($indent . $this->oneIndent, $exec);
                }
            }
            if ($this->currentToken->type === TokenType::RBRACKET)
            {
                echo $indent . "}". PHP_EOL;
                $this->currentToken = $this->t->nextToken();
            }
            else
            {
                throw new EvalSectionException("right bracket expected" .PHP_EOL);
            }
        }
    }

    function evalCondition($exec) 
    {
        //global $charArr, $i, $value, $map, $currentToken, $t;
        // <condition> ::= ID ('<' | '>' | '=') INT
        $v1 = null; // value associated with ID
        if ($this->currentToken->type !== TokenType::ID)//TokenType::ID)
        {
            throw new EvalSectionException("identifier expected" .PHP_EOL);
        }
        $key = $this->currentToken->value;
        echo $key;
       /* echo "Current Token" . $this->currentToken->type . PHP_EOL;
		echo "Should exec" . $exec . PHP_EOL;
		echo "Key: " . $key . PHP_EOL;*/
        if ($exec)
        {
            if (array_key_exists($key, $this->map)) 
			{
                $v1 = $this->map[$key];
            }
            else 
			{
				$v1 = null;
            }
			if ($v1 === null)
			{
				throw new EvalSectionException("undefined variable" . PHP_EOL);
			}
        }
        $this->currentToken = $this->t->nextToken();
        $operator = $this->currentToken->type;
        if ($this->currentToken->type !== TokenType::EQUAL && $this->currentToken->type !== TokenType::LESS && $this->currentToken->type !== TokenType::GREATER)
        {
            throw new EvalSectionException("comparison operator expected" .PHP_EOL);
        }
        echo $this->currentToken->value;
        $this->currentToken = $this->t->nextToken();
        if ($this->currentToken->type !== TokenType::INTS)
        {
            throw new EvalSectionException("integer expected" .PHP_EOL);
        }
		$value = intval($this->currentToken->value);
		echo $value . " ";
        $this->currentToken = $this->t->nextToken();
        // compute return value
        if (!$exec)
        {
            return false;
        }
        $trueResult = false;
        switch ($operator)
        {
            case tokenType::LESS:
                $trueResult = $v1 < $value;
            break;
            case tokenType::GREATER:
                $trueResult = $v1 > $value;
            break;
            case tokenType::EQUAL:
                $trueResult = $v1 === $value;
        }
        return $trueResult;
    }
}
$call = new Fall21PHPProg();
$call->main();

?>
