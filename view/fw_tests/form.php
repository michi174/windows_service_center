<h2 class="step_line" style="width:260px;">Die Form Klasse</h2>
<h4>Allgemein:</h4>
<p class="section">
    Mit der Klasse k&ouml;nnen Formularelemente erzeugt und &uuml;berpr&uuml;ft werden. Es k&ouml;nnen einzelne Elemente erzeugt werden, 
    aber auch <span class="code small output">Forms</span>, die mehere Elemente enthalten, sowie <span class="code small output">Fieldsets</span> ,
    die mehrere <span class="code small output">Forms</span> enhalten k&ouml;nnen.
    Die Ausgabe der Elemente erfolgt &uuml;ber <span class="code small output">ViewHelper</span>. Siehe mehr dazu im Kapitel ViewHelper
</p>
<?= $this->notification ?><br>
<?php if($this->valid !== true): ?>
<h4>Verschiedene Tests der Form-Klasse:</h4><br>
<div id="register" style="display:inline-block">
<?= $this->form()->openTag($this->register); ?>
<div id="register-header" class="section-title s-t-top register">
    <img alt="register" src="template/win8_style/grafics/header/register.png" style="vertical-align:middle; margin-right:5px;">
    Registrierung (Testformular)
</div>
<div id="register-pflicht" class="section-body register">
	<h4 style="margin-left:5px;">Pflichtfelder</h4>
	<?= $this->formRow($this->register->get("vorname")) ?><br>
    <?= $this->formRow($this->register->get("nachname")) ?><br>
    <?= $this->formRow($this->register->get("username")) ?><br>
    <?= $this->formRow($this->register->get("email")) ?><br>
    <?= $this->formRow($this->register->get("pwd")) ?><br>
    <?= $this->formRow($this->register->get("pwd_wdh")) ?><br>
</div><br>
<div id="register-opt" class="section-body s-b-lower register">
    <h4>Optionale Angaben</h4>
    <?= $this->formRow($this->register->get("street")) ?><br>
    <?= $this->formRow($this->register->get("plz")) ?><br>
    <?= $this->formRow($this->register->get("city")) ?><br>
    <?= $this->formSelect($this->register->get("land"))?><br><br>
    <?= $this->formRadio($this->register->get("sex"))->get("m")?>&nbsp;
    <?= $this->formRadio($this->register->get("sex"))->get("f")?>
</div><br>
<div id="register-save" class="section-title s-t-bottom register">
    <?= $this->formCheckbox($this->register->get("agb")) ?>
    <?= $this->formLabel($this->register->get("agb")) ?><br><br>
    <?= $this->formReset($this->register->get("reset")) ?>
    <?= $this->formSubmit($this->register->get("speichern")) ?><br>
</div>
<?= $this->form()->closeTag() ?>
</div>
<?php else:?>
<div class="section-wrapper">
    <div class="section-title s-t-top">
        <h4>Folgende Eingaben wurden erfolgreich &uuml;bermittelt</h4>
    </div>
    <div class="section-body">
        <table>
          <tr>
            <td style="width:150px">Vorname:</td>
            <td><?= $this->register->getData("vorname")?></td>
          </tr>
          <tr>
            <td>Nachname:</td>
            <td><?= $this->register->getData("nachname")?></td>
          </tr>
          <tr>
            <td colspan="2"><br>Das Passwort wird aus Sicherheitsgr&uuml;nden nicht angezeigt.</td>
          </tr>
        </table>
    </div>
</div>
<?php endif;?>