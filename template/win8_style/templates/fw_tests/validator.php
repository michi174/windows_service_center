<?php 
use wsc\validator\NotEmpty;
use wsc\application\Application;
use wsc\validator\StringLength;
use wsc\validator\Between;
use wsc\validator\ValidatorChain;


$valid		= array(
		"string"	=> "Testtext",
		"int"		=> 10,
		"float"		=> 23.15,
		"bool"		=> true,
		"array"		=> array("nicht leer"),
		"object"	=> Application::getInstance()
);
$invalid	= array(
		"string"	=> "",
		"int"		=> 0,
		"float"		=> 0.0,
		"bool"		=> false,
		"array"		=> array(),
		"object"	=> Application::getInstance()
);



function getOutput($valid, $value, $messages = NULL)
{
	$valid_str	= ($valid === true) ? "valide" : "invalide";
	$css_class	= ($valid === true) ? "success" : "error";
	
	echo "<div class=\"output $css_class\">";
	
		echo "(".gettype($value).") &rsquo;". $value . "&rsquo; ist ".$valid_str;
		
	if($valid === false && !is_null($messages))
	{
		foreach ($messages as $message)
		{
			echo " (".$message.")";
		}
	}
	echo "</div>";
}
?>
	<h2 class="step_line" style="width:260px;">Die Validator Klasse</h2>
	<h4>Allgemein:</h4>
	<p>
        Folgende Validatoren sind vorhanden:<br>
        <a href="#not-empty" class="output code small">NotEmpty</a>
        <a href="#stringlength" class="output code small">Stringlength</a>
        <a href="#between" class="output code small">Between</a>
        <br /><a href="#chain" class="output code small">Verkettung von Validatoren</a>&nbsp;
	</p><br />
	<p>
		Mit jedem Validator k&ouml;nnen folgende Methoden verwendet werden:<br />
		<span class="output code small">getValue()</span> : Gibt die zu pr&uuml;fenden Inhalt zur&uuml;ck.<br />
		<span class="output code small">getMessage()</span> : Gibt die Nachrichten als array zur&uuml;ck.<br />
		<span class="output code small">isValid($value)</span> : Gibt zur&uuml;ck ob der &uuml;bergebene Parameter g&uuml;ltig ist. <span class="output code small">boolean</span><br />
		<span class="output code small">setMessage($message_key, $message)</span> : Ersetzt die Systemnachricht durch eine benutzerdefinierte Nachricht.<br />
	</p><br />
	<p>
	Eine Fehlernachricht kann folgendermassen bearbeitet werden:
	<pre class="output code">$validator->setMessage(NotEmpty::IS_INVALID, "der Datentyp ist ung&uuml;ltig");</pre>
	<?php 
		$validator	= new NotEmpty(array(NotEmpty::INT, NotEmpty::FLOAT));
		$validator->setMessage(NotEmpty::IS_INVALID, "der Datentyp ist ung&uuml;ltig");
		getOutput($validator->isValid($valid['string']), $validator->getValue(), $validator->getMessage());
	?>
	Es gibt eine Kurzschreibweisse, welche so aussehen w&uuml;rde
	<pre class="output code">$validator("text"); 	//isValid() wird erspart.</pre>
</p>
	<h4 class="new-paragraph" id="not-empty">NotEmpty:</h4>
	<p class="introduction standard-font-size">
		&Uuml;berpr&uuml;ft, ob der &uuml;bergeben Inhalt leer bzw. nicht leer ist.<br />
		Folgende Datentypen k&ouml;nnen &uuml;berpr&uuml;ft werden:
		<span class="output code small">bool</span>
		<span class="output code small">int</span>
		<span class="output code small">float</span>
		<span class="output code small">string</span>
		<span class="output code small">array</span>
		<span class="output code small">object</span><br />
		Der Datentyp <span class="output code small">object</span> ist dabei immer &quot;nicht leer&quot;.
	</p>
	<p>
		Wird mit der Einstellung &quot;alle Datentypen&quot; getestet.<br />
		<pre class="output code">$validator = new NotEmpty();
$validator->isValid("Text");</pre>
	</p>
	<?php 
		$validator	= new NotEmpty();
		getOutput($validator->isValid($valid['string']), $validator->getValue(), $validator->getMessage()) 
	?>
	<div class="step_line"></div>
	<p>
		Wird mit der Einstellung &quot;nur Integer und Float&quot; getestet.<br />
		<pre class="output code">$validator = new NotEmpty (array(NotEmpty::INT,NotEmpty::FLOAT));
		
$validator->isValid("Text");
$validator->isValid(23.15);</pre>
	</p>
	<?php 
		$validator	= new NotEmpty(array(NotEmpty::INT, NotEmpty::FLOAT));
		getOutput($validator->isValid($valid['string']), $validator->getValue(), $validator->getMessage());
		getOutput($validator->isValid($valid['float']), $validator->getValue(), $validator->getMessage());
	?>
	<h4 class="new-paragraph" id="stringlength">StringLength:</h4>
	<p class="introduction standard-font-size">
		Testet, ob die L&auml;nge eines Strings g&uuml;ltig ist.<br />
		Als Optionen k&ouml;nnen die Werte <span class="output code small">min</span> und <span class="output code small">max</span>
		verwendet werden. Werden die Optionen nicht verwendet liefert die Klasse <span class="output code small">true</span> zur&uuml;ck sobald ein String kleiner als
		<span class="output code small">PHP_INT_MAX</span> ist. Es werden alle Datentypen ausser 
		<span class="output code small">object</span> und <span class="output code small">array</span> unterst&uuml;tzt. Achtung! Der Wert wird automatisch in einen String konvertiert.  
	</p>
	<p>
		Es wird mit den Einstellungen 
		<span class="output code small">min = 5</span> und 
		<span class="output code small">max = 10</span> gepr&uuml;ft:<br />
		<pre class="output code">$validator = new StringLength(array('min' => 5, 'max' => 10);
$validator->isValid("Testtext");</pre>
	</p>
	<?php 
		$validator	= new StringLength(array('min'	=> 5,'max'	=> 10));
		getOutput($validator->isValid($valid['string']), $validator->getValue(), $validator->getMessage());
	?>
	<div class="step_line"></div>
	<p>
		Es wird mit den Einstellungen 
		<span class="output code small">min = 10</span> gepr&uuml;ft:<br />
		<pre class="output code">$validator = new StringLength(array('min' => 10);
$validator->isValid("Testtext");</pre>
	</p>
	<?php 
		$validator	= new StringLength(array('min'	=> 10));
		getOutput($validator->isValid($valid['string']), $validator->getValue(), $validator->getMessage());
	?>
	<h4 class="new-paragraph" id="between">Between:</h4>
	<p class="introduction standard-font-size">
		Testet, ob eine Zahl zwischen den vorgegeben Min- und Maximalwerten liegt.<br />
		Als Optionen k&ouml;nnen die Werte <span class="output code small">min</span> , <span class="output code small">max</span> und <span class="output code small">strict</span>
		verwendet werden. Werden die <span class="output code small">min</span> und <span class="output code small">max</span> Parameter nicht verwendet liefert die Klasse <span class="output code small">true</span> zur&uuml;ck sobald eine Zahl zwischen
		<span class="output code small">-PHP_INT_MAX</span> und <span class="output code small">PHP_INT_MAX</span> liegt.
		Die Option <span class="output code small">strict</span> bestimmt, ob die Zahl genau zischen den beiden Limits liegen muss,
		oder ob diese miteingeschlossen sind.
	</p>
	<p>
		Es wird mit den Einstellungen 
		<span class="output code small">min = -5</span> und 
		<span class="output code small">max = 10</span> gepr&uuml;ft:<br />
		<pre class="output code">$validator = new Between(array('min' => -5, 'max' => 10);
$validator->isValid(10);</pre>
	</p>
	<?php 
		$validator	= new Between(array('min' => -5, 'max' => 10));
		getOutput($validator->isValid($valid['int']), $validator->getValue(), $validator->getMessage());
	?>
	<div class="step_line"></div>
	<p>
		Es wird mit den Einstellungen 
		<span class="output code small">min = -5</span> , 
		<span class="output code small">max = 10</span> und
		<span class="output code small">strict</span> 
		gepr&uuml;ft:<br />
		<pre class="output code">$validator = new Between(array('min' => -5, 'max' => 10, Between::STRICT);
$validator->isValid(10);</pre>
	</p>
	<?php 
		$validator	= new Between(array('min' => -5, 'max' => 10, Between::STRICT));
		getOutput($validator->isValid($valid['int']), $validator->getValue(), $validator->getMessage());
	?>
	<h4 class="new-paragraph" id="chain">Verkettung von Validatoren:</h4>
	<p class="introduction standard-font-size">
		Es k&ouml;nnen mehrere Validatoren anhand einer <span class="output code small">ValidatorChain()</span> miteinander verbunden werden, um mit nur einem Befehl,
		den Wert, auf alle gew&uuml;nschten Kriterien pr&uuml;fen zu k&ouml;nnen. Die Validatoren k&ouml;nnen im Konstruker als Objekt eines Validators, als <span class="output code small">array</span> mit mehreren Objekten von Validatoren oder mit der Methode <span class="output code small">add()</span> ebenfalls als einzelnes Objekt und als <span class="output code small">array</span> &uuml;bergeben werden.
	</p>
	<p>
		Es wird mit den Einstellungen <br />
		<span class="output code small">NotEmpty()</span> , <br /> 
		<span class="output code small">Between()</span> mit der Einstellung <span class="output code small">max = 100</span><br />
		<span class="output code small">Stringlength</span> mit den Einstellungen <span class="output code small">min = 1</span> und <span class="output code small">max = 2</span>
		 gepr&uuml;ft:<br />
		<pre class="output code">$chain	= new ValidatorChain(new NotEmpty());
$chain->add(array(new Between(array('max' => 100)), new StringLength(array('min' => 1, 'max' => 2))));
$chain->isValid(100);</pre>
	</p>
	<?php 
	
		$chain	= new ValidatorChain(new NotEmpty());
		$chain->add(array(new Between(array('max' => 100)), new StringLength(array('min' => 1, 'max' => 2))));
		getOutput($chain->isValid(100), $chain->getValue(), $chain->getMessage());
	?>
	<p>
	Die Validatoren <span class="output code small">NotEmpty()</span> und <span class="output code small">Between()</span> melden <span class="output code small">true</span>. Das Ergebnis ist allerdings trotzdem <span class="output code small">false</span>
	weil der Validator <span class="output code small">Stringlength()</span> die Zahl 100 in einen String umwandelt und dessen L&auml;nge mit 3 (Zeichen) bestimmt. Wäre der Wert beispielsweise <span class="output code small">80</span> w&uuml;rde dieses Ergebnis rauskommen.
	</p>
    <?php 
	
		$chain	= new ValidatorChain(new NotEmpty());
		$chain->add(array(new Between(array('max' => 100)), new StringLength(array('min' => 1, 'max' => 2))));
		getOutput($chain->isValid(80), $chain->getValue(), $chain->getMessage());
	?>