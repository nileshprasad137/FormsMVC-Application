<?php
/*
 * Insurance Allocation report
 *
 * This module shows relative insurance usage by unique patients
 * that are seen within a given time period.  Each patient that had
 * a visit is counted only once, regardless of how many visits.
 *
 * Copyright (C) 2016-2017 Terry Hill <teryhill@librehealth.io> 
 * 
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
 * @author Terry Hill <teryhill@librehealth.io> 
 * @link http://librehealth.io 
 */

 include_once("../globals.php");
 include_once("../../library/patient.inc");
 include_once("../../library/acl.inc");
 require_once("../../library/formatting.inc.php");
 $DateFormat = DateFormatRead();
 $DateLocale = getLocaleCodeForDisplayLanguage($GLOBALS['language_default']);


 $from_date = fixDate($_POST['form_from_date']);
 $to_date   = fixDate($_POST['form_to_date'], date('Y-m-d'));

if ($_POST['form_csvexport']) {
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Type: application/force-download");
  header("Content-Disposition: attachment; filename=insurance_distribution.csv");
  header("Content-Description: File Transfer");
  // CSV headers:
  if (true) {
    echo '"Insurance",';
    echo '"Charges",';
    echo '"Adjustments",';
    echo '"Payments",';
    echo '"Visits",';
    echo '"Patients",';
    echo '"Pt Pct"' . "\n";
  }
}
else {
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Patient Insurance Distribution','e'); ?></title>
<script type="text/javascript" src="../../library/overlib_mini.js"></script>
<script type="text/javascript" src="../../library/calendar.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/js/jquery-1.9.1.min.js"></script>
<script language="JavaScript">

 $(document).ready(function() {
  var win = top.printLogSetup ? top : opener.top;
  win.printLogSetup(document.getElementById('printbutton'));
 });

</script>

<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
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
</head>

<body class="body_top">

<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<span class='title'><?php xl('Report','e'); ?> - <?php xl('Patient Insurance Distribution','e'); ?></span>

<div id="report_parameters_daterange">
    <?php date("d F Y", strtotime(oeFormatDateForPrintReport($_POST['form_from_date'])))
    . " &nbsp; to &nbsp; ". date("d F Y", strtotime(oeFormatDateForPrintReport($_POST['form_to_date']))); ?>
</div>

<form name='theform' method='post' action='insurance_allocation_report.php' id='theform'>

<div id="report_parameters">
<input type='hidden' name='form_refresh' id='form_refresh' value=''/>
<input type='hidden' name='form_csvexport' id='form_csvexport' value=''/>

<table>
 <tr>
  <td width='410px'>
    <div style='float:left'>

    <table class='text'>
        <tr>
            <td class='label'>
               <?php xl('From','e'); ?>:
            </td>
            <td>
               <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php echo $form_from_date ?>'>
            </td>
            <td class='label'>
               <?php xl('To','e'); ?>:
            </td>
            <td>
               <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php echo $form_to_date ?>'>
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
                        <?php xl('Submit','e'); ?>
                    </span>
                    </a>

                    <?php if ($_POST['form_refresh']) { ?>
                    <a href='#' class='css_button' id='printbutton'>
                        <span>
                            <?php xl('Print','e'); ?>
                        </span>
                    </a>
                    <a href='#' class='css_button' onclick='$("#form_csvexport").attr("value","true"); $("#theform").submit();'>
                    <span>
                        <?php xl('Export to CSV','e'); ?>
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

</form>
</div> <!-- end parameters -->

<div id="report_results">
<table>

 <thead>
  <th align='left'> <?php xl('Primary Insurance','e'); ?> </th>
  <th align='right'> <?php xl('Charges','e'); ?> </th>
  <th align='right'> <?php xl('Adjustments','e'); ?> </th>
  <th align='right'> <?php xl('Payments','e'); ?> </th>
  <th align='right'> <?php xl('Visits','e'); ?> </th>
  <th align='right'> <?php xl('Patients','e'); ?> </th>
  <th align='right'> <?php xl('Pt %','e'); ?> </th>
 </thead>
 <tbody>
<?php
} // end not export
if ($_POST['form_refresh'] || $_POST['form_csvexport']) {

  $from_date = fixDate($_POST['form_from_date']);
  $to_date   = fixDate($_POST['form_to_date'], date('Y-m-d'));

    $query = " Select b.pid, b.encounter, Sum(b.fee) As charges, " .
  " (Select Sum(a.adj_amount) From ar_activity As a " .
  " Where a.pid = b.pid And a.encounter = b.encounter) As adjustments, " .
  " (Select Sum(a.pay_amount) From ar_activity As a " .
  " Where a.pid = b.pid And a.encounter = b.encounter) As payments, " .
  " Max(fe.date) As date " .
  " From form_encounter As fe, billing As b " .
  " Where b.pid = fe.pid And b.encounter = fe.encounter And " .
  " fe.date >= '$from_date' And fe.date <= '$to_date' And " .
  " b.code_type != 'COPAY' And b.activity > 0 And b.fee != 0 " .
  " Group By b.pid, b.encounter " .
  " Order By b.pid, b.encounter";

  $res = sqlStatement($query);
  $insarr = array();
  $prev_pid = 0;
  $patcount = 0;

  while ($row = sqlFetchArray($res)) {
    $patient_id = $row['pid'];
    $encounter_date = $row['date'];
    $irow = sqlQuery("SELECT insurance_companies.name " .
      "FROM insurance_data, insurance_companies WHERE " .
      "insurance_data.pid = $patient_id AND " .
      "insurance_data.type = 'primary' AND " .
      "insurance_data.date <= '$encounter_date' AND " .
      "insurance_companies.id = insurance_data.provider " .
      "ORDER BY insurance_data.date DESC LIMIT 1");
    $plan = $irow['name'] ? $irow['name'] : '-- No Insurance --';
    $insarr[$plan]['visits'] += 1;
    $insarr[$plan]['charges'] += sprintf('%0.2f', $row['charges']);
    $insarr[$plan]['adjustments'] += sprintf('%0.2f', $row['adjustments']);
    $insarr[$plan]['payments'] += sprintf('%0.2f', $row['payments']);
    if ($patient_id != $prev_pid) {
      ++$patcount;
      $insarr[$plan]['patients'] += 1;
      $prev_pid = $patient_id;
    }
  }

  ksort($insarr);

  while (list($key, $val) = each($insarr)) {
    if ($_POST['form_csvexport']) {
        echo '"' . $key                                                . '",';
        echo '"' . oeFormatMoney($val['charges'])                      . '",';
        echo '"' . oeFormatMoney($val['adjustments'])                  . '",';
        echo '"' . oeFormatMoney($val['payments'])                     . '",';
        echo '"' . $val['visits']                                      . '",';
        echo '"' . $val['patients']                                    . '",';
        echo '"' . sprintf("%.1f", $val['patients'] * 100 / $patcount) . '"' . "\n";
    }
    else {
?>
 <tr>
  <td>
   <?php echo $key ?>
  </td>
  <td align='right'>
   <?php echo oeFormatMoney($val['charges']) ?>
  </td>
  <td align='right'>
   <?php echo oeFormatMoney($val['adjustments']) ?>
  </td>
  <td align='right'>
   <?php echo oeFormatMoney($val['payments']) ?>
  </td>
  <td align='right'>
   <?php echo $val['visits'] ?>
  </td>
  <td align='right'>
   <?php echo $val['patients'] ?>
  </td>
  <td align='right'>
   <?php printf("%.1f", $val['patients'] * 100 / $patcount) ?>
  </td>
 </tr>
<?php
    } // end not export
  } // end while
} // end if

if (! $_POST['form_csvexport']) {
?>

</tbody>
</table>
</div> <!-- end of results -->

</body>

<link rel="stylesheet" href="../../library/css/jquery.datetimepicker.css">
<script type="text/javascript" src="../../library/js/jquery.datetimepicker.full.min.js"></script>
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
<?php
} // end not export
?>
