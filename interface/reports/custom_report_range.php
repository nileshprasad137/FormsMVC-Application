<?php
/*
 * Superbill Report
 * 
 * Copyright (C) 2017 Terry Hill <teryhill@librehealth.io> 
 * No other copyright information in the previous header
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3 
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * LICENSE: This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0
 * See the Mozilla Public License for more details.
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package LibreHealth EHR 
 * @author  Brady Miller <brady@sparmy.com>
 * @author Terry Hill <teryhill@librehealth.io> 
 * @link    http://librehealth.io
 */

$fake_register_globals=false;
$sanitize_all_escapes=true;

require_once(dirname(__file__)."/../globals.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/report.inc");
require_once("$srcdir/classes/Document.class.php");
require_once("$srcdir/classes/Note.class.php");
require_once("$srcdir/formatting.inc.php");
$DateFormat = DateFormatRead(true);
$DateLocale = getLocaleCodeForDisplayLanguage($GLOBALS['language_default']);

$startdate = $enddate = "";
if(empty($_POST['start']) || empty($_POST['end'])) {
    // set some default dates
    $startdate = date($DateFormat, (time() - 30*24*60*60));
    $enddate = date($DateFormat, time());
}
else {
    // set dates
    $startdate = $_POST['start'];
    $enddate = $_POST['end'];
}
//Patient related stuff
if ($_POST["form_patient"])
$form_patient = isset($_POST['form_patient']) ? $_POST['form_patient'] : '';
$form_pid = isset($_POST['form_pid']) ? $_POST['form_pid'] : '';
if ($form_patient == '' ) $form_pid = '';
?>
<html>

<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../library/css/jquery.datetimepicker.css">
<style>

@media print {
    .title {
        visibility: hidden;
    }
    .pagebreak {
        page-break-after: always;
        border: none;
        visibility: hidden;
    }

    #superbill_description {
        visibility: hidden;
    }

    #report_parameters {
        visibility: hidden;
    }
    #superbill_results {
       margin-top: -30px;
    }
}

@media screen {
    .title {
        visibility: visible;
    }
    #superbill_description {
        visibility: visible;
    }
    .pagebreak {
        width: 100%;
        border: 2px dashed black;
    }
    #report_parameters {
        visibility: visible;
    }
}
#superbill_description {
   margin: 10px;
}
#superbill_startingdate {
    margin: 10px;
}
#superbill_endingdate {
    margin: 10px;
}

#superbill_patientdata {
}
#superbill_patientdata h1 {
    font-weight: bold;
    font-size: 1.2em;
    margin: 0px;
    padding: 5px;
    width: 100%;
    background-color: #eee;
    border: 1px solid black;
}
#superbill_insurancedata {
    margin-top: 10px;
}
#superbill_insurancedata h1 {
    font-weight: bold;
    font-size: 1.2em;
    margin: 0px;
    padding: 5px;
    width: 100%;
    background-color: #eee;
    border: 1px solid black;
}
#superbill_insurancedata h2 {
    font-weight: bold;
    font-size: 1.0em;
    margin: 0px;
    padding: 0px;
    width: 100%;
    background-color: #eee;
}
#superbill_billingdata {
    margin-top: 10px;
}
#superbill_billingdata h1 {
    font-weight: bold;
    font-size: 1.2em;
    margin: 0px;
    padding: 5px;
    width: 100%;
    background-color: #eee;
    border: 1px solid black;
}
#superbill_signature {
}
#superbill_logo {
}
</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery-1.9.1.min.js"></script>

<script language="Javascript">
 $(document).ready(function() {
  var win = top.printLogSetup ? top : opener.top;
  win.printLogSetup(document.getElementById('printbutton'));
 });

// CapMinds :: invokes  find-patient popup.
 function sel_patient() {
  dlgopen('../main/calendar/find_patient_popup.php?pflag=0', '_blank', 500, 400);
 }

// CapMinds :: callback by the find-patient popup.
 function setpatient(pid, lname, fname, dob) {
  var f = document.theform;
  f.form_patient.value = lname + ', ' + fname;
  f.form_pid.value = pid;

 }
</script>
</head>

<body class="body_top">

<span class='title'><?php echo xlt('Reports'); ?> - <?php echo xlt('Superbill'); ?></span>

<div id="superbill_description" class='text'>
<?php echo xlt('Superbills, sometimes referred to as Encounter Forms or Routing Slips, are an essential part of most medical practices.'); ?>
</div>

<div id="report_parameters">

<form method="post" name="theform" id='theform' action="custom_report_range.php">
<input type='hidden' name='form_refresh' id='form_refresh' value=''/>
<table>
 <tr>
  <td width='650px'>
    <div style='float:left'>

    <table class='text'>
        <tr>
            <td class='label'>
               <?php echo xlt('Start Date'); ?>:
            </td>
            <td>
               <input type='text' name='start' id="form_from_date" size='10' value='<?= htmlspecialchars($startdate); ?>'>
            </td>
            <td class='label'>
               <?php echo xlt('End Date'); ?>:
            </td>
            <td>
               <input type='text' name='end' id="form_to_date" size='10' value='<?= htmlspecialchars($enddate); ?>'>
            </td>

            <td>
            &nbsp;&nbsp;<span class='text'><?php echo xlt('Patient'); ?>: </span>
            </td>
            <td>
            <input type='text' size='20' name='form_patient' style='width:100%;cursor:pointer;cursor:hand' value='<?php echo attr($form_patient) ? attr($form_patient) : xla('Click To Select'); ?>' onclick='sel_patient()' title='<?php echo xla('Click to select patient'); ?>' />
            <input type='hidden' name='form_pid' value='<?php echo attr($form_pid); ?>' />
            </td>
            </tr>
            <tr><td>
        </tr>
    </table>

    </div>

  </td>
  <td align='left' valign='middle' height="100%">
    <table style='border-left:1px solid; width:100%; height:100%' >
        <tr>
            <td>
                <div style='margin-left:15px'>
                    <a href='#' class='css_button' onclick='$("#form_refresh").attr("value","true"); $("#theform").submit();'>
                    <span>
                        <?php echo xlt('Submit'); ?>
                    </span>
                    </a>

                    <?php if ($_POST['form_refresh']) { ?>
                    <a href='#' class='css_button' id='printbutton'>
                        <span>
                            <?php echo xlt('Print'); ?>
                        </span>
                    </a>
                    <?php } ?>
                </div>
            </td>
        </tr>
    </table>
  </td>
 </tr>
</table>
</div> <!-- end of parameters -->

</form>

<div id="superbill_results">

<?php
if( !(empty($_POST['start']) || empty($_POST['end']))) {
    $sql = "select * from facility where billing_location = 1";
    $db = $GLOBALS['adodb']['db'];
    $results = $db->Execute($sql);
    $facility = array();
    if (!$results->EOF) {
        $facility = $results->fields;
?>
<p>
<h2><?php text($facility['name'])?></h2>
<?php text($facility['street'])?><br>
<?php text($facility['city'])?>, <?php text($facility['state'])?> <?php text($facility['postal_code'])?><br>

</p>
<?php
    }
        $sqlBindArray = array();
        $res_query =    "select * from forms where " .
                        "form_name = 'Patient Encounter' and " .
                        "date between ? and ? " ;
                array_push($sqlBindArray, prepareDateBeforeSave($startdate), prepareDateBeforeSave($enddate));
        if($form_pid) {
        $res_query.= " and pid=? "; 
        array_push($sqlBindArray,$form_pid);
        }
        $res_query.=     " order by date DESC" ;
        $res =sqlStatement($res_query,$sqlBindArray);
    
    while($result = sqlFetchArray($res)) {
        if ($result{"form_name"} == "Patient Encounter") {
            $patient_encounter[] = $result{"form_id"}.":".$result{"encounter"};
            $pids[] = $result{"pid"};   
        }
    }
    $N = 6;

    function postToGet($patient_encounter, $pids) {
        $getstring="";
        $serialpatient_encounter = serialize($patient_encounter);
        $serialpids = serialize($pids);
        $getstring = "patient_encounter=".urlencode($serialpatient_encounter)."&pids=".urlencode($serialpids);

        return $getstring;
    }

    $iCounter = 0;
    if(empty($patient_encounter)){ $patient_encounter = array(); }
    foreach($patient_encounter as $patient){
        /*
        $inclookupres = sqlStatement("select distinct formdir from forms where pid='".$pids[$iCounter]."'");
        while($result = sqlFetchArray($inclookupres)) {
            include_once("{$GLOBALS['incdir']}/forms/" . $result{"formdir"} . "/report.php");
        }
        */

        print "<div id='superbill_patientdata'>";
        print "<h1>".xlt('Patient Data').":</h1>";
        printRecDataOne($patient_data_array, getRecPatientData ($pids[$iCounter]), $N);
        print "</div>";

        print "<div id='superbill_insurancedata'>";
        print "<h1>".xlt('Insurance Data').":</h1>";
        print "<h2>".xlt('Primary').":</h2>";
        printRecDataOne($insurance_data_array, getRecInsuranceData ($pids[$iCounter],"primary"), $N);
        print "<h2>".xlt('Secondary').":</h2>";
        printRecDataOne($insurance_data_array, getRecInsuranceData ($pids[$iCounter],"secondary"), $N);
        print "<h2>".xlt('Tertiary').":</h2>";
        printRecDataOne($insurance_data_array, getRecInsuranceData ($pids[$iCounter],"tertiary"), $N);
        print "</div>";

        print "<div id='superbill_billingdata'>";
        print "<h1>".xlt('Billing Information').":</h1>";
        if (count($patient) > 0) {
            $billings = array();
            echo "<table width='100%'>";
            echo "<tr>";
            echo "<td class='bold' width='10%'>".xlt('Date')."</td>";
            echo "<td class='bold' width='20%'>".xlt('Provider')."</td>";
            echo "<td class='bold' width='40%'>".xlt('Code')."</td>";
            echo "<td class='bold' width='10%'>".xlt('Fee')."</td></tr>\n";
            $total = 0.00;
            $copays = 0.00;
            //foreach ($patient as $be) {

                $ta = explode(":",$patient);
                $billing = getPatientBillingEncounter($pids[$iCounter],$ta[1]);

                $billings[] = $billing;
                foreach ($billing as $b) {
                    // grab the date to reformat it in the output
                    $bdate = strtotime($b['date']);

                    echo "<tr>\n";
                    echo "<td class='text' style='font-size: 0.8em'>" . oeFormatShortDate(date("Y-m-d",$bdate)) . "<BR>" . date("h:i a", $bdate) . "</td>";
                    echo "<td class='text'>" . text($b['provider_name']) . "</td>";
                    echo "<td class='text'>";
                    echo text($b['code_type']) . ":\t" . text($b['code']) . "&nbsp;". text($b['modifier']) . "&nbsp;&nbsp;&nbsp;" . text($b['code_text']) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo "</td>\n";
                    echo "<td class='text'>";
                    echo oeFormatMoney($b['fee']);
                    echo "</td>\n";
                    echo "</tr>\n";
                    $total += $b['fee'];
                }
            // Calculate the copay for the encounter
            $copays = getPatientCopay($pids[$iCounter],$ta[1]);
            //}
            echo "<tr><td>&nbsp;</td></tr>";
            echo "<tr><td class='bold' colspan=3 style='text-align:right'>".xlt('Sub-Total')."</td><td class='text'>" . oeFormatMoney($total + abs($copays)) . "</td></tr>";
            echo "<tr><td class='bold' colspan=3 style='text-align:right'>".xlt('Copay Paid')."</td><td class='text'>" . oeFormatMoney(abs($copays)) . "</td></tr>";
            echo "<tr><td class='bold' colspan=3 style='text-align:right'>".xlt('Total')."</td><td class='text'>" . oeFormatMoney($total) . "</td></tr>";
            echo "</table>";
            echo "<pre>";
            //print_r($billings);
            echo "</pre>";
        }
        echo "</div>";

        ++$iCounter;
        print "<br/><br/>".xlt('Physician Signature').":  _______________________________________________";
        print "<hr class='pagebreak' />";
    }
}
    ?>
</div>

    </body>

<script type="text/javascript" src="../../library/js/jquery.datetimepicker.full.min.js"></script>
<script>
    $(function() {
        $("#form_from_date").datetimepicker({
            timepicker: true,
            format: "<?= $DateFormat; ?>"
        });
        $("#form_to_date").datetimepicker({
            timepicker: true,
            format: "<?= $DateFormat; ?>"
        });
        $.datetimepicker.setLocale('<?= $DateLocale;?>');
    });

</script>
</html>
