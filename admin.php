<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//

if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $user, $language;
translate("modules/Support/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();

if (!$user)
{
    $visiteur = 0;
} 
else
{
    $visiteur = $user[1];
} 
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function main()
    {
	global $nuked, $language; ?>

<div class="content-box"> 
		<div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Contact.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
	<div class="tab-content" id="tab2"><div style="text-align: center;"><?php echo _LISTTICKETS." ". _OUVERTS; ?> <b> | 
	<a href="index.php?file=Support&amp;page=admin&amp;op=listClose"><?php echo _LISTTICKETS." ". _FERMES; ?></a> | 
	<a href="index.php?file=Support&amp;page=admin&amp;op=main_pref"><?php echo _PREFS; ?></a></b></div><br />
	<table style="margin-left: auto;margin-right: auto;text-align: left;" width="90%"  border="0" cellspacing="1" cellpadding="2">
	<tr>
	<td style="width: 10%;" align="center"><b>#</b></td>
	<td style="width: 30%;" align="center"><b><?php echo _SUJET; ?></b></td>
	<td style="width: 20%;" align="center"><b><?php echo _MEMBRE; ?></b></td>
	<td style="width: 20%;" align="center"><b><?php echo _DATE; ?></b></td>
	<td style="width: 20%;" align="center"><b><?php echo _OPERATIONS; ?></b></td></tr>
<?php
	$cat = recupCat(); $nbTickets = 0;
        while($c = mysql_fetch_assoc($cat))
        {
            $tickets = recupTicketsCat($c["id"]);
            if(mysql_num_rows($tickets) == 0){
                break;
            } ?>
            <tr><td colspan="5"><br /><h4><?php echo $c["nom"]; ?></h4></td></tr>
            <?php while($t = mysql_fetch_assoc($tickets))
            { $t["date"] = strftime("%d/%m/%Y %H:%M", $t["date"]); ?>
                <tr>
	<td style="width: 10%;"><?php echo $t["id"]; ?></td>
	<td style="width: 30%;"><?php echo $t["titre"]; ?></td>
	<td style="width: 20%;"><?php echo $t["auteur"]; ?></td>
	<td style="width: 20%;"><?php echo $t["date"]; ?></td>
	<td style="width: 20%;"><a href="index.php?file=Support&amp;page=admin&amp;op=view&amp;id=<?php echo $t["id"]; ?>"><?php echo _CONSULT.'/'._REPLY; ?></a> - <a href="index.php?file=Support&amp;page=admin&amp;op=close&amp;id=<?php echo $t["id"]; ?>"><?php echo _CLOSE; ?></a></td></tr>
            <?php $nbTickets++; }

        }
        


	if ($nbTickets == 0) echo "<tr><td align=\"center\" colspan=\"5\">" . _NOTICKETS . "</td></tr>\n"; ?>

	</table><br /><div style="text-align: center;">[ <a href="index.php?file=Admin"><b><?php echo _BACK; ?></b></a> ]</div><br /></div></div>
    <?php }
    
    function listClose()
    {
        global $nuked, $language;  ?>

<div class="content-box"> 
		<div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Contact.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
	<div class="tab-content" id="tab2"><div style="text-align: center;"><b><a href="index.php?file=Support&amp;page=admin"><?php echo _LISTTICKETS." ". _OUVERTS; ?></a> | 
	</b><?php echo _LISTTICKETS." ". _FERMES; ?><b> | 
	<a href="index.php?file=Support&amp;page=admin&amp;op=main_pref"><?php echo _PREFS; ?></a></b></div><br />
	<table style="margin-left: auto;margin-right: auto;text-align: left;" width="90%"  border="0" cellspacing="1" cellpadding="2">
	<tr>
	<td style="width: 10%;" align="center"><b>#</b></td>
	<td style="width: 30%;" align="center"><b><?php echo _SUJET; ?></b></td>
	<td style="width: 20%;" align="center"><b><?php echo _MEMBRE; ?></b></td>
	<td style="width: 20%;" align="center"><b><?php echo _DATE; ?></b></td>
	<td style="width: 20%;" align="center"><b><?php echo _OPERATIONS; ?></b></td></tr>
        
<?php
	$cat = recupCat(); $nbTickets = 0;
        while($c = mysql_fetch_assoc($cat))
        {
            $tickets = recupTicketsCatClose($c["id"]);
            if(mysql_num_rows($tickets) == 0){
                break;
            } ?>
            <tr><td colspan="5"><br /><h4><?php echo $c["nom"]; ?></h4></td></tr>
            <?php while($t = mysql_fetch_assoc($tickets))
            { $t["date"] = strftime("%d/%m/%Y %H:%M", $t["date"]); ?>
                <tr>
	<td style="width: 10%;"><?php echo $t["id"]; ?></td>
	<td style="width: 30%;"><?php echo $t["titre"]; ?></td>
	<td style="width: 20%;"><?php echo $t["auteur"]; ?></td>
	<td style="width: 20%;"><?php echo $t["date"]; ?></td>
        <td style="width: 20%;"><a href="index.php?file=Support&amp;page=admin&amp;op=view&amp;id=<?php echo $t["id"]; ?>"><?php echo _CONSULT; ?></a> - <a href="index.php?file=Support&amp;page=admin&amp;op=open&amp;id=<?php echo $t["id"]; ?>"><?php echo _OPEN; ?></a></td>
        </tr>
        <?php $nbTickets++; }

        }
        


	if ($nbTickets == 0) echo "<tr><td align=\"center\" colspan=\"5\">" . _NOTICKETS . "</td></tr>\n"; ?>

	</table><br /><div style="text-align: center;">[ <a href="index.php?file=Admin"><b><?php echo _BACK; ?></b></a> ]</div><br /></div></div>
    <?php }
 
    function viewThread($thread_ID)
    {
        global $nuked, $language;
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else
        {
            $messages = recupThreadMessages($thread_ID);
        
        ?>
<div class="content-box"> 
		<div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Contact.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
	<div class="tab-content" id="tab2"><div style="text-align: center;"><b><a href="index.php?file=Support&amp;page=admin"><?php echo _LISTTICKETS." ". _OUVERTS; ?></a> | 
	<a href="index.php?file=Support&amp;page=admin&amp;op=listClose"><?php echo _LISTTICKETS." ". _FERMES; ?></a> | 
	<a href="index.php?file=Support&amp;page=admin&amp;op=main_pref"><?php echo _PREFS; ?></a></b></div><br />
    <h4>&nbsp;&nbsp;<?php echo _SUJET." : ".$thread["titre"]; ?></h4><br />
</div><div style="padding:10px;">
        <?php while($m = mysql_fetch_assoc($messages)){ if($m["admin"] == 0){ ?>
<div style="width:100%;"><b><?php echo $m["auteur"] . _WROTE . strftime("%x %H:%M", $m["date"]) ?></b><br />
<?php echo $m["texte"] ?></div>
            <?php } else { ?>
<div style="width:100%; background-color: yellow;"><b><?php echo $m["auteur"] . _WROTE . strftime("%x %H:%M", $m["date"]) ?></b><br />
<?php echo $m["texte"] ?></div>
            <?php }
        } ?> </div>

<br />
<?php if($thread["closed"] == 0) { ?>
<form method="post" action="index.php?file=Support&amp;page=admin&amp;op=reply">
    <table style="margin-left: auto;margin-right: auto;text-align: left;" cellspacing="1" cellpadding="3" border="0">
	<tr><td align="center"><h3><b><?php echo _REPLY; ?></b></h3><input type="text" style="display:none;" name="id" id="id" size="5" value="<?php echo $thread_ID; ?>" /></td></tr>
	<tr><td align="center"><textarea class="editorsimpla" id="ns_corps" name="corps" cols="60" rows="12"></textarea><br /><input type="submit" class="bouton" value="<?php echo _SEND; ?>"/></td></tr>
    </table>
</form><div style="text-align:center;"><br /><a href="index.php?file=Support&amp;page=admin&amp;op=close&amp;id=<?php echo $thread["id"]; ?>">[ <?php echo _CLOSE." "._THISTICKET; ?> ]</a><br /><br /></div><?php } else { ?>

<div style="text-align:center;"><br /><a href="index.php?file=Support&amp;page=admin&amp;op=open&amp;id=<?php echo $thread["id"]; ?>">[ <?php echo _OPEN." "._THISTICKET; ?> ]</a><br /><br /></div><?php } 
    }
    }


    function view($mid)
    {
	
       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINCONTACT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Contact.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"4\">\n"
	. "<tr><td>" . _FROM . "  <a href=\"mailto:" . $email . "\"><b>" . $nom . "</b></a> (IP : " . $ip . ") " . _THE . " " . $day . "</td></tr>\n"
	. "<tr><td><b>" . _YSUBJECT . " :</b> " . $titre . "</td></tr>\n"
	. "<tr><td><br />" . $message . "</td></tr></table>\n"
	. "<div style=\"text-align: center;\"><br /><input type=\"button\" value=\"" . _DELTHISMESS . "\" onclick=\"javascript:delmail('" . $name . "', '" . $mid . "');\" />\n"
	. "<br /><br />[ <a href=\"index.php?file=Contact&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";

    }


    function del($mid)
    {
	global $nuked, $user;
	
	$sql = mysql_query("DELETE FROM " . CONTACT_TABLE . " WHERE id = '" . $mid . "'");
	// Action
	$texteaction = "". _ACTIONDELCONTACT .".";
	$acdate = time();
	$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
	//Fin action
	echo "<div class=\"notification success png_bg\">\n"
	. "<div>\n"
	. "" . _MESSDELETE . "\n"
	. "</div>\n"
	. "</div>\n";
	redirect("index.php?file=Contact&page=admin", 2);
    }


    function main_pref()
    {
        global $nuked, $language;

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINCONTACT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Contact.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Contact&amp;page=admin\">" . _LISTMAIL . "</a> | "
	. "</b>" . _PREFS . "</div><br />\n"
	. "<form method=\"post\" action=\"index.php?file=Contact&amp;page=admin&amp;op=change_pref\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
	. "<tr><td align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
	. "<tr><td>" . _EMAILCONTACT . " : <input type=\"text\" name=\"contact_mail\" size=\"40\" value=\"" . $nuked['contact_mail'] . "\" /></td></tr>\n"
	. "<tr><td>" . _FLOODCONTACT . " : <input type=\"text\" name=\"contact_flood\" size=\"2\" value=\"" . $nuked['contact_flood'] . "\" /></td></tr></table>\n"
	. "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
	. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Contact&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($contact_mail, $contact_flood)
    {
        global $nuked, $user;

        $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $contact_mail . "' WHERE name = 'contact_mail'");
        $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $contact_flood . "' WHERE name = 'contact_flood'");
		// Action
		$texteaction = "". _ACTIONPREFCONT .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _PREFUPDATED . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Contact&page=admin", 2);
    } 

    
    
    function recupTicketsCat($cat_ID)
    {
	global $nuked;
        if(is_nan($cat_ID)){return 0;}
    	$sql = mysql_query("SELECT * FROM ". $nuked["prefix"] ."_support_threads WHERE cat_id = '" . $cat_ID . "' AND closed = 0 ORDER BY id DESC");
        return $sql;
    }
    function recupTicketsCatClose($cat_ID)
    {
	global $nuked;
        if(is_nan($cat_ID)){return 0;}
    	$sql = mysql_query("SELECT * FROM ". $nuked["prefix"] ."_support_threads WHERE cat_id = '" . $cat_ID . "' AND closed = 1 ORDER BY id DESC");
        return $sql;
    }
    function recupThreadMessages($thread_id)
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". $nuked["prefix"] ."_support_messages WHERE thread_id = '" . $thread_id . "' ORDER BY date ASC");
        return $sql;
    }
    function recupCat()
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". $nuked["prefix"] ."_support_cat ORDER BY ordre ASC");
        return $sql;
    }
    function recupThread($thread_id)
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". $nuked["prefix"] ."_support_threads WHERE id = '" . $thread_id . "' ORDER BY id DESC LIMIT 0,1");
        $sql = mysql_fetch_assoc($sql);
        return $sql;
    }
    function getCatName($catID)
    {
	global $nuked;
        if(is_nan($cat_ID)){return 0;}
    	$sql = mysql_query("SELECT nom FROM ". $nuked["prefix"] ."_support_cat WHERE id = '" . $catID . "' ORDER BY id DESC LIMIT 0,1");
        $sql = mysql_fetch_assoc($sql);
        return $sql;
    }
    
    
    
    switch($_REQUEST['op'])
    {
	case "view":
	viewThread($_REQUEST['id']);
	break;

	case "listClose":
            listClose();
	break;

	case "main_pref":
	main_pref();
	break;

	case "change_pref":
	change_pref($_REQUEST['contact_mail'], $_REQUEST['contact_flood']);
	break;

	default:
        main();
	break;
    }

} 
else if ($level_admin == -1)
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
else if ($visiteur > 1)
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
else
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}

adminfoot();

?>