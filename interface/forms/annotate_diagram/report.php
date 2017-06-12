<?php
 /**
 * Copyright Medical Information Integration,LLC info@mi-squared.com
 * 
 * LICENSE: This Source Code is subject to the terms of the Mozilla Public License, v. 2.0.
 * See the Mozilla Public License for more details.
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 *
 * Rewrite and modifications by sjpadgett@gmail.com Padgetts Consulting 2016.
 *
 * @package LibreHealth EHR
 * @author  Medical Information Integration,LLC <info@mi-squared.com>
 * @link    http://librehealth.io
 */
 
require_once(__DIR__.'/../../globals.php');
require_once($GLOBALS['srcdir'].'/api.inc');
require_once("$srcdir/options.inc.php");
?>
<html>
<head>
</head>
<style>body {
    background-color: white;
}
div .tab{
display:inline-block !important;
width:auto;height:auto;
}
.outer-container {
    margin: 10px;
}
#container {
    position: relative;
    border:2px solid black;
    box-sizing:border-box;
    min-width:1px;
    min-height:1px;
    display:inline-block;
    overflow: hidden;
}
.symcursor{ cursor: url("redblock.png") 8 8, auto;}
.txtcursor{ cursor: url("xhair.cur") 16 16, auto;}
.marker {
    position: absolute;    
    background-color: #FF8282;
    min-width:14px; 
    min-height: 14px;
    text-align:center;
}   
.count {
    font-size: 14px;    
    color: black;
    margin:2px;
}
.xmark {
    position: absolute;
    text-align:center;
}
.xcnt {
    font-size: 14px;    
    color: red;
    margin:2px;
}

.dytxt {
    display:none;
    background-color:lightgrey;
    position:absolute;
    width: 70px; height:16px; 
    font-size: 14px;
    color: red;
    margin:2px;
    
}
#mode {
    display: none;
    padding:4px;
    background-color: white;
    border:1px solid black;
}
.rblock {
    color:red;
    display: inline-block;
}

label, input { display:block; }
fieldset { padding:0; border:0; }
</style>
<body>
<?php
function annotate_diagram_report( $pid, $encounter, $cols, $formid){
    $x = array(); $y = array(); $label = ''; $detail = '';

    $fdata = formFetch("form_annotate_diagram", $formid);
    $imgname =(string)$GLOBALS['rootdir'].'/forms/annotate_diagram/'.($fdata['imagedata']);

    $tmp = stripcslashes ($fdata['data']);
    $coordinates = preg_split('/}/', $tmp, -1, PREG_SPLIT_NO_EMPTY);

    $i = 0;
    foreach( $coordinates as $idata => $coordinate ) {
        $coordinate = ltrim($coordinate, '/\'/');
        if( $coordinate ){
            $info = preg_split('/\^/', $coordinate, -1);
            $x[$i] = (int)$info[0]; $y[$i] = (int)$info[1]; $label = rtrim($info[2]); $detail[$i] = urldecode( $info[3]);
            $legend[$i]=$label;
            $i++;
        }
    }
/* Start image and markers print */
    echo "<div class='outer-container'>";
    echo"<div id='container' class='#container'>";
    print "<img src=$imgname id='main-img'/>";
    $arrlength=count($legend);
    for($c=0;$c<$arrlength;$c++){
      if($legend[$c] != ""){
        if( $legend[$c][0] != '' ){
            if($legend[$c] < '~'){
                echo '<div class="marker" style="top:'. attr($y[$c]).'px; left:'. attr($x[$c]).'px;">';
                echo '<span class="count">'. text($legend[$c]) . '</span>';
            }
            else{
                echo '<div class="xmark" style="top:'. attr($y[$c]).'px; left:'. attr($x[$c]).'px;">';
                echo '<span class="xcnt">'. text($legend[$c]) . '</span>';
            }
        }
        else{
            $ltmp = ltrim($legend[$c],"");
            echo '<div class="xmark" style="top:'. attr($y[$c]).'px; left:'. attr($x[$c]).'px;">';
            echo '<span class="xcnt">'. text($ltmp) . '</span>';
        }
      echo "</div>";
      }
    }
// Start label print
    echo "</div><div id='legend' class='legend'><div class='body'>";
    echo "<ul style='list-style-type:disc'>";
    for($c=0;$c<$arrlength;$c++){
        if(!isSpecial($legend[$c]))  
            echo "<li><span class='legend-item'><b>" . text(($legend[$c])) . "</b> " . text(($detail[$c])) . "</span></li>";
    }
    echo "</ul></div></div></div>";
// Done - clear 
    echo "<p style='clear: both' />";

}
function isSpecial($elem){
        if ( $elem > '~' || $elem[0] == '' || $elem == 'N'){
            return true;
        }
        else
            return false;
}
?>
</body>
</html>