<?php
/*
 *  Encounters report. (/interface/reports/encounters_report.php
 *  
 *
 *  This report shows past encounters with filtering and sorting, 
 *  Added filtering to show encounters not e-signed, encounters e-signed and forms e-signed.
 * 
 * Copyright (C) 2015-2017 Terry Hill <teryhill@librehealth.io> 
 * Copyright (C) 2007-2010 Rod Roark <rod@sunsetsystems.com>
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
 * @package LibreHealth EHR 
 * @author Terry Hill <teryhill@librehealth.io> 
 * @author Rod Roark <rod@sunsetsystems.com>
 * @link http://librehealth.io
 *
 */

require_once("../globals.php");
require_once $GLOBALS['srcdir'].'/headers.inc.php';
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
require_once "$srcdir/formdata.inc.php";
$DateFormat = DateFormatRead();
$DateLocale = getLocaleCodeForDisplayLanguage($GLOBALS['language_default']);

$alertmsg = ''; // not used yet but maybe later


function bucks($amount) {
  if ($amount) printf("%.2f", $amount);
}

function show_doc_total($lastdocname, $doc_encounters) {
  if ($lastdocname) {
    echo " <tr>\n";
    echo "  <td class='detail'>$lastdocname</td>\n";
    echo "  <td class='detail' align='right'>$doc_encounters</td>\n";
    echo " </tr>\n";
  }
}

$form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
$form_to_date = fixDate($_POST['form_to_date'], date('Y-m-d'));
$form_provider  = $_POST['form_provider'];
$form_facility  = $_POST['form_facility'];
$form_details   = $_POST['form_details'] ? true : false;
$form_new_patients = $_POST['form_new_patients'] ? true : false;
$form_esigned = $_POST['form_esigned'] ? true : false;
$form_not_esigned = $_POST['form_not_esigned'] ? true : false;
$form_encounter_esigned = $_POST['form_encounter_esigned'] ? true : false;


// Get the info.
//
    $esign_fields = '';
    $esign_joins = '';
  if ($form_encounter_esigned) {
    $esign_fields = ", es.table, es.tid ";
    $esign_joins = "LEFT OUTER JOIN esign_signatures AS es ON es.tid = fe.encounter ";
  }
    if ($form_esigned) {
    $esign_fields = ", es.table, es.tid ";
    $esign_joins = "LEFT OUTER JOIN esign_signatures AS es ON es.tid = fe.encounter ";
  }
  if ($form_not_esigned) {
    $esign_fields = ", es.table, es.tid ";
    $esign_joins = "LEFT JOIN esign_signatures AS es on es.tid = fe.encounter ";
  }

$query = "SELECT " .
  "fe.encounter, fe.date, fe.reason, " .
  "f.formdir, f.form_name, " .
  "p.fname, p.mname, p.lname, p.pid, p.pubpid, " .
  "u.lname AS ulname, u.fname AS ufname, u.mname AS umname " .
  "$esign_fields" .
  "FROM ( form_encounter AS fe, forms AS f ) " .
  "LEFT OUTER JOIN patient_data AS p ON p.pid = fe.pid " .
  "LEFT JOIN users AS u ON u.id = fe.provider_id " .
  "$esign_joins" .
  "WHERE f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'patient_encounter' ";
if ($form_to_date) {
  $query .= "AND fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_to_date 23:59:59' ";
} else {
  $query .= "AND fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_from_date 23:59:59' ";
}
if ($form_provider) {
  $query .= "AND fe.provider_id = '$form_provider' ";
}
if ($form_facility) {
  $query .= "AND fe.facility_id = '$form_facility' ";
}
if ($form_new_patients) {
  $query .= "AND fe.date = (SELECT MIN(fe2.date) FROM form_encounter AS fe2 WHERE fe2.pid = fe.pid) ";
}
if ($form_encounter_esigned) {
  $query .= "AND es.tid = fe.encounter AND es.table = 'form_encounter' ";
}
if ($form_esigned) {
  $query .= "AND es.tid = fe.encounter ";
}
if ($form_not_esigned) {
 $query .= "AND es.tid IS NULL ";
}

$res = sqlStatement($query);
?>
<html>
<head>
<?php html_header_show();?>
<title><?php echo xlt('Encounters Report'); ?></title>


<style type="text/css">

/* specifically include & exclude from printing */
@media print {
    #report_parameters {
        visibility: hidden;
        display: none;
    }
    #report_parameters_daterange {
        visibility: visible;
        display: inline;
    }
    #report_results table {
       margin-top: 0px;
    }
}

/* specifically exclude some from the screen */
@media screen {
    #report_parameters_daterange {
        visibility: hidden;
        display: none;
    }
}

</style>

<?php
   include_css_library("tablesorter-master/dist/css/theme.blue.min.css");
   include_css_library("jquery-datetimepicker/jquery.datetimepicker.css");
   include_js_library("jquery-min-3-1-1/index.js");
   include_js_library("jquery-datetimepicker/jquery.datetimepicker.full.min.js");
   include_js_library("tablesorter-master/dist/js/jquery.tablesorter.min.js");
   include_js_library("tablesorter-master/dist/js/jquery.tablesorter.widgets.min.js");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/report_helper.js"></script>

<script LANGUAGE="JavaScript">



 $(document).ready(function() {
  oeFixedHeaderSetup(document.getElementById('mymaintable'));
  var win = top.printLogSetup ? top : opener.top;
  win.printLogSetup(document.getElementById('printbutton'));
 });

 $(document).ready(function()
    {
        $("#mymaintable").tablesorter();
 }
);

 function refreshme() {
  document.forms[0].submit();
 }

</script>

</head>
<body class="body_top">
<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<span class='title'><?php echo xlt('Report'); ?> - <?php echo xlt('Encounters'); ?></span>

<div id="report_parameters_daterange">
    <?php date("d F Y", strtotime(oeFormatDateForPrintReport($_POST['form_from_date'])))
    . " &nbsp; to &nbsp; ". date("d F Y", strtotime(oeFormatDateForPrintReport($_POST['form_to_date']))); ?>
</div>

<form method='post' name='theform' id='theform' action='encounters_report.php'>

<div id="report_parameters">
<table>
 <tr>
  <td width='550px'>
    <div style='float:left'>

    <table class='text'>
        <tr>
            <td class='label'>
                <?php echo xlt('Facility'); ?>:
            </td>
            <td>
            <?php dropdown_facility($form_facility, 'form_facility', true); ?>
            </td>
            <td class='label'>
               <?php echo xlt('Provider'); ?>:
            </td>
            <td>
                <?php

                 // Build a drop-down list of providers.
                 //

                 $query = "SELECT id, lname, fname FROM users WHERE ".
                  "authorized = 1 $provider_facility_filter ORDER BY lname, fname"; //(CHEMED) facility filter

                 $ures = sqlStatement($query);

                 echo "   <select name='form_provider'>\n";
                 echo "    <option value=''>-- " . xlt('All') . " --\n";

                 while ($urow = sqlFetchArray($ures)) {
                  $provid = $urow['id'];
                  echo "    <option value='" . attr($provid) . "'";
                  if ($provid == $_POST['form_provider']) echo " selected";
                  echo ">" . text($urow['lname']) . ", " . text($urow['fname']) . "\n";
                 }

                 echo "   </select>\n";

                ?>
            </td>
            <td>
       <label><input type='checkbox' name='form_new_patients' title='First-time visits only'<?php  if ($form_new_patients) echo ' checked'; ?>>
        <?php  echo xlt('New'); ?></label>
            </td>
            <td>
               <label><input type='checkbox' name='form_esigned'<?php  if ($form_esigned) echo ' checked'; ?>>
               <?php  echo xlt('Forms Esigned'); ?></label>
            </td>
            <td>
               <label><input type='checkbox' name='form_encounter_esigned'<?php  if ($form_encounter_esigned) echo ' checked'; ?>>
               <?php  echo xlt('Encounter Esigned'); ?></label>
            </td>
        </tr>
        <tr>
            <td class='label'>
               <?php echo xlt('From'); ?>:
            </td>
            <td>
               <input type='text' name='form_from_date' id="form_from_date" size='10'
                      value='<?php echo htmlspecialchars(oeFormatShortDate(attr($form_from_date))) ?>'>
            </td>
            <td class='label'>
               <?php echo xlt('To'); ?>:
            </td>
            <td>
               <input type='text' name='form_to_date' id="form_to_date" size='10'
                      value='<?php echo htmlspecialchars(oeFormatShortDate(attr($form_to_date))) ?>'>
            </td>
            <td>
               <label><input type='checkbox' name='form_details'<?php  if ($form_details) echo ' checked'; ?>>
               <?php echo xlt('Details'); ?></label>
            </td>
            <td>
               <label><input type='checkbox' name='form_not_esigned'<?php  if ($form_not_esigned) echo ' checked'; ?>>
               <?php echo xlt('Not Esigned'); ?></label>
            </td>
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

</div> <!-- end report_parameters -->

<?php
 if ($_POST['form_refresh']) {
?>
<div id="report_results">
<table id='mymaintable' class="tablesorter">

 <thead>
<?php if ($form_details) { ?>
  <th><?php echo xlt('Provider'); ?></th>
  <th><?php echo xlt('Date'); ?></th>
  <th><?php echo xlt('Patient'); ?></th>
  <th><?php echo xlt('ID'); ?></th>
  <th><?php echo xlt('Status'); ?></th>
  <th><?php echo xlt('Encounter'); ?></th>
  <th><?php echo xlt('Encounter Number'); ?></th>
  <th><?php echo xlt('Form'); ?></th>
  <th><?php echo xlt('Coding'); ?></th>
<?php } else { ?>
  <th><?php echo xlt('Provider'); ?></td>
  <th><?php echo xlt('Encounters'); ?></td>
<?php } ?>
 </thead>
 <tbody>
<?php
if ($res) {
  $lastdocname = "";
  $doc_encounters = 0;
  while ($row = sqlFetchArray($res)) {
    $patient_id = $row['pid'];

    $docname = '';
    if (!empty($row['ulname']) || !empty($row['ufname'])) {
      $docname = $row['ulname'];
      if (!empty($row['ufname']) || !empty($row['umname']))
        $docname .= ', ' . $row['ufname'] . ' ' . $row['umname'];
    }

    $errmsg  = "";
    if ($form_details) {
      // Fetch all other forms for this encounter.
      $encnames = '';      
      $encarr = getFormByEncounter($patient_id, $row['encounter'],
        "formdir, user, form_name, form_id");
      if($encarr!='') {
          foreach ($encarr as $enc) {
            if ($enc['formdir'] == 'patient_encounter') continue;
            if ($encnames) $encnames .= '<br />';
            $encnames .= text($enc['form_name']); // need to html escape it here for output below
          }
      }     

      // Fetch coding and compute billing status.
      $coded = "";
      $billed_count = 0;
      $unbilled_count = 0;
      if ($billres = getBillingByEncounter($row['pid'], $row['encounter'],
        "code_type, code, code_text, billed"))
      {
        foreach ($billres as $billrow) {
          // $title = addslashes($billrow['code_text']);
          if ($billrow['code_type'] != 'COPAY' && $billrow['code_type'] != 'TAX') {
            $coded .= $billrow['code'] . ', ';
            if ($billrow['billed']) ++$billed_count; else ++$unbilled_count;
          }
        }
        $coded = substr($coded, 0, strlen($coded) - 2);
      }

      // Figure product sales into billing status.
      $sres = sqlStatement("SELECT billed FROM drug_sales " .
        "WHERE pid = '{$row['pid']}' AND encounter = '{$row['encounter']}'");
      while ($srow = sqlFetchArray($sres)) {
        if ($srow['billed']) ++$billed_count; else ++$unbilled_count;
      }

      // Compute billing status.
      if ($billed_count && $unbilled_count) $status = xl('Mixed' );
      else if ($billed_count              ) $status = xl('Closed');
      else if ($unbilled_count            ) $status = xl('Open'  );
      else                                  $status = xl('Empty' );
?>
 <tr bgcolor='<?php echo $bgcolor ?>'>
  <td><?php echo text($docname) //echo ($docname == $lastdocname) ? "" : text($docname) ?>&nbsp;</td>
  <td><?php echo text(oeFormatShortDate(substr($row['date'], 0, 10))) ?>&nbsp;</td>
  <td><?php echo text($row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname']); ?>&nbsp;</td>
  <td><?php echo text($row['pid']); ?>&nbsp;</td>
  <td><?php echo text($status); ?>&nbsp;</td>
  <td><?php echo text($row['reason']); ?>&nbsp;</td>
  <td><?php echo text($row['encounter']); ?>&nbsp;</td>
  <td><?php echo $encnames; //since this variable contains html, have already html escaped it above ?>&nbsp;</td>
  <td><?php echo text($coded); ?></td>
 </tr>
<?php
    } else {
      if ($docname != $lastdocname) {
        show_doc_total($lastdocname, $doc_encounters);
        $doc_encounters = 0;
      }
      ++$doc_encounters;
    }
    $lastdocname = $docname;

    }
  if (!$form_details) show_doc_total($lastdocname, $doc_encounters);
}
?>
</tbody>
</table>
</div>  <!-- end encresults -->
<?php } else { ?>
<div class='text'>
    <?php echo xlt('Please input search criteria above, and click Submit to view results.' ); ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo xlt('Column headers can be clicked to change sort order') ?>
</div>
<?php } ?>

<input type='hidden' name='form_refresh' id='form_refresh' value=''/>

</form>
</body>

<script language='JavaScript'>

<?php if ($alertmsg) { echo " alert('$alertmsg');\n"; } ?>

</script>
<script>
    $(function() {
        $("#form_from_date").datetimepicker({
            timepicker: false,
            format: "<?= $DateFormat; ?>"
        });
        $("#form_to_date").datetimepicker({
            timepicker: false,
            format: "<?= $DateFormat; ?>"
        });
        $.datetimepicker.setLocale('<?= $DateLocale;?>');
    });

</script>

</html>
