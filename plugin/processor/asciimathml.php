<?php
// Copyright 2005-2007 Won-Kyu Park <wkpark at kldp.org>
// All rights reserved. Distributable under GPL see COPYING
// a asciimathml processor plugin by AnonymousDoner
//
// Author: Won-Kyu Park <wkpark@kldp.org> and AnonymousDoner
// Date: 2007-11-02
// Name: a AsciiMathML processor
// Description: It support AsciiMathML
// URL: MoniWiki:AsciiMathML
// Version: $Revision$
// License: GPL
//
// please see http://kldp.net/forum/message.php?msg_id=9419
//
// download the following javascript in the local/ dir to enable this processor:
//  http://www1.chapman.edu/~jipsen/mathml/ASCIIMathML.js
//  and add small code or set $_add_func=1;
//-----x8-----
// function translateById(objId) {
//   AMbody = document.getElementById(objId);
//   AMprocessNode(AMbody, false);
//   if (isIE) { //needed to match size and font of formula to surrounding text
//     var frag = document.getElementsByTagName('math');
//     for (var i=0;i<frag.length;i++) frag[i].update()
//   }
// }
//   AMinitSymbols();
//-----x8-----
// to changes this processor as a default inline latex formatter:
// 1. set $inline_latex='asciimathml';
// 2. replace the latex processor: $processors=array('latex'=>'asciimathml');
//
// $Id$

function processor_asciimathml($formatter,$value="") {
  global $DBInfo;

  if ($value[0]=='#' and $value[1]=='!')
  list($line,$value)=explode("\n",$value,2);

  if ($line)
    list($tag,$args)=explode(' ',$line,2);

  $_add_func=1;

  $flag = 0;
  $bgcolor='';
  if (!$formatter->wikimarkup) {
    $cid=&$GLOBALS['_transient']['asciimathml'];
    if ( !$cid ) { $flag = 1; $cid = 1; }
    $id=$cid;
    $cid++;
  } else {
    $flag = 1;
    $id=md5($value.'.'.time());
    $bgcolor="mathbgcolor='yellow';\n";
  }

  if ( $flag ) {
    $js=qualifiedUrl($DBInfo->url_prefix .'/local/ASCIIMathML.js');
    $out .= "<script type=\"text/javascript\" src=\"$js\"></script>\n";
    if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
      $out.='<object id="mathplayer"'.
        ' classid="clsid:32F66A20-7614-11D4-BD11-00104BD3F987">'.
        '</object>'.
        '<?import namespace="mml" implementation="#mathplayer"?>';

    if ($_add_func)
      $out.=<<<AJS
<script type="text/javascript">
/*<![CDATA[*/
function translateById(objId,flag) {
  AMbody = document.getElementById(objId);
  math2ascii(AMbody); // for WikiWyg mode switching
$bgcolor
  AMprocessNode(AMbody, false);
  if (isIE) { //needed to match size and font of formula to surrounding text
    var frag = AMbody.getElementsByTagName('math')[0];
    frag.update()
  }
}

// AMinitSymbols();
/*]]>*/
</script>
AJS;
  }

  $out .= "<span><span class=\"AM\" id=\"AM-$id\">$value</span>" .
    "<script type=\"text/javascript\">translateById('AM-$id');".
    "</script></span>";
  return $out;
}

// vim:et:sts=2:sw=2
