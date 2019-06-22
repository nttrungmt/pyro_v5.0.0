<?

include("wikiparser.php");

$homename = "Pyro Robotics";
$home = "/";
$title = "Pyro, Python Robotics";
?>

<html>
<head>
 <LINK rel=stylesheet href=stylesheet.css>
<?
print "<title>$title: DIY Documentation</title>";
?>
 <meta http-equiv="Content-Type" content="text/html;">
</head>
<body bgcolor="#ffffff">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td width="804" colspan=7 align=center><img src="images/PyroLogo.gif" width="800" height="100"></td>
  </tr>
</table>
<table width="804" border="0" cellpadding="0" cellspacing="0">
<tr><td colspan=7>
<?

 function prettyname($page) {
   $page = str_replace("_2d", "-", $page);
   $page = str_replace("_2e", ".", $page);
   $page = str_replace("_3a", ":", $page);
   $page = str_replace("_20", " ", $page);
   return $page;
 }

 function getwikipage($page) {
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
 }

$data = $_POST["page"];
$wikipage = Array();
$wikipage[1] = "Pyro";
$wikipage[2] = "PyroSoftware";
$wikipage[3] = "PyroLiveCD";
$wikipage[4] = "PyroInstallation";
$wikipage[5] = "PyroFAQ";
$wikipage[6] = "PyroDeveloperCVS";
$wikipage[7] = "PyroScreenShots";
$wikipage[8] = "PyroCurriculum";
$wikipage[9] = "PyroCurriculumExamples";
$wikipage[10] = "PyroModuleIntroduction";
$wikipage[11] = "Pyro_20Robot_20Morphology";
$wikipage[12] = "Pyro_20Effectors";
$wikipage[13] = "The_20Pyro_20Interface";
$wikipage[14] = "Robot_20Attributes";
$wikipage[15] = "Robot_20Simulators";
$wikipage[16] = "The_20Pyrobot_20Simulator";
$wikipage[17] = "The_20Stage_20Simulator";
$wikipage[18] = "The_20Gazebo_20Simulator";
$wikipage[19] = "The_20Robocup_20Simulator";
$wikipage[20] = "Robot_20Hardware";
$wikipage[21] = "Using_20the_20Pioneer_20Robot";
$wikipage[22] = "Using_20the_20Hemisson_20Robot";
$wikipage[23] = "Using_20the_20Sony_20AIBO_20Robot";
$wikipage[24] = "Interfacing_20Your_20Own_20Robot";
$wikipage[25] = "Pyro_20Sensors";
$wikipage[26] = "Pyro_20Devices";
$wikipage[27] = "Pyro_20Devices Advanced";
$wikipage[28] = "Pyro_20Brains";
$wikipage[29] = "PyroModuleObjectOverview";
$wikipage[30] = "PyroModulePythonIntro";
$wikipage[31] = "PyroModuleDirectControl";
$wikipage[32] = "PyroModuleSequencingControl";
$wikipage[33] = "ExamplePickingUpPucks";
$wikipage[34] = "PyroModuleFSM:UsingGenerators";
$wikipage[35] = "PyroModuleBehaviorBasedControl";
$wikipage[36] = "PyroModuleReinforcementLearning";
$wikipage[37] = "PyroModuleNeuralNetworks";
$wikipage[38] = "PyroModuleEvolutionaryAlgorithms";
$wikipage[39] = "PyroFromPython";
$wikipage[40] = "PyroModuleComputerVision";
$wikipage[41] = "Introduction_20to_20Computer_20Vision";
$wikipage[42] = "PyroModuleVisionSystem";
$wikipage[43] = "PyroVisionSystemFunctions";
$wikipage[44] = "Simulated_20Vision_20using_20FakeCamera";
$wikipage[45] = "Using_20Khepera_20with_20a_20Camera";
$wikipage[46] = "PyroModuleMapping";
$wikipage[47] = "PyroModuleMultirobot";
$wikipage[48] = "ExampleChase";
$wikipage[49] = "ExampleMultipleBrains";
$wikipage[50] = "FurtherReading";
$wikipage[51] = "PyroIndex";
$wikipage[52] = "PyroAdvancedTopics";
$wikipage[53] = "PyroModuleFSM:UsingGenerators";
$wikipage[54] = "PyroModuleNeuralNetworksAdvanced";
$wikipage[55] = "PyroModuleSelfOrganizingMap";
$wikipage[56] = "PyroModuleRAVQ";
$wikipage[57] = "PyroModuleCA";
$wikipage[58] = "PyroModuleAI:Search";
$wikipage[59] = "PyroModuleAI:GamePlaying";
$wikipage[60] = "PyroUserManual";
$wikipage[61] = "PyroSiteNotes ";
$wikipage[62] = "PyroSiteNotesBrynMawr";
$wikipage[63] = "PyroModulePioneerRobot ";
$wikipage[64] = "PyroHardware";
$wikipage[65] = "Using_20the_20Pioneer_20Robot";
$wikipage[66] = "Using_20the_20Hemisson_20Robot";
$wikipage[67] = "Using_20the_20Sony_20AIBO_20Robot";
$wikipage[68] = "Interfacing_20Your_20Own_20Robot";
$wikipage[69] = "The_20Pyrobot_20Simulator";
$wikipage[70] = "The_20Stage_20Simulator";
$wikipage[71] = "The_20Gazebo_20Simulator";
$wikipage[72] = "The_20Robocup_20Simulator";
$wikipage[73] = "PyroCommunity";
$wikipage[74] = "PyroUsers";
$wikipage[75] = "PyroWhatsNew";
$wikipage[76] = "PyroPublications";
$wikipage[77] = "Introduction_20to_20Neural_20Nets";
$wikipage[78] = "Building_20Neural_20Networks_20using_20Conx";
$wikipage[79] = "Generalization_20in_20a_20Neural_20Network";
$wikipage[80] = "Autoassociative_20and_20Recurrent_20Networks";
$wikipage[81] = "SRNModuleExperiments";
$wikipage[82] = "Incremental_20Neural_20Networks";
$wikipage[83] = "Robot_20Learning_20using_20Neural_20Networks";
$wikipage[84] = "Conx_20Implementation_20Details";

asort($data);

echo "<h1>Pyro Documentation</h1>\n";
echo "<h3>Table of Contents</h3>\n";
echo "<ol>\n";
foreach ($data as $key => $p) { 
  if ($p) {
    echo "<li> <a href=#" . $wikipage[$key] . ">" . prettyname($wikipage[$key]) . "</a>\n";
  }
}
echo "</ol>\n";

foreach ($data as $key => $p) { 
  if ($p) {
    echo "<a name=\"" . $wikipage[$key] . "\">\n";
    getwikipage($wikipage[$key]);
  }
}

?>
</tr>
<tr><td colspan=7>
<br>
    <a href="http://creativecommons.org/licenses/by-sa/2.0/">
       <img alt="CreativeCommons" border="0" src="/html/somerights.gif">
    </a>
<?
print "<a href=http://emergent.brynmawr.edu/index.cgi/$page?action=show>ViewWiki</a> | ";
print "<a href=http://emergent.brynmawr.edu/index.cgi/$page?action=edit>EditWiki</a> | ";
print "<a href=mailto:dblank@cs.brynmawr.edu>Webmaster@${homename}</a>";
?>
</td></tr>
</table>
</body>
</html>
