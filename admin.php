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
                continue;
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
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _TICKETDONTEXIST . "\n"
            . "</div>\n"
            . "</div>\n"; 
            redirect("javascript:history.back()",5);
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
            $sql = mysql_query("SELECT avatar FROM ". mysql_real_escape_string($nuked["prefix"]) ."_users WHERE id = '".mysql_real_escape_string($m["auteur_id"])."' LIMIT 0,1");
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
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Support.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
    <div class="tab-content" id="tab2"> <?php
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _TICKETDONTEXIST . "\n"
            . "</div>\n"
            . "</div>\n"; 
            redirect("javascript:history.back()",5);
        }
        else if(empty($corps)){echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _UNKSUJETCORPS . "\n"
            . "</div>\n"
            . "</div>\n";
            redirect("javascript:history.back()",5);
        }
        
        else { 
            $sql = mysql_query("INSERT INTO ". mysql_real_escape_string($nuked["prefix"]) ."_support_messages (texte, date, auteur, auteur_id, auteur_ip, thread_id, admin) VALUES ('". mysql_real_escape_string($corps) ."', '". time() ."', '". mysql_real_escape_string($user[2]) ."', '". mysql_real_escape_string($user[0]) ."', '". mysql_real_escape_string($user[3]) ."', '". mysql_real_escape_string($thread_ID) ."', '1') ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
            redirect("javascript:history.back()",5);
            }
            else {         
                
                if($thread["notify"]){
                    $sql_user_mail = mysql_query("SELECT * FROM ". USER_TABLE ." WHERE id = '$user[0]' LIMIT 0,1 ");
                    $sql_user_mail = mysql_fetch_assoc($sql_user_mail);
                    sendmail($user[2], $thread["titre"], $thread_ID, secu_html(html_entity_decode($sql_user_mail["mail"], ENT_QUOTES)));
                }
        ?>
<div class="notification success png_bg">
	<div>
	<?php echo _REPLYSUCCESS."\n".$corps; ?>
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
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Support.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
    <div class="tab-content" id="tab2"><?php 
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _TICKETDONTEXIST . "\n"
            . "</div>\n"
            . "</div>\n"; 
            redirect("javascript:history.back()",5);
        }
        else { 
            $sql = mysql_query("UPDATE  ". mysql_real_escape_string($nuked["prefix"]) ."_support_threads SET  `closed` =  '1' WHERE id = '". mysql_real_escape_string($thread_ID) ."' ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
            redirect("javascript:history.back()",5);
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
        <div style="text-align:right;"><a href="help/<?php echo $language; ?>/Support.php" rel="modal">
	<img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" /></a>
	</div></div>
    <div class="tab-content" id="tab2"><?php 
        $thread = recupThread($thread_ID);
        if(empty($thread["id"]))
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _TICKETDONTEXIST . "\n"
            . "</div>\n"
            . "</div>\n"; 
            redirect("javascript:history.back()",5);
        }
        else { 
            $sql = mysql_query("UPDATE  ". mysql_real_escape_string($nuked["prefix"]) ."_support_threads SET  `closed` =  '0' WHERE id = '". mysql_real_escape_string($thread_ID) ."' ");
            if(!$sql){
            ?> <div style="text-align:center;"><h2><?php echo _ERREUR; ?></h2></div><?php
                redirect("javascript:history.back()",5);
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

       
	
	<table style="margin-left: auto;margin-right: auto;text-align: left;" border="0" cellspacing="0" cellpadding="3">
	<tr><td align="center"><b><?php echo _PREFS; ?></b></td></tr>
        <tr><td>
            <form method="post" action="index.php?file=Support&amp;page=admin&amp;op=pref_insert_cat">
            <?php echo _INSERTCAT; ?><br /><div style="padding-left:10px;"><br />
            <label for="categorie_insert"><?php echo _NEWCATNAME;?></label><input type="text" id="categorie_insert" name="categorie_insert" size="40" /><br /><br />
            <label for="ordre_insert"><?php echo _ORDRE;?></label><input type="text" name="ordre_insert" id="ordre_insert" size="2" /><br /><br /><input type="submit" value="<?php echo _SEND; ?>" /></div>
            </form>
        </td></tr>
        <tr><td>
            <form method="post" action="index.php?file=Support&amp;page=admin&amp;op=pref_rename_cat">
            <?php echo _RENAMECAT; ?><br /><div style="padding-left:10px;"><br /><label for="categorie_old"><?php echo _CATAMODIF;?></label><select name="categorie_old" id="categorie_old">
                <?php $cats = recupCat(); while($cat = mysql_fetch_assoc($cats)){ ?> <option value="<?php echo $cat["id"];?>"><?php echo $cat["nom"]." (".$cat["ordre"].")";?></option><?php } ?>
            </select><br /><br />
            <label for="categorie_rename"><?php echo _NEWCATNAME;?></label><input type="text" id="categorie_rename" name="categorie_rename" size="40" /><br /><br />
            <label for="ordre_rename"><?php echo _ORDRE;?></label><input type="text" name="ordre_rename" id="ordre_rename" size="2" /><br /><br /><input type="submit" value="<?php echo _SEND; ?>" /></div>
            </form>
        </td></tr>
	<tr><td>
            <form method="post" action="index.php?file=Support&amp;page=admin&amp;op=pref_delete_cat">
            <?php echo _DELETECAT; ?><br /><div style="padding-left:10px;"><br /><label for="categorie_delete"><?php echo _DELETETHISCAT;?></label>
                <select name="categorie_delete" id="categorie_delete">
                <?php $cats = recupCat(); while($cat = mysql_fetch_assoc($cats)){ ?> <option value="<?php echo $cat["id"];?>"><?php echo $cat["nom"];?></option><?php } ?>
            </select><br /><br /><input type="submit" value="<?php echo _SEND; ?>" /></div>
            </form>
        </td></tr></table>
	<div style="text-align: center;"><br />[ <a href="index.php?file=Support&amp;page=admin"><b><?php echo  _BACK; ?></b></a> ]</div><br /></div></div>
<?php    } 

    
    function insert_cat($nom, $ordre){
        global $nuked;
        if(is_nan($ordre)){$ordre = 1;}
        echo mysql_real_escape_string(html_entity_decode($nom));
        $sql = mysql_query("INSERT INTO ". mysql_real_escape_string($nuked["prefix"]) ."_support_cat (nom, ordre) VALUES ( '".mysql_real_escape_string($nom)."', '".mysql_real_escape_string($ordre)."') ");
        echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _CATADDED . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Support&page=admin", 2);
        
    }
	
    function rename_cat($old_cat, $nom, $ordre){
        global $nuked;
        if(is_nan($ordre)){$ordre = 1;}
        if(is_nan($old_cat)){echo "<div class=\"notification error png_bg\">\n"
		. "<div>\n"
		. "" . _ERREUR . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Support&page=admin&op=main_pref", 2);}
        $sql = mysql_query("UPDATE ". mysql_real_escape_string($nuked["prefix"]) ."_support_cat SET nom = '".mysql_real_escape_string($nom)."', ordre = '".mysql_real_escape_string($ordre)."' WHERE id = '".mysql_real_escape_string($old_cat)."' ");
        echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _CATRENAMED . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Support&page=admin", 2);
        
    }
	
    function delete_cat($id){
        global $nuked;
        $erreur = 0;
        $cat = recupCat();
        while($c = mysql_fetch_assoc($cat)){$erreur++;}
        if($erreur > 1){$erreur = false;} else {$erreur = true;}
        
        if(is_nan($id) OR $erreur OR $id <1){echo "<div class=\"notification error png_bg\">\n"
		. "<div>\n"
		. "" . _ERREUR . "\n";
		if($erreur){ echo "<br />"._ERRNOCAT . "\n";}
		echo "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Support&page=admin&op=main_pref", 5);}
        else {
            $sql = mysql_query("DELETE FROM ". mysql_real_escape_string($nuked["prefix"]) ."_support_cat WHERE id = '".mysql_real_escape_string($id)."' ");
            echo "<div class=\"notification success png_bg\">\n"
                    . "<div>\n"
                    . "" . _CATDELETED . "\n"
                    . "</div>\n"
                    . "</div>\n";
            redirect("index.php?file=Support&page=admin", 2);}
    }

    
    
    function recupTicketsCat($cat_ID)
    {
	global $nuked;
        if(is_nan($cat_ID)){return 0;}
    	$sql = mysql_query("SELECT * FROM ". mysql_real_escape_string($nuked["prefix"]) ."_support_threads WHERE cat_id = '" . mysql_real_escape_string($cat_ID) . "' AND closed = 0 ORDER BY id DESC");
        return $sql;
    }
    function recupTicketsCatClose($cat_ID)
    {
	global $nuked;
        if(is_nan($cat_ID)){return 0;}
    	$sql = mysql_query("SELECT * FROM ". mysql_real_escape_string($nuked["prefix"]) ."_support_threads WHERE cat_id = '" . mysql_real_escape_string($cat_ID) . "' AND closed = 1 ORDER BY id DESC");
        return $sql;
    }
    function recupThreadMessages($thread_id)
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". mysql_real_escape_string($nuked["prefix"]) ."_support_messages WHERE thread_id = '" . mysql_real_escape_string($thread_id) . "' ORDER BY date ASC");
        return $sql;
    }
    function recupCat()
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". mysql_real_escape_string($nuked["prefix"]) ."_support_cat ORDER BY ordre ASC");
        return $sql;
    }
    function recupThread($thread_id)
    {
	global $nuked, $user;
    	$sql = mysql_query("SELECT * FROM ". mysql_real_escape_string($nuked["prefix"]) ."_support_threads WHERE id = '" . mysql_real_escape_string($thread_id) . "' ORDER BY id DESC LIMIT 0,1");
        $sql = mysql_fetch_assoc($sql);
        return $sql;
    }
    function getCat($catID)
    {
	global $nuked;
        if(is_nan($cat_ID)){return 0;}
    	$sql = mysql_query("SELECT * FROM ". mysql_real_escape_string($nuked["prefix"]) ."_support_cat WHERE id = '" . mysql_real_escape_string($catID) . "' ORDER BY id DESC LIMIT 0,1");
        $sql = mysql_fetch_assoc($sql);
        return $sql;
    }
    
    function sendmail($auteur, $sujet, $id, $mail)
    {
	global $nuked, $user_ip, $nuked;

    	$time = time();
    	$date = strftime("%x %H:%M", $time);
    	$url_site = mysql_query("SELECT name, value FROM ". mysql_real_escape_string($nuked["prefix"]) ."_config WHERE name = 'url' LIMIT 0,1");
		if($url_site){
        $url_site = mysql_fetch_assoc($url_site);
        
    	
        $auteur = trim($auteur);
        $mail = trim($mail);
        $sujet = trim($sujet);
        
        $corps = "<p>Bonjour,<br />vous avez re&ccedil;u une r&eacute;ponse &agrave; votre ticket de support \"<b>$sujet</b>\" de la part de $auteur.<br /><br />Vous pouvez consulter la r&eacute;ponse &agrave; l'adresse : <a href=\"".$url_site["value"]."index.php?file=Support&amp;op=view&amp;id=$id\">".$url_site["value"]."index.php?file=Support&amp;op=view&amp;id=$id</p>";


        $subjet = $nuked['name']." - Notification de r&eacute;ponse au ticket : ".$sujet;
        $corp = $corps . "\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
        $from = "From: " . $nuked["name"] . " - Notifications  <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'] . "\r\n";
        $from.= "Content-Type: text/html\r\n\r\n";

        $corp = secu_html(html_entity_decode($corp));

        mail($mail, $subjet, $corp, $from);

        echo "<br /><br /><div style=\"text-align: center;\">" . _SENDCMAIL . "</div><br /><br />";
		}
		else { echo "<br /><br /><div style=\"text-align: center;\">" . _SENDFMAIL . "</div><br /><br />"; }
        redirect("index.php?file=Support&amp;page=admin", 3);
    	
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

	case "pref_insert_cat":
	insert_cat($_REQUEST['categorie_insert'], $_REQUEST['ordre_insert']);
	break;
    
	case "pref_rename_cat":
	rename_cat($_REQUEST['categorie_old'], $_REQUEST['categorie_rename'], $_REQUEST['ordre_rename']);
	break;
    
	case "pref_delete_cat":
	delete_cat($_REQUEST['categorie_delete']);
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