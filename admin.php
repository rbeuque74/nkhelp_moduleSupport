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
include("modules/Support/config.php");
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
    function main($closed = 0)
    {
	global $nuked, $language; ?>

<div class="content-box"> 
        <div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Support.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
	<div class="tab-content" id="tab2"><div style="text-align: center;"><?php if($closed){ ?><b><a href="index.php?file=Support&amp;page=admin"><?php } echo _LISTTICKETS." ". _OUVERTS; if($closed){ ?></a><?php } else { ?><b><?php } ?> | 
	<?php if(!$closed){ ?><a href="index.php?file=Support&amp;page=admin&amp;op=index&amp;tickets=close"><?php } else { ?></b><?php } echo _LISTTICKETS." ". _FERMES; if(!$closed){ ?></a><?php } else {?> <b><?php } ?> | 
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
            $tickets = ($closed == 0) ? recupTicketsCat($c["id"]) : recupTicketsCatClose($c["id"]);
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
	<td style="width: 20%;"><a href="index.php?file=Support&amp;page=admin&amp;op=view&amp;id=<?php echo $t["id"]; ?>"><?php echo _CONSULT; if($closed ==0){ echo '/'._REPLY;} ?></a> - <a href="index.php?file=Support&amp;page=admin&amp;op=<?php if($closed ==0){ echo "close";} else{ echo"open";}?>&amp;id=<?php echo $t["id"]; ?>"><?php if($closed ==0){ echo _CLOSE;} else{ echo _OPEN;}?></a></td></tr>
            <?php $nbTickets++; }

        }
        


	if ($nbTickets == 0) echo "<tr><td align=\"center\" colspan=\"5\">" . _NOTICKETS . "</td></tr>\n"; ?>

	</table><br /><div style="text-align: center;">[ <a href="index.php?file=Admin"><b><?php echo _BACK; ?></b></a> ]</div><br /></div></div>
    <?php }

    function viewThread($thread_ID)
    {
        global $nuked, $language, $color_top, $color_content1, $color_content2, $color_admin;
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
    <table style="margin-left: auto;margin-right: auto;text-align: left;" cellspacing="1" cellpadding="0" border="0">
	<tr><td align="center"><h4>&nbsp;&nbsp;<?php echo _SUJET." : ".$thread["titre"]; ?></h4></td></tr>

        <?php while($m = mysql_fetch_assoc($messages)){ 
            $sql = mysql_query("SELECT avatar FROM ". $nuked["prefix"] ."_users WHERE id = '".mysql_real_escape_string($m["auteur_id"])."' LIMIT 0,1");
            $user_avatar = mysql_fetch_assoc($sql);
            ?>
        <tr <?php if($m["admin"] == 1){ ?>style="background:<?php echo $color_admin;?>;"<?php }?>><td style="padding:5px;"><div id="message" style="width:100%; text-align:left;">
                <div style="float:left; width:45px; height:45px; padding-right:3px; "><img src="<?php echo checkimg($user_avatar["avatar"]); ?>" width="45" height="45" alt="" /></div>
                <div>
                    <div style="left:4px;"><b><?php if($m["admin"] != 0){ ?> {admin} <?php } echo $m["auteur"] . _WROTE.strftime("%x &agrave; %H:%M", $m["date"]) ?></b></div>
                    <div style="padding-left:4px;"><?php echo $m["texte"] ?></div>
                </div>
                <div style="clear:both;"></div>
                
            </div></td></tr>
            
      <?php  } ?> </table>

<br />
<?php if($thread["closed"] == 0) { ?>
<form method="post" action="index.php?file=Support&amp;page=admin&amp;op=reply">
    <table style="margin-left: auto;margin-right: auto;text-align: left;" cellspacing="1" cellpadding="3" border="0">
	<tr><td align="center"><h3><b><?php echo _REPLY; ?></b></h3><input type="text" style="display:none;" name="id" id="id" size="5" value="<?php echo $thread_ID; ?>" /></td></tr>
	<tr><td align="center"><textarea class="editorsimpla" id="ns_corps" name="corps" cols="60" rows="12"></textarea><br /><input type="submit" class="bouton" value="<?php echo _SEND; ?>"/></td></tr>
    </table>
</form><div style="text-align:center;"><br />[ <a href="index.php?file=Support&amp;page=admin&amp;op=close&amp;id=<?php echo $thread["id"]; ?>"><b><?php echo _CLOSE." "._THISTICKET; ?></b></a> ] - [ <a href="index.php?file=Support&amp;page=admin"><b><?php echo _BACK; ?></b></a> ]<br /><br /></div><?php } else { ?>

<div style="text-align:center;"><br />[ <a href="index.php?file=Support&amp;page=admin&amp;op=open&amp;id=<?php echo $thread["id"]; ?>"><b><?php echo _OPEN." "._THISTICKET; ?></b></a> ] - [ <a href="index.php?file=Support&amp;page=admin"><b><?php echo _BACK; ?></b></a> ]<br /><br /></div><?php } ?>
</div></div>
<?php    }
    }


    function reply($thread_ID, $corps, $new=0)
    {
        global $nuked, $language, $user; ?>
<div class="content-box"> 
		<div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Contact.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
    <div class="tab-content" id="tab2"> <?php
        if(is_nan($thread_ID))
        {
            ?> <div style="text-align:center;"><h3><?php echo _TICKETDONTEXIST; ?></h3>
            <br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h3><?php echo _TICKETDONTEXIST; ?></h3>
            <br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else { 
            $sql = mysql_query("INSERT INTO ". $nuked["prefix"] ."_support_messages (texte, date, auteur, auteur_id, auteur_ip, thread_id, admin) VALUES ('". mysql_real_escape_string(secu_html(html_entity_decode($corps, ENT_QUOTES))) ."', '". time() ."', '". mysql_real_escape_string($user[2]) ."', '". mysql_real_escape_string($user[0]) ."', '". mysql_real_escape_string($user[3]) ."', '". mysql_real_escape_string($thread_ID) ."', '1') ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
            }
            else if($new == 0) {
        ?>
<div class="notification success png_bg">
	<div>
	<?php echo _REPLYSUCCESS; ?>
	</div>
	</div>
	<?php redirect("index.php?file=Support&page=admin", 2); ?>
    </div></div>

        <?php     
    }
    }
    }
    
    function close($thread_ID)
    {
        global $nuked, $language, $user; ?>
<div class="content-box"> 
		<div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Contact.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
    <div class="tab-content" id="tab2"><?php 
        if(is_nan($thread_ID))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /><br /></div><?php
        }
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /><br /></div><?php
        }
        else { 
            $sql = mysql_query("UPDATE  ". $nuked["prefix"] ."_support_threads SET  `closed` =  '1' WHERE id = '". mysql_real_escape_string($thread_ID) ."' ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
            }
            else {
        ?>
<div class="notification success png_bg">
	<div>
	<?php echo _CLOSESUCCESS; ?>
	</div>
	</div>
	<?php redirect("javascript:history.back()", 2); ?>
    </div></div>


        <?php     
    }
    }
    }
    
    
    function open($thread_ID)
    {
        global $nuked, $language, $user; ?>
<div class="content-box"> 
		<div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Contact.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
    <div class="tab-content" id="tab2"><?php 
        if(is_nan($thread_ID))
        {
            ?> <div style="text-align:center;"><h3><?php echo _TICKETDONTEXIST; ?></h3>
            <br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /><br /></div><?php
        }
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h3><?php echo _TICKETDONTEXIST; ?></h3>
            <br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /><br /></div><?php
        }
        else { 
            $sql = mysql_query("UPDATE  ". $nuked["prefix"] ."_support_threads SET  `closed` =  '0' WHERE id = '". mysql_real_escape_string($thread_ID) ."' ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
            }
            else if($new == 0) {
        ?>
<div class="notification success png_bg">
	<div>
	<?php echo _OPENSUCCESS; ?>
	</div>
	</div>
	<?php redirect("javascript:history.back()", 2); ?>
    </div></div>

        <?php     
    }
    }
    }
    

    function main_pref()
    {
        global $nuked, $language; ?>

<div class="content-box"><div class="content-box-header"><h3><?php echo _ADMINSUPPORT; ?></h3>
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Support.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
	<div class="tab-content" id="tab2"><div style="text-align: center;"><b><a href="index.php?file=Support&amp;page=admin"><?php echo _LISTTICKETS." ". _OUVERTS; ?></a> | 
	<a href="index.php?file=Support&amp;page=admin&amp;op=index&amp;tickets=close"><?php echo _LISTTICKETS." ". _FERMES; ?></a></b> | 
	<?php echo _PREFS; ?></div><br />

       
	<form method="post" action="index.php?file=Contact&amp;page=admin&amp;op=change_pref">
	<table style="margin-left: auto;margin-right: auto;text-align: left;" border="0" cellspacing="0" cellpadding="3">
	<tr><td align="center"><big><?php echo _PREFS; ?></big></td></tr>
	<tr><td><?php echo _EMAILCONTACT; ?>  : <input type="text" name="contact_mail" size="40" value="<?php echo  $nuked['contact_mail'];?> " /></td></tr>
	<tr><td><?php echo _FLOODCONTACT; ?>  : <input type="text" name="contact_flood" size="2" value="<?php echo $nuked['contact_flood'];?> " /></td></tr></table>
	<div style="text-align: center;"><br /><input type="submit" value="<?php echo _SEND; ?>" /></div>
	<div style="text-align: center;"><br />[ <a href="index.php?file=Contact&amp;page=admin"><b><?php echo  _BACK; ?></b></a> ]</div></form><br /></div></div>
<?php    } 

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

	case "index":
            if(isset($_REQUEST["tickets"]) AND $_REQUEST["tickets"] == "close")
            {
                main(1);
            }
            else {
                main();
            }
	break;
    
    case"reply":
            reply($_REQUEST["id"], $_REQUEST["corps"]);
            break;
        case"close":
            close($_REQUEST["id"]);
            break;
        case"open":
            open($_REQUEST["id"]);
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