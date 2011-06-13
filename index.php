<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
/*Module Support / nkhelp */
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $nuked, $language, $user;
translate("modules/Support/lang/" . $language . ".lang.php");
include("modules/Support/config.php");

opentable();

if (!$user)
{
    $lvlUser = 0; 
} 
else
{
    $lvlUser = $user[1]; 
} 

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
$level_admin = admin_mod($ModName);
if($level_access == 0){$level_access = 1;}
if ($lvlUser >= $level_access && $level_access > -1)
{
    function index($closed=0)
    {
        global $lvlUser, $nuked, $user, $color_content2, $color_content1, $color_top;
        if($lvlUser AND $closed == 0)
        {
            $tickets = recupTickets(); 
        }
        else if($closed == 1)
        {
            $tickets = recupTicketsClose(); 
        }
        ?>
<div style="text-align:center;">
    <h2><?php echo _SUPPORT; ?></h2>
    <?php if($closed){ ?><a href="index.php?file=Support"><?php } else { echo "<b>";} echo "[ "._LISTTICKETS." ". _OUVERTS." ]"; if($closed){ ?></a> <?php }else { echo "</b>";} ?> - <?php if(!$closed){?><a href="index.php?file=Support&amp;op=index&amp;tickets=close"><?php }else { echo "<b>";} echo "[ "._LISTTICKETS." ". _FERMES." ]"; if(!$closed){ ?></a><?php }else { echo "</b>";} ?><br /><br />
</div>
<table style="margin-left:auto; margin-right:auto; text-align:left; background:<?php echo $color_content1; ?>; border:1px solid <?php echo $color_top; ?>; width:98%;" cellspacing="1" cellpadding="2">
    <tbody>
        <tr style="background: <?php echo $color_top; ?>">
            <td><b>#</b></td>
            <td><b><?php echo _SUJET; ?></b></td>
            <td><b><?php echo _CAT; ?></b></td>
            <td style="width: 20%;"><b><?php echo _DATE; ?></b></td>
            <td><b><?php echo _OPERATIONS; ?></b></td>
        </tr>
        <?php if($lvlUser == 0){ ?>
        <tr>
            <td colspan="5" style="text-align:center;"><?php echo _VIEWUNREG; ?></td>
        </tr>
            <?php }
        else if(mysql_num_rows($tickets) == 0){ ?>
          <tr style="background: <?php echo $color_content1; ?>">
            <td colspan="5" style="text-align:center;"><?php echo _NOTICKETS; ?></td>
        </tr> <?php }
        else { $counter=0;
            while($t = mysql_fetch_assoc($tickets)){ ?>
        <tr <?php if($counter%2 ==0){ ?>style="background: <?php echo $color_content1; ?>" <?php } else { ?>  style="background: <?php echo $color_content2; ?>" <?php } ?> >
            <td><?php echo $t["id"]; ?></td>
            <td><?php echo $t["titre"]; ?></td>
            <td><?php $cat = getCatName($t["cat_id"]); echo $cat["nom"]; ?></td>
            <td><?php echo strftime("%x %H:%M", $t["date"]); ?></td>
            <td><a href="index.php?file=Support&amp;op=view&amp;id=<?php echo $t["id"]; ?>"><?php echo _CONSULT; if(!$closed) { echo '/'._REPLY;} ?></a> - <a href="index.php?file=Support&amp;op=<?php if(!$closed){echo "close";} else {echo "open";}?>&amp;id=<?php echo $t["id"]; ?>"><?php if(!$closed){echo _CLOSE;} else {echo _OPEN;}?></a></td>
        </tr>
        <?php  
           $counter++; }
        }
?>
        <tr></tr>
    </tbody>
</table>

<?php if(!$closed){ ?>
<br /><br /><br /><form method="post" action="index.php?file=Support&amp;op=post">
	<table style="margin-left: auto;margin-right: auto;text-align: left;" cellspacing="1" cellpadding="3" border="0">
	<tr><td align="center"><h3><?php echo _ADDTICKET; ?></h3></td></tr>
	<tr><td><b><?php echo _SUJET; ?> : </b>&nbsp;<input id="ns_sujet" type="text" name="sujet" value="" size="36" /></td></tr>
	<tr><td><b><?php echo _CAT; ?> : </b>&nbsp;
                <select name="cat" id="cat">
                    <?php $cat = recupCat(); while($c = mysql_fetch_assoc($cat)){ ?>
                   <option value="<?php echo $c["id"]; ?>"><?php echo $c["nom"]; ?></option>
                   <?php } ?>
                </select></td></tr>
        <tr><td><textarea class="editorsimpla" id="ns_corps" name="corps" cols="60" rows="12"></textarea></td></tr>
        <tr><td><b><?php echo _NOTIFYME; ?> : </b>&nbsp;<input type="checkbox" name="notify" id="notify" checked="checked" /></td></tr>
	<tr><td align="center"><br /><input type="submit" class="bouton" value="<?php echo _SEND; ?>" /></td></tr></table></form><br />
 <?php   }}

    
    function viewThread($thread_ID)
    {
        global $lvlUser, $nuked, $user, $color_content2, $color_content1, $color_top, $color_admin;
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else if($lvlUser == 0 || $thread["auteur_id"] != $user[0])
        {
            ?> <div style="text-align:center;"><h2><?php echo _PASPROPRIOTICKET; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else
        {
            $messages = recupThreadMessages($thread_ID);
        
        ?>
<div style="text-align:center;">
    <h2><?php echo _SUPPORT; ?></h2>
    <a href="index.php?file=Support<?php if($thread["closed"] == 1) { echo "&amp;op=index&amp;tickets=close"; } ?>">[ <?php echo _RETURNLISTTICKETS; ?> ]</a> - <a href="index.php?file=Support&amp;op=<?php if($thread["closed"] == 0){echo "close";} else {echo "open";}?>&amp;id=<?php echo $thread["id"]; ?>">[ <?php if($thread["closed"] == 0){echo _CLOSE;} else {echo _OPEN;} echo " "._THISTICKET; ?> ]</a>
    <h3><?php echo $thread["titre"]; ?></h3>
</div>
        <div style="width:98%; margin-left:auto; margin-right:auto;">
        <?php $counter=0; while($m = mysql_fetch_assoc($messages)){
            $sql = mysql_query("SELECT avatar FROM ". $nuked["prefix"] ."_users WHERE id = '".mysql_real_escape_string($m["auteur_id"])."' LIMIT 0,1");
            $user_avatar = mysql_fetch_assoc($sql);
            ?>

            <div id="message" style="width:100%; padding:1px; margin-bottom:3px; text-align:left; border:1px solid <?php echo $color_top; ?>; <?php if($counter != 0){ ?> border-top:0px; <?php } ?> background : <?php if($m["admin"] == 1){ echo $color_admin; } else if($counter%2 ==0){echo $color_content1;} else {echo $color_content2;} ?>; ">
                <div style="float:left; width:45px; height:45px; padding-right:3px; "><img src="<?php echo checkimg($user_avatar["avatar"]); ?>" width="45" height="45" alt="" /></div>
                <div>
                    <div style="left:4px; background: <?php echo $color_top; ?>"><?php if($m["admin"] == 0){ echo _YOUWROTE;} else { echo $m["auteur"] . _WROTE;} echo strftime("%x &agrave; %H:%M", $m["date"]) ?></div>
                    <div style="padding-left:4px;"><?php echo $m["texte"] ?></div>
                </div>
                <div style="clear:both;"></div>
                
            </div>
            <?php $counter++;
        } ?></div>

<br />
<?php if($thread["closed"] == 0) { ?>
<form method="post" action="index.php?file=Support&amp;op=reply">
    <table style="margin-left: auto;margin-right: auto;text-align: left;" cellspacing="1" cellpadding="3" border="0">
	<tr><td align="center"><h3><b><?php echo _REPLY; ?></b></h3><input type="text" style="display:none;" name="id" id="id" size="5" value="<?php echo $thread_ID; ?>" /></td></tr>
	<tr><td><textarea class="editorsimpla" id="ns_corps" name="corps" cols="60" rows="12"></textarea></td></tr>
	<tr><td align="center"><input type="submit" class="bouton" value="<?php echo _SEND; ?>"/></td></tr>
    </table>
</form><?php } 
    }
    }

    
    
    function close($thread_ID) {
        global $lvlUser, $nuked, $user;
        if(is_nan($thread_ID))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else if($lvlUser == 0 || $thread["auteur_id"] != $user[0])
        {
            ?> <div style="text-align:center;"><h2><?php echo _PASPROPRIOTICKET; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else { 
            $sql = mysql_query("UPDATE  ". $nuked["prefix"] ."_support_threads SET  `closed` =  '1' WHERE id = '". mysql_real_escape_string($thread_ID) ."' ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
            }
            else {
        ?>
<div style="text-align:center;">
    <h2><?php echo _SUPPORT; ?></h2>
    <h3><?php echo $thread["titre"]; ?></h3>
    <br /><br />
    <?php echo _CLOSESUCCESS; ?>
    <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br />
</div>

        <?php     
    }
    }
    }
    function open($thread_ID){
        global $lvlUser, $nuked, $user;
        if(is_nan($thread_ID))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else if($lvlUser == 0 || $thread["auteur_id"] != $user[0])
        {
            ?> <div style="text-align:center;"><h2><?php echo _PASPROPRIOTICKET; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php
        }
        else { 
            $sql = mysql_query("UPDATE  ". $nuked["prefix"] ."_support_threads SET  `closed` =  '0' WHERE id = '". mysql_real_escape_string($thread_ID) ."' ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
            }
            else {
        ?>
<div style="text-align:center;">
    <h2><?php echo _SUPPORT; ?></h2>
    <h3><?php echo $thread["titre"]; ?></h3>
    <br /><br />
    <?php echo _OPENSUCCESS; ?>
    <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br />
</div>

        <?php     
    }
    }
    }
    function addTicket($sujet, $corps, $cat, $notify)
    {
        global $lvlUser, $nuked, $user;
        if(is_nan($cat) OR is_null(getCatName($cat)))
        {
        ?> <div style="text-align:center;"><h2><?php echo _UNKNCAT; ?></h2></div><?php
        }
        else {
            $time= time();
        $sql = mysql_query("INSERT INTO ". $nuked["prefix"] ."_support_threads (titre, date, closed, auteur, auteur_id, cat_id, notify) VALUES ('". mysql_real_escape_string($sujet) ."', '". $time ."', '0', '". mysql_real_escape_string($user[2]) ."', '". mysql_real_escape_string($user[0]) ."', '". mysql_real_escape_string($cat) ."', '".$notify."') ");
        if(!$sql){
        ?> <div style="text-align:center;"><h2><?php echo _ERREUR."1   ". mysql_error($sql); ?></h2></div><?php
        }
        else {
            $sql = mysql_query("SELECT id FROM ". $nuked["prefix"] ."_support_threads WHERE date = '". $time ."' AND closed = '0' AND auteur_id = '". $user[0] ."' LIMIT 0,1 ");
            $sql = mysql_fetch_assoc($sql); 
            reply($sql["id"], $corps, 1);
        ?>
<div style="text-align:center;">
    <h2><?php echo _SUPPORT; ?></h2>
    <h3><?php echo $thread["titre"]; ?></h3>
    <br /><br />
    <?php echo _TICKETSUCCESS; ?>
    <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br />
</div>

        <?php     
    }
    }
    }
    
    function reply($thread_ID, $corps, $new=0)
    {
        global $lvlUser, $nuked, $user;
        if(is_nan($thread_ID))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php $new = 0;
        }
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            ?> <div style="text-align:center;"><h2><?php echo _TICKETDONTEXIST; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php $new = 0;
        }
        else if($lvlUser == 0 || $thread["auteur_id"] != $user[0])
        {
            ?> <div style="text-align:center;"><h2><?php echo _PASPROPRIOTICKET; ?></h2>
            <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br /></div><?php $new = 0;
        }
        else { 
            $requete = "INSERT INTO ". $nuked["prefix"] ."_support_messages (texte, date, auteur, auteur_id, auteur_ip, thread_id, admin)  VALUES ('". mysql_real_escape_string(secu_html(html_entity_decode($corps, ENT_QUOTES))) ."', '". time() ."', '". mysql_real_escape_string($user[2]) ."', '". mysql_real_escape_string($user[0]) ."', '". mysql_real_escape_string($user[3]) ."', '". mysql_real_escape_string($thread_ID) ."', '0')";
            $sql2 = mysql_query($requete);
            if(!$sql2){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR."   ".  mysql_errno($sql2)." : ".  mysql_error($sql2); ?></h2></div><?php
            }
            else if($new == 0) {
        ?>
<div style="text-align:center;">
    <h2><?php echo _SUPPORT; ?></h2>
    <h3><?php echo $thread["titre"]; ?></h3>
    <br /><br />
    <?php echo _REPLYSUCCESS; ?>
    <br /><br /><a href="javascript:history.back()"><b>[ <?php echo _BACK; ?> ]</b></a><br />
</div>

        <?php     
    }
    }
    }
    


    function recupTickets()
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". $nuked["prefix"] ."_support_threads WHERE auteur_id = '" . $user[0] . "' AND closed = 0 ORDER BY id DESC");
        return $sql;
    }
    function recupTicketsClose()
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". $nuked["prefix"] ."_support_threads WHERE auteur_id = '" . $user[0] . "' AND closed = 1 ORDER BY id DESC");
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

    	$sql = mysql_query("SELECT nom FROM ". $nuked["prefix"] ."_support_cat WHERE id = '" . $catID . "' ORDER BY id DESC LIMIT 0,1");
        $sql = mysql_fetch_assoc($sql);
        return $sql;
    }
    function sendmail($nom, $mail, $sujet, $corps)
    {
	global $nuked, $user_ip, $nuked;

    	$time = time();
    	$date = strftime("%x %H:%M", $time);
    	$contact_flood = $nuked['contact_flood'] * 60;

    	$sql = mysql_query("SELECT date FROM " . CONTACT_TABLE . " WHERE ip = '" . $user_ip . "' ORDER BY date DESC LIMIT 0, 1");
    	$count = mysql_num_rows($sql);
    	list($flood_date) = mysql_fetch_array($sql);
    	$anti_flood = $flood_date + $contact_flood;

    	if ($count > 0 && $time < $anti_flood)
    	{
	    echo "<br /><br /><div style=\"text-align: center;\">" . _FLOODCMAIL . "</big></div><br /><br />";
	    redirect("index.php", 3);
    	}
    	else
    	{
	    $nom = trim($nom);
	    $mail = trim($mail);
	    $sujet = trim($sujet);

	    $subjet = $sujet . ", " . $date;
	    $corp = $corps . "\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
	    $from = "From: " . $nom . " <" . $mail . ">\r\nReply-To: " . $mail . "\r\n";
	    $from.= "Content-Type: text/html\r\n\r\n";

	    if ($nuked['contact_mail'] != "") $email = $nuked['contact_mail'];
	    else $email = $nuked['mail'];	
		$corp = secu_html(html_entity_decode($corp));
		
	    mail($email, $subjet, $corp, $from);

	    $name = htmlentities($nom, ENT_QUOTES);
	    $email = htmlentities($mail, ENT_QUOTES);
	    $subject = htmlentities($sujet, ENT_QUOTES);
	    $text = secu_html(html_entity_decode($corps, ENT_QUOTES));

	    $add = mysql_query("INSERT INTO " . CONTACT_TABLE . " ( `id` , `titre` , `message` , `email` , `nom` , `ip` , `date` ) 
                VALUES ( '' , '" . $subject . "' , '" . $text . "' , '" . $email . "' , '" . $name . "' , '" . $user_ip . "' , '" . $time . "' )");
		$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$time."', '1', '"._NOTCON.": [<a href=\"index.php?file=Contact&page=admin\">lien</a>].')");
	    echo "<br /><br /><div style=\"text-align: center;\">" . _SENDCMAIL . "</div><br /><br />";
	    redirect("index.php", 3);
    	}
    }

    switch($_REQUEST['op']){

	case"sendmail":
	sendmail($_REQUEST['nom'], $_REQUEST['mail'], $_REQUEST['sujet'], $_REQUEST['corps']);
	break;

	case"index":
	
            if(isset($_REQUEST["tickets"]) AND $_REQUEST["tickets"] == "close")
            {
                index(1);
            }
            else {
                index();
            }
	break;
    
        case"view":
            viewThread($_REQUEST["id"]);
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
        case"post":
            if(isset($_POST['notify']) AND ($_POST['notify'] == 'on')){$notify = 1;} else { $notify = 0; }
            addTicket($_REQUEST["sujet"], $_REQUEST["corps"], $_REQUEST["cat"], $notify);
            break;

	default:
	index();
	break;
    }

} 
else if ($level_access == -1)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
} 
else if ($level_access == 1 && $visiteur == 0)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
} 
else
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
} 


echo "<br />";
closetable();

?>