<h2 class="step_line" style="width:260px;">Die Form Klasse</h2>
<h4>Allgemein:</h4>
<p class="section">
    Mit der Klasse k&ouml;nnen Formularelemente erzeugt und &uuml;berpr&uuml;ft werden. Es k&ouml;nnen einzelne Elemente erzeugt werden, 
    aber auch <span class="code small output">Forms</span>, die mehere Elemente enthalten, sowie <span class="code small output">Fieldsets</span> ,
    die mehrere <span class="code small output">Forms</span> enhalten k&ouml;nnen.
    Die Ausgabe der Elemente erfolgt &uuml;ber <span class="code small output">ViewHelper</span>. Siehe mehr dazu im Kapitel ViewHelper
</p>

<h4>Verschiedene Elemente ohne Form:</h4>
<?= $this->FormText($this->vorname) ?>
<?= $this->FormText($this->nachname) ?><br>
<?= $this->FormPassword($this->password)?>
<?= $this->FormPassword($this->password_rpd)?><br><br>
<?= $this->FormSubmit($this->senden) ?><br>