<?
if (! $page) $page = "Pyro";

include("wikiparser.php");

$homename = "Pyro Robotics";
$home = "/";
$title = "Pyro, Python Robotics";
$buttons = array("Home" => "Pyro", 
		 "Software" => "PyroSoftware", 
		 "Curriculum" => "PyroCurriculum", 
		 "Hardware" => "PyroHardware",
		 "Community" => "PyroCommunity",
		 "What's New" => "PyroWhatsNew",
		 "Publications" => "PyroPublications",
                 "Search" => "FindPage"
		 );

?>

<html>
<head>
 <LINK rel=stylesheet href=stylesheet.css>
<?
print "<title>$title: $page</title>";
?>
 <meta http-equiv="Content-Type" content="text/html;">
</head>
<body bgcolor="#ffffff">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td width="804" colspan=8 align=center><img src="images/PyroLogo.gif" width="800" height="100"></td>
  </tr>
  <tr>
<? 

foreach ($buttons as $key => $value) {
  if ($page == $value) { // active page
    print "<td>[ $key ]</td>";
  } else { // another page is active
    if (substr($value, 0, 7) == "http://") {
      print "<td>[ <a href=\"$value\">$key</a> ]</td>";
    } else {
      print "<td>[ <a href=\"${home}?page=$value\">$key</a> ]</td>";
    }
  }
}

?>
  </tr>
</table>
<table width="804" border="0" cellpadding="0" cellspacing="0">
<tr><td colspan=8>
<?
$title = $page;
$page = str_replace("-", "_2d", $page);
$page = str_replace(".", "_2e", $page);
$page = str_replace(":", "_3a", $page);
$filename = "/var/moin/share/moin/data/text/" . $page;
if ($page and file_exists($filename)) {
  $fp = fopen($filename, "r");
  $bodytext = '';
  while(!feof($fp)) {
    $bodytext .= fread($fp, 4096);
  }
  if (preg_match("/#FORMAT python\n/", $bodytext)) {
    $text = preg_replace("/#FORMAT python\n/", "", $bodytext);
    print "<pre>\n$text\n</pre>\n";
  } else {
    $wikiparser = new WikiParser();
    $wikiparser->setURL("${home}?page=");
    print "<p><hr>";
    print $wikiparser->html($bodytext);
  }
} else {
  print "<hr align=left width=804>";
  print "Page does not yet exist";
}
print "<hr align=left width=804></td></tr>\n";
print "</table><table width=804 border=0 cellpadding=0 cellspacing=0>";
print "<tr>";

foreach ($buttons as $key => $value) {
  if ($page == $value) { // active page
    print "<td>[ $key ]</td>";
  } else { // another page is active
    if (substr($value, 0, 7) == "http://") {
      print "<td>[ <a href=\"$value\">$key</a> ]</td>";
    } else {
      print "<td>[ <a href=\"${home}?page=$value\">$key</a> ]</td>";
    }
  }
}
?>
</tr>
<tr><td colspan=8>
<br>
    <a href="http://creativecommons.org/licenses/by-sa/2.0/">
       <img alt="CreativeCommons" border="0" src="/html/somerights.gif">
    </a>
<?

print "<a href=http://emergent.brynmawr.edu/index.cgi/$page?action=show>View Wiki Source</a> | ";
print "<a href=http://emergent.brynmawr.edu/index.cgi/$page?action=edit>Edit Wiki Source (requires login, which anyone can do)</a> | ";
print "<br><a href=mailto:dblank@cs.brynmawr.edu>Webmaster@${homename}</a>";
?>
</td></tr>
</table>
</body>
</html>
