<?php
use wsc\user\User;


$auth	= $this->application->load("auth");
$user	= $auth->getUser();
$config	= $this->application->load("config");
$db		= $this->application->load("Database");


if(isset($_POST['addres']))
{
	$sql	= "
				INSERT 
					acl_resources(name, disp_name, description) 
				VALUES('".$_POST['resname']."','".$_POST['resdname']."','".$_POST['resdesc']."')
			";
	
	$res	= $db->query($sql) or die($db->error);
}
if(isset($_POST['addpri']))
{
	$sql	= "
				INSERT
					acl_privileges(name, disp_name, description)
				VALUES('".$_POST['priname']."','".$_POST['pridname']."','".$_POST['pridesc']."');
			";

	$res	= $db->query($sql) or die($db->error);
}
if(isset($_POST['addlink']))
{
	$res_link	= $db->query("SELECT * FROM acl_link_privileges WHERE aclResourcesId = '".$_POST['linkres']."' AND aclPrivilegesId ='".$_POST['linkpri']."'") or die($db->error);
	$num_link	= $res_link->num_rows;
	
	if($num_link == 0)
	{	
		$sql	= "
					INSERT
						acl_link_privileges(aclResourcesId, aclPrivilegesId)
					VALUES('".$_POST['linkres']."','".$_POST['linkpri']."');
				";
	
		$res	= $db->query($sql) or die($db->error);
	}
	else
	{
		$error	= new wsc\systemnotification\SystemNotification("error");
		$error->addMessage("&quot;Resourcen - Recht&quot; - Verkn&uuml;pfung ist bereits vorhanden!<br>Der Datensatz wurde nicht gespeichert.");
		$error->printMessage();
	}
}
$ressources	= $this->acl->getAllResources();
$privileges	= $this->acl->getAllPrivileges();

//$perm	= $this->acl->checkResourcelink();

?>
<div style="float:left; width:70%;">
    <h1>ACL Systemtest</h1>
    <div class="step_line"></div>
    <p class="introduction">Diese Seite dient dazu, die ACL (= Access Control List) Klasse zu testen. Es werden kleinere Formulare eingebaut um nicht immer direkt in der Datenbank arbeiten zu m&uuml;ssen.</p>
    
    <div>
        <div class="section-title s-t-top">
            <h4>Berechtigungstests:</h4>
        </div>
        <div class="section-body s-b-bottom">
			<?php 
            if($this->acl->hasPermission(new User($this->application, 1), "acl_test", "administrate") === true)
            {
                echo "Berechtigt.";
            }
            else 
            {	
                echo "Nicht berechtigt.";	
            }
            
            ?>
        </div>
    </div>

    <div>
        <div class="section-title s-t-top">
            <h4>Alle Benutzerrechte:</h4>
        </div>
        <div class="section-body s-b-bottom">
            <?php 
            foreach($user->permissions as $userpermission)
            {
                $plinkid	= $db->getDataByID("acl_link_privileges", $userpermission['id']);
                $aresource	= $db->getDataByID("acl_resources", $plinkid['aclResourcesId']);
                $aprivilege	= $db->getDataByID("acl_privileges", $plinkid['aclPrivilegesId']);
                echo "P-ID: ".$userpermission['id']. " - ". $aresource['name']." -> ".$aprivilege['name']."<br />";
                echo "&nbsp;&nbsp;" .$aresource['disp_name']." ".strtolower($aprivilege['disp_name']).": ".$aprivilege['description']."<br>";
            }
            ?>
        </div>
    </div>
    <div class="left">
        <div class="section-title s-t-top">
            <h4>Rollen:</h4>
        </div>
        <div class="section-body s-b-bottom">
            <?php 
            foreach($user->roles as $userrole)
            {
                $urole	= $db->getDataByID("acl_roles", $userrole);
                echo "R-ID: ".$urole['id']." - " . $urole['name'] . "<br />";
            }
            ?>
        </div>
    </div>
    
    <div class="right">
        <div class="section-title s-t-top">
            <h4>Gruppen:</h4>
        </div>
        <div class="section-body s-b-bottom">
            <?php 
            foreach($user->groups as $usergroup)
            {
                $ugroup	= $db->getDataByID("user_groups", $usergroup);
                echo "G-ID: ".$ugroup['id']." - " . $ugroup['name'] . "<br />";
            }
            ?>
        </div>
    </div>
    <div class="clearing"></div>
    
    <div>
        <div class="section-title s-t-top">
            <h4>Vorhandene Resourcen:</h4>
        </div>
        <div class="section-body s-b-bottom">
        <?php 
        foreach ($ressources as $resource)
        {
            echo "R-ID: ".$resource['id']." - ".$resource['name']."<br>";
            $sql_spri	= "
                                SELECT 
                                    acl_link_privileges.*,
                                    acl_privileges.name,
                                    acl_privileges.disp_name,
                                    acl_privileges.description
                                FROM 
                                    acl_link_privileges
                                JOIN
                                    acl_privileges
                                ON 
                                    acl_link_privileges.aclPrivilegesId = acl_privileges.id
                                WHERE 
                                    acl_link_privileges.aclResourcesId = '".$resource['id']."'
                ";
            $res_spri	= $db->query($sql_spri) or die($db->error);
            
            
            while(($row_spri=$res_spri->fetch_assoc()) != false)
            {
                echo "&nbsp;&nbsp;P-Link-ID: ".$row_spri['id']." - ".$row_spri['name']."<br>";
            }
            echo"<br>";
        }
        ?>
        </div>
    </div>
</div>
<div style="width:29%; float:right;>
<form action="<?= "?" . $config->get("forward_link"); ?>" method="post">
    <div id="resource-form-wrapper">
        <div class="section-title s-t-top" id="resource-form-title">
            <h4>Neue Resource erfassen</h4>
        </div>
        <div class="section-body" id="resource-form-body">
            <input type="text" placeholder="Resourcenname" style="width:95%; margin-bottom:5px;" name="resname" required><br>
            <input type="text" placeholder="Displayname" style="width:95%; margin-bottom:5px;" name="resdname" required><br>
            <textarea placeholder="Beschreibung..." style="width:95%;height:100px" name="resdesc" required></textarea><br>
        </div>
        <div class="section-title s-t-bottom">
        	<input type="submit" name="addres" value="Speichern">
        </div>
    </div>
</form>
<form action="<?= "?" . $config->get("forward_link"); ?>" method="post">
	<div class="section-wrapper">
    	<div class="section-title s-t-top">
            <h4>Neues Recht erfassen</h4>
        </div>
        <div class="section-body">
            <input type="text" placeholder="Rechtename" style="width:95%; margin-bottom:5px;" name="priname" required><br>
            <input type="text" placeholder="Displayname" style="width:95%; margin-bottom:5px;" name="pridname" required><br>
            <textarea placeholder="Beschreibung..." style="width:95%;height:100px" name="pridesc" required></textarea><br>
        </div>
        <div class="section-title s-t-bottom">
            <input type="submit" name="addpri" value="Speichern">
        </div>
	</div>
</form>
<br>
<form action="<?="?" . $config->get("forward_link"); ?>" method="post">
	<div class="section-wrapper">
    	<div class="section-title s-t-top">
            <h4>Neue Verkn&uuml;pfung erstellen</h4>
        </div>
        <div class="section-body">
            <select name="linkres" style="width:95%;" required>
            <option value="-1" selected="selected" disabled="disabled">Resource ausw&auml;hlen</option>
                <?php 
                    foreach ($ressources as $resource)
                    {
                        echo "<option value=".$resource['id'].">".$resource['name']."</option>";
                    }
                ?>
            </select><br>
            <select name="linkpri" style="width:95%;" required>
            <option value="-1" selected="selected" disabled="disabled">Recht ausw&auml;hlen</option>
                <?php 
                    foreach ($privileges as $privilege)
                    {
                        echo "<option value=".$privilege['id'].">".$privilege['name']."</option>";
                    }
                ?>
            </select>
        </div>
        <div class="section-title s-t-bottom">
            <input type="submit" name="addlink" value="Speichern">
        </div>
    </div>
</form>
</div>
<div class="clearing"></div>