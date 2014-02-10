<h2 class="step_line" style="width:260px;">Die Form Klasse</h2>
<h4>Allgemein:</h4>
<p class="section">
    Mit der Klasse k&ouml;nnen Formularelemente erzeugt und &uuml;berpr&uuml;ft werden. Es k&ouml;nnen einzelne Elemente erzeugt werden, 
    aber auch <span class="code small output">Forms</span>, die mehere Elemente enthalten, sowie <span class="code small output">Fieldsets</span> ,
    die mehrere <span class="code small output">Forms</span> enhalten k&ouml;nnen.
    Die Ausgabe der Elemente erfolgt &uuml;ber <span class="code small output">ViewHelper</span>. Siehe mehr dazu im Kapitel ViewHelper
</p>

<h4>Verschiedene Elemente ohne Form:</h4>
<br>
<fieldset id="login" style="border:1px solid #ccc; width:300px; padding:10px;">
	<legend style="margin-left:5px">Registrierung (Test)</legend>
    <?= $this->form()->openTag($this->register) ?>
		<?= $this->formText($this->register->get("vorname")) ?>
        <?= $this->formText($this->register->get("nachname")) ?><br>
        <?= $this->formPassword($this->register->get("pwd"))?>
        <?= $this->formPassword($this->register->get("pwd_wdh")) ?><br><br>
        <?= $this->formElement($this->register->get("gender")) ?>
        <?= $this->register->get("gender")->getLabel(true) ?><br><br>
        <?= $this->formSubmit($this->register->get("speichern")) ?><br>
	<?= $this->form()->closeTag() ?>
</fieldset>