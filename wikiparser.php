<?

// This class parses a string from a wiki (specifically the rules
// as defined by Moinmoin wiki) into HTML.
// D. S. Blank

function url($url) {
  Header ( "Location: $url" );
  echo "<HTML><HEAD><TITLE>Redirect</TITLE></HEAD><BODY>" .
    "Please go <A target=_top HREF=\"" . $url . "\">here</A>.</BODY></HTML>.\n";
  exit;
}

if (! $page) {
  if ($_SERVER["argv"][0]) {
    $page = $_SERVER["argv"][0];
  } else {
    $page = "FrontPage";
  }
} 
if ($page == "RecentChanges" || 
    $page == "FindPage" || 
    $page == "WikiSandBox" || 
    $page == "HelpIndex"
    ) {
  url("http://emergent.brynmawr.edu/index.cgi/" . $page);
}

class WikiParser {
  var $rule_count = 0;
  var $counter = 0;
  var $currentOrderedSpaces = 0;
  var $currentUnorderedSpaces = 0;
  var $rule = array();
  var $repl = array();
  var $option = array();
  var $wiki_url = "";
  var $wikidir = "/var/moin/share/moin/data/text/";
  var $lastHeaderNum = 0;
  var $level = array();
  var $section_numbers = 1;
  //var $wiki_url = "/emergent/";

  function makeLevelHeading($max) {
    if ($this->section_numbers == 0) return "";
    $retval = "";
    for ($i = 1; $i <= $max; $i++) {
      if ($this->level[$i] == 0)
	$this->level[$i] = 1;
      $retval .= $this->level[$i] . ".";
    }
    return $retval . " ";
  }

  function preformat($text) {
    $text = preg_replace("'&#36;&#36;&#36;(.*)&#36;&#36;&#36;'", "<font color=\"red\">$1</font>", $text);
    $text = preg_replace("'&#36;&#36;(.*)&#36;&#36;'", "<font color=\"blue\">$1</font>", $text);
    $text = preg_replace("/\\\'\\\'\\\'(.*)\\\'\\\'\\\'/", "<b>$1</b>", $text);
    return "<pre class=\"code\">" . $text . "</pre>";
  }

  function setURL($url) {
    $this->wiki_url = $url;
  }

  function addRule( $match, $replacement, $option = 0) {
    $this->rule[$this->rule_count] = $match;
    $this->repl[$this->rule_count] = $replacement;
    $this->option[$this->rule_count] = $option;
    $this->rule_count++;
  }

  function memoize($num, $o1, $o2, $o3, $o4) {
    //if ($num == -1) { // wiki system replacement before <> subs
    //$this->counter += 1;
    //$this->actual[$this->counter] = $o1;
    //return "&&&&$this->counter&&&&";
    //} else 
    if ($this->option[$num] == 1) { // function
      eval( '$retval = ' . $this->repl[$num] . ';');
      if ($retval != $o1) { // this should only happen with funcs
	$this->counter += 1;
	$this->actual[$this->counter] = $retval;
	if ($o3[0] == "=") {
	  return "\n\n<<<$this->counter>>>";
	}
	return "<<<$this->counter>>>";
      } else {
	return $retval;
      }
    } else if ($this->option[$num] == 0) { // string
      $this->counter += 1;
      eval( '$this->actual[$this->counter] = "' . $this->repl[$num] . '";');
      return "<<<$this->counter>>>";
    } else if ($this->option[$num] == 2) { // immediate, no memoize
      eval( '$retval = "' . $this->repl[$num] . '";');
      return str_replace("\'", "'", $retval);
    } else if ($this->option[$num] == 3) { // preface string with newline
      $this->counter += 1;
      eval( '$this->actual[$this->counter] = "' . $this->repl[$num] . '";');
      return "\n<<<$this->counter>>>";
    }
  }
  
  function applyRule($num, $text) {
    //print "APPLYING RULE $num: " . $this->rule[$num] ."<br>";
    $text = preg_replace($this->rule[$num] . 'e', 
            "\$this->memoize($num, \"\$1\",\"\$2\",\"\$3\",\"\$4\")", 
            $text);
    return $text;
  }

  function handlePragma($command) {
    if ($command == "section-numbers off") 
      $this->section_numbers = 0;
    return "";
  }

  function handleFormat($command) {
    if ($command == "python") 
      return "<pre>";
    return "";
  }

  function headerNum($level, $text) {
    if ($this->lastHeaderNum == $level) { // increment current level
      $this->level[$level]++;
    } else if ($this->lastHeaderNum < $level) { // start new level
      $this->level[$level] = 1;
    } else if ($this->lastHeaderNum > $level) { // return to a previous level
      $this->level[$level]++;
      // clear all higher than level
      for ($i = $level + 1; $i <= 5; $i++) {
	$this->level[$i] = 0;
      }
    }
    $levelString = $this->makeLevelHeading($level);
    $this->lastHeaderNum = $level;
    return "<h$level width=804>$levelString $text</h$level>";
  }

  function WikiParser() {
    // ORDER MATTERS
    $titleSearch = "";
    $fullSearch = "<a href=http://emergent.brynmawr.edu/index.cgi/FindPage>Go here for full search functions</a>";
    $goto = '<table border="0" cellspacing="0" cellpadding="0">
<form method="POST" action="FindPage">
<input type="hidden" name="action" value="inlinesearch">

<a href="/index.cgi/FindPage?action=edit">EditText</a>

of this page
(last modified 2004-01-09 12:39:15)
<br>
<a href="/index.cgi/FindPage?value=FindPage">FindPage</a>
 by browsing, title search <input style="font-family:Verdana; font-size:9pt;" type="text" name="text_title" value="" size="15" maxlength="50"><input type="image" src="/wiki/img/moin-search.png" name="button_title"  alt="[?]" hspace="3" width="12" height="12" border="0">, text search <input style="font-family:Verdana; font-size:9pt;" type="text" name="text_full" value="" size="15" maxlength="50"><input type="image" src="/wiki/img/moin-search.png" name="button_full"  alt="[?]" hspace="3" width="12" height="12" border="0"> or an index<br>
Or try one of these actions: 
<a href="/index.cgi/FindPage?action=LikePages">LikePages</a>, 
<a href="/index.cgi/FindPage?action=LocalSiteMap">LocalSiteMap</a>, 
<a href="/index.cgi/FindPage?action=SpellCheck">SpellCheck</a><br>

</td></tr>
</form>
</table>';
    $this->addRule("'\n#pragma (.*)'", '$this->handlePragma($o1)', 1); 
    $this->addRule("'\n#redirect (.*)'", '"This page has been relocated at " . $this->wikiname($o1, 1)', 1); 
    $this->addRule("'\n#acl .*'", ""); 
    $this->addRule("'\n##.*'", ""); 
    $this->addRule("'\[\[BR\]\]'",'<p>');
    // [[SpecialWords]]
    $this->addRule("'\[\[TitleSearch\]\]'", $titleSearch);
    $this->addRule("'\[\[GoTo\]\]'", $goto);
    $this->addRule("'\[\[fullSearch\]\]'", $fullSearch);
    $this->addRule("'\[\[FullSearch\(\)\]\]'", $fullSearch);
    // used to just take out:
    //$this->addRule("'\[\[.*\]\]'",'');
    // inline:
    $this->addRule("'{{{([^}\n]*)}}}'", '<TT class=\"wiki\">$o1</TT>');    
    // code block:         
    //$this->addRule("'\{\{\{(.*)\}\}\}'Us", '<pre class=\"code\">$o1</pre>');
    $this->addRule("'\{\{\{(.*)\}\}\}'Us", '$this->preformat($o1, 1)', 1);
    // [http word]:
    $this->addRule("'\[http://(\S*)\]'", "<a href=http://\$o1><img border=0 width=11 src=/wiki/img/moin-www.png>&nbsp;\$o1</a>"); 
    $this->addRule("'\[http://(\S*) (.*)\]'U", "<a href=http://\$o1><img border=0 width=11 src=/wiki/img/moin-www.png>&nbsp;\$o2</a>"); 
    $this->addRule("'\[file://(\S*) (.*)\]'U", "<a href=file://\$o1><img border=0 width=11 src=/wiki/img/moin-www.png>&nbsp;\$o2</a>"); 
    // images:
    $this->addRule("'http://(\S*\.gif)'i", '<img src=http://$o1>');  // gif
    $this->addRule("'http://(\S*\.png)'i",'<img src=http://$o1>');   // png
    $this->addRule("'http://(\S*\.jpg)'i",'<img src=http://$o1>');   // jpg
    $this->addRule("'http://(\S*\.jpeg)'i",'<img src=http://$o1>');   // jpg
    // html:
    $this->addRule("'http://([a-zA-Z0-9./\-~#\?\&\_]*)'", '<a href=http://$o1>$o1</a>');
    // HR: FIX: allow multiple widths
    $this->addRule("'\n(==*) (.*) (==*)'", '$this->headerNum(strlen($o1),$o2,$o3)', 1); 
/*
    $this->addRule("'\n(=) (.*) (=)'", '$this->headerNum(strlen($o1),$o2,$o3)', 1);
    $this->addRule("'\n(==) (.*) (==)'", '$this->headerNum(strlen($o1),$o2,$o3)', 1); 
    $this->addRule("'\n(===) (.*) (===)'", '$this->headerNum(strlen($o1),$o2,$o3)', 1); 
    $this->addRule("'\n(====) (.*) (====)'", '$this->headerNum(strlen($o1),$o2,$o3)', 1); 
    $this->addRule("'\n(=====) (.*) (=====)'", '$this->headerNum(strlen($o1),$o2,$o3)', 1); 
*/
    $this->addRule("'\n----*'", "<hr>\n", 3); 
    // color:
    $this->addRule("/&#36;&#36;&#36;(.*?)&#36;&#36;&#36;/",'<font color=\"red\">$o1</font>', 2);  // immediate
    $this->addRule("/&#36;&#36;(.*?)&#36;&#36;/",'<font color=\"blue\">$o1</font>', 2);  // immediate
    // bold:
    $this->addRule("/'''(.*?)'''/",'<b>$o1</b>', 2);  // immediate
    // italic: 
    $this->addRule("/''(.*?)''/", '<i>$o1</i>', 2);   // immediate
    // smile1:
    $this->addRule("'\s\:\)\s'", " <img width=11 src=/wiki/img/smile.png> ");  
    // smile2:
    $this->addRule("'\sB\)\s'", " <img width=11 src=/wiki/img/smile2.png> ");  
    // smile3:
    $this->addRule("'\s\:\)\)\s'", " <img width=11 src=/wiki/img/smile3.png> ");  
    // smile4:
    $this->addRule("'\s\;\)\s'", " <img width=11 src=/wiki/img/smile4.png> ");  
    // smile5:
    $this->addRule("'\s\:D\s'", " <img width=11 src=/wiki/img/biggrin.png> ");  
    // smile6:
    $this->addRule("'\s\&lt\;\:\(\s'", " <img width=11 src=/wiki/img/frown.png> ");  
    // smile7:
    $this->addRule("'\sX\-\(\s'", " <img width=11 src=/wiki/img/angry.png> ");  
    // smile:
    $this->addRule("'\s\:o\s'", " <img width=11 src=/wiki/img/redface.png> ");  
    // smile:
    $this->addRule("'\s\:\(\s'", " <img width=11 src=/wiki/img/sad.png> ");  
    // alert:
    $this->addRule("'\s\/!\\\\\s'", " <img width=11 src=/wiki/img/alert.png> ");  
    // attention:
    $this->addRule("'\s\&lt\;\!\&gt\;\s'", " <img width=11 src=/wiki/img/attention.png> ");   
    // idea:
    $this->addRule("'\s\(\!\)\s'", " <img width=11 src=/wiki/img/idea.png> ");
    // smile:
    $this->addRule("'\s\:\-\?\s'"," <img width=11 src=/wiki/img/tongue.png> ");
    // smile:
    $this->addRule("'\s\:\\\\\s'"," <img width=11 src=/wiki/img/ohwell.png> ");
    // smile:
    $this->addRule("'\s\&gt\;\:\&gt\;\s'",
		   " <img width=11 src=/wiki/img/devil.png> ");
    // smile:
    $this->addRule("'\s\%\)\s'", " <img width=11 src=/wiki/img/eyes.png> ");  
    // smile:
    $this->addRule("'\s\@\)\s'", " <img width=11 src=/wiki/img/eek.png> ");  
    // smile:
    $this->addRule("'\s\|\)\s'", " <img width=11 src=/wiki/img/tired.png> ");  
    // smile:
    $this->addRule("'\s\;\)\)\s'", " <img width=11 src=/wiki/img/lol.png> ");  
    // [[HTML]]:
    $this->addRule("'\[\[([^\]]*)\]\]'",'<a href=' . $this->wiki_url . '$o1>$o1</a>');
    // ["WikiName"]:
    $this->addRule("'\[\&quot\;(.*?)\&quot\;]'", '$this->wikiname($o1, 1)', 1); // func
    // wikiwords, other strings:
    $this->addRule("'([\w\.\/\@\:]+)'", '$this->wikiname($o1)', 1); // func
    //$this->addRule("'\n'", "", 3); 
  }

  function wikiname($o1, $force = 0) {
    if ($force) {
      $this->counter += 1;
      $d1 = str_replace(" ", "_20", $o1);
      $d1 = str_replace("/", "_2f", $d1);
      $this->actual[$this->counter] = "<a href=" . $this->wiki_url . "${d1}>${o1}</a>";
      return "<<<$this->counter>>>";
    }
    $extra = "";
    if (substr($o1, -1) == "." ||
	substr($o1, -1) == "?" ||
	substr($o1, -1) == ",") {
      $extra = substr($o1, -1);
      $o1 = substr($o1, 0, -1);
    }
    if ($o1 != strtoupper($o1) &&
        $o1 != strtolower($o1) &&
        $o1 != ucfirst(strtolower($o1)) &&
        $o1 == ucfirst($o1) &&
	$o1 != strtoupper( substr($o1, 0, -1)) . "s" &&
        (strpos($o1, '/') === false) &&  // FIX: this can happen in inter wiki names
        (strpos($o1, ".") === false) &&
        (strpos($o1, "@") === false) 
	) {
      $this->counter += 1;
      if (strpos($o1, ':') == false) {
	$d1 = str_replace(" ", "_20", $o1);
	$d1 = str_replace("/", "_2f", $d1);
	$this->actual[$this->counter] = "<a href=" . $this->wiki_url . "${d1}>${o1}</a>";
      } else {
	$parts = split(":", $o1);
	$this->actual[$this->counter] = "<a href=http://c2.com/cgi/wiki?" . $parts[1] . "><img src=/wiki/img/moin-inter.png border=0> " . $parts[1] . "</a>";
      }
      return "<<<$this->counter>>>$extra";
    } else if (strpos($o1, "@")) { // email address
      $this->counter += 1;
      $this->actual[$this->counter] = "<a href=mailto:$o1>$o1</a>";
      return "<<<$this->counter>>>$extra";
    } else {
      return "${o1}${extra}"; // just return word
    }
  }

  function getFile($filename) {
    $filename = str_replace("-", "_2d", $filename);
    $filename = str_replace(".", "_2e", $filename);
    $text = '';
    $pre = 0;
    $fp = @fopen($this->wikidir . $filename, "r");
    if ($fp > 0) {
      while(!feof($fp)) {
	$tmp = fread($fp, 4096);
	if (strtoupper(substr($tmp, 0, 7)) == "#FORMAT") {
	  $text .= "{{{\n";
	  $text .= substr($tmp, 15);
	  $pre = 1;
	} else {
	  $text .= $tmp;
	}
      }
      if ($pre) {
	$text .= "\n}}}\n";
	$text .= "[[http://emergent.brynmawr.edu/emergent/${filename}?action=raw download]] " .
	  "[[http://emergent.brynmawr.edu/emergent/${filename}?action=edit edit]]\n";
      }
      //$text .= $this->memoize(2, "<a href=" . $this->wiki_url . "${filename}?action=raw>[download]</a>", 0, 0, 0);
      //$retval = $this->memoize(-1, "<a href=" . $this->wiki_url . "${filename}?action=raw>${o1}</a>", 0, 0, 0);
      //$text .= $retval . "\n";
      return $text;
    } else {
      return "file not found: $filename";
    }
  }

  function html($text) {
    // get all includes:
    $text = $this->pass0($text);
    // replace items with <<<code>>>:
    $text = $this->pass1("\n" . $text);
    // handle lists and tables:
    $text = $this->pass2($text);
    // replace <<<code>>> with items:
    $text = $this->pass3($text); 
    $text = $this->pass3($text); 
    $text = $this->pass3($text); 
    return $text;
  }
  
  function pass0($text) {
    // [[Include(PyroModulesContents)]]
    $text = preg_replace("'\[\[Include\(([^\]]*)\)\]\]'" . 'e', 
			 "\$this->pass0(\$this->getFile(\"\$1\"))", 
			 $text);
    return $text;
  }

  function pass1($text) {
    $text = str_replace('"', '&quot;', $text); // replace \" with double quote
    $text = str_replace('<', '&lt;', $text); 
    $text = str_replace('>', '&gt;', $text); 
    $text = str_replace('$', '&#36;', $text); 
    for ($i = 0; $i < count($this->rule); $i++) {
      $text = $this->applyRule($i, $text);
    }
    return $text;
  }

  function pass3($text) {
    for ($i = 1; $i <= count($this->actual); $i++) {
      $text = preg_replace("'\<\<\<" . ($i+0) . "\>\>\>'", 
			   str_replace("\'", "'", $this->actual[$i]), 
			   $text);
    }
    return $text;
  }

  function pass2($text) {
    // FIX: this could be reduced
    $lines = split("\n", $text);
    $depth = array();
    $depth[0] = '';
    $depth_type = array();
    $depth_type[0] = '';
    $table = 0;
    for ($i = 0; $i < count($lines); $i++) {
      if (preg_match("'\s*\|\|\|\|\|\|(.*?)\|\|$'", $lines[$i], $o)) { // table
	$lines[$i] = "";
	if ($table == 0)
	  $lines[$i] .= "<table border=1>";
	$table = 1;
	// || text || <td> text </td>
	// |||| text || <td span=2> text </td>
	$o[1] = str_replace("||||", "</td><td colspan=2 align=center>", $o[1]);
	$row = str_replace("||", "</td><td>", $o[1]);
	$lines[$i] .= "<tr><td colspan=3 align=center>$row</td></tr>";
      } else if (preg_match("'\s*\|\|\|\|(.*?)\|\|$'", $lines[$i], $o)) { // table
	$lines[$i] = "";
	if ($table == 0)
	  $lines[$i] .= "<table border=1>";
	$table = 1;
	// || text || <td> text </td>
	// |||| text || <td span=2> text </td>
	$o[1] = str_replace("||||", "</td><td colspan=2 align=center>", $o[1]);
	$row = str_replace("||", "</td><td>", $o[1]);
	$lines[$i] .= "<tr><td colspan=2 align=center>$row</td></tr>";
      } else if (preg_match("'\s*\|\|(.*?)\|\|$'", $lines[$i], $o)) { // table 
	$lines[$i] = "";
	if ($table == 0)
	  $lines[$i] .= "<table border=1>";
	$table = 1;
	// || text || <td> text </td>
	// |||| text || <td span=2> text </td>
	$o[1] = str_replace("||||||", "</td><td colspan=3 align=center>", $o[1]);
	$o[1] = str_replace("||||", "</td><td colspan=2 align=center>", $o[1]);
	$row = str_replace("||", "</td><td>", $o[1]);
	$lines[$i] .= "<tr><td>$row</td></tr>";
      } else if (preg_match("'^(\s+)(\d+)\.(\#[^\s]*)*(.*)$'", $lines[$i], $o)) { 
	// numbered list:
	if ($o[3][0] == '#')
	  $o[3] = substr($o[3], 1);
	$lines[$i] = '';
	if (strlen($o[1]) > $depth[count($depth) - 1]) {
	  $lines[$i] .= "<ol type=\"$o[2]\"";
	  if ($o[3]) 
	    $lines[$i] .= " start=\"$o[3]\"";
	  $lines[$i] .= ">";
	  $depth[count($depth)] = strlen($o[1]); 
	  $depth_type[count($depth_type)] = "</ol>";
	} else if (strlen($o[1]) < $depth[count($depth) - 1]) {
	  while (strlen($o[1]) < $depth[count($depth) - 1]) {
	    $lines[$i] .= $depth_type[count($depth_type)-1];
	    array_pop($depth);
	    array_pop($depth_type);
	  }
	}
	$lines[$i] .= "<li>" . trim($o[4]) . "</li>";
      } else if (preg_match("'^(\s+)([aAiI]*)\.(\#[^\s]*)*(.*)$'", 
			    $lines[$i], $o)) {
	// lettered list:
	if ($o[3][0] == '#')
	  $o[3] = substr($o[3], 1);
	$lines[$i] = '';
	if (strlen($o[1]) > $depth[count($depth) - 1]) {
	  $lines[$i] .= "<ol type=\"$o[2]\"";
	  if ($o[3]) 
	    $lines[$i] .= " start=\"$o[3]\"";
	  $lines[$i] .= ">";
	  $depth[count($depth)] = strlen($o[1]); 
	  $depth_type[count($depth_type)] = "</ol>";
	} else if (strlen($o[1]) < $depth[count($depth) - 1]) {
	  while (strlen($o[1]) < $depth[count($depth) - 1]) {
	    $lines[$i] .= $depth_type[count($depth_type)-1];
	    array_pop($depth);
	    array_pop($depth_type);
	  }
	}
	$lines[$i] .= "<li>" . trim($o[4]) . "</li>";
      } else if (preg_match("'^(\s+)\*(.*)$'", $lines[$i], $o)) {
	// unordered list:
	$lines[$i] = '';
	if (strlen($o[1]) > $depth[count($depth) - 1]) {
	  $lines[$i] .= "<ul>";
	  $depth[count($depth)] = strlen($o[1]); 
	  $depth_type[count($depth_type)] = "</ul>";
	} else if (strlen($o[1]) < $depth[count($depth) - 1]) {
	  while (strlen($o[1]) < $depth[count($depth) - 1]) {
	    $lines[$i] .= $depth_type[count($depth_type)-1];
	    array_pop($depth);
	    array_pop($depth_type);
	  }
	}
	$lines[$i] .= "<li>" . trim($o[2]) . "</li>";
      } else if (preg_match("'^(\s+)(.*)$'", $lines[$i], $o)) { 
	// indented list:
	$lines[$i] = '';
	if (strlen($o[1]) > $depth[count($depth) - 1]) {
	  $lines[$i] .= "<ol>";
	  $depth[count($depth)] = strlen($o[1]); 
	  $depth_type[count($depth_type)] = "</ol>";
	} else if (strlen($o[1]) < $depth[count($depth) - 1]) {
	  while (strlen($o[1]) < $depth[count($depth) - 1]) {
	    $lines[$i] .= $depth_type[count($depth_type)-1];
	    array_pop($depth);
	    array_pop($depth_type);
	  }
	}
	$lines[$i] .= "<P>$o[2]";
      } else if (preg_match("'^\s*$'", $lines[$i], $o)) { // newline
	$lines[$i] = "";
	if ($table == 1) {
	  $lines[$i] .= "</table>";
	  $table = 0;
	}
	while (count($depth) > 1) {
	  $lines[$i] .= $depth_type[count($depth_type)-1];
	  array_pop($depth);
	  array_pop($depth_type);
	}
	$lines[$i] .= "<P>";
      } else {// else don't bother, it isn't a line we're interested in
	if ($table == 1) {
	  $lines[$i] = "</table>" . $lines[$i];
	  $table = 0;
	}
      }
    }
    return join($lines, "\n");
  }
}
?>