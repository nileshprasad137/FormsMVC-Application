<?php
/*
 * Front Receipts report
 * This report lists front office receipts for a given date range.
 *
 * Copyright (C) 2016-2017 Terry Hill <teryhill@librehealth.io> 
 * Copyright (C) 2006-2015 Rod Roark <rod@sunsetsystems.com>
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
 * @author Rod Roark <rod@sunsetsystems.com>
 * @link http://librehealth.io 
 */

require_once("../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");

$DateFormat = DateFormatRead();
$DateLocale = getLocaleCodeForDisplayLanguage($GLOBALS['language_default']);
$from_date = fixDate($_POST['form_from_date'], date(DateFormatRead(true)));
$to_date = fixDate($_POST['form_to_date'], date(DateFormatRead(true)));
function bucks($amt)
{
  return ($amt != 0.00) ? oeFormatMoney($amt) : '';
 }
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Front Office Receipts','e'); ?></title>
<script type="text/javascript" src="../../library/overlib_mini.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery-1.9.1.min.js"></script>

<script language="JavaScript">

<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>
 $(document).ready(function() {
  var win = top.printLogSetup ? top : opener.top;
  win.printLogSetup(document.getElementById('printbutton'));
 });

 // The OnClick handler for receipt display.
 function show_receipt(pid,timestamp) {
  dlgopen('../patient_file/front_payment.php?receipt=1&patient=' + pid +
   '&time=' + timestamp, '_blank', 550, 400);
 }

</script>

<link rel='stylesheet' href='<?php echo $css_header ?>' type='text/css'>
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
    #report_results {
       margin-top: 30px;
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

<span class='title'><?php xl('Report','e'); ?> - <?php xl('Front Office Receipts','e'); ?></span>

<div id="report_parameters_daterange">
    <?php date("d F Y", strtotime(oeFormatDateForPrintReport($_POST['form_from_date'])))
    . " &nbsp; to &nbsp; ". date("d F Y", strtotime(oeFormatDateForPrintReport($_POST['form_to_date']))); ?>
</div>

<form name='theform' method='post' action='front_receipts_report.php' id='theform'>

<div id="report_parameters">

<input type='hidden' name='form_refresh' id='form_refresh' value=''/>

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
               <input type='text' name='form_from_date' id="form_from_date" size='10'
                      value='<?php echo htmlspecialchars(attr($form_from_date)) ?>'/>
            </td>
            <td class='label'>
               <?php xl('To','e'); ?>:
            </td>
            <td>
               <input type='text' name='form_to_date' id="form_to_date" size='10'
                      value='<?php echo htmlspecialchars(attr($form_to_date)) ?>'/>
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
                                    <a href='#' class='css_button'
                                       onclick='$("#form_refresh").attr("value","true"); $("#theform").submit();'>
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
                    <?php } ?>
                </div>
            </td>
        </tr>
    </table>
  </td>
 </tr>
</table>
</div> <!-- end of parameters -->

<?php
 if ($_POST['form_refresh'] || $_POST['form_orderby']) {
?>
<div id="report_results">
<table>
 <thead>
  <th> <?php xl('Time','e'); ?> </th>
  <th> <?php xl('Patient','e'); ?> </th>
  <th> <?php xl('ID','e'); ?> </th>
  <th> <?php xl('Method','e'); ?> </th>
  <th> <?php xl('Source','e'); ?> </th>
  <th align='right'> <?php xl('Today','e'); ?> </th>
  <th align='right'> <?php xl('Previous','e'); ?> </th>
  <th align='right'> <?php xl('Total','e'); ?> </th>
 </thead>
 <tbody>
<?php
 if (true || $_POST['form_refresh']) {
  $total1 = 0.00;
  $total2 = 0.00;

  $query = "SELECT r.pid, r.dtime, " .
    "SUM(r.amount1) AS amount1, " .
    "SUM(r.amount2) AS amount2, " .
    "MAX(r.method) AS method, " .
    "MAX(r.source) AS source, " .
    "MAX(r.user) AS user, " .
    "p.fname, p.mname, p.lname, p.pid " .
    "FROM payments AS r " .
    "LEFT OUTER JOIN patient_data AS p ON " .
    "p.pid = r.pid " .
    "WHERE " .
    "r.dtime >= '$from_date 00:00:00' AND " .
    "r.dtime <= '$to_date 23:59:59' " .
    "GROUP BY r.dtime, r.pid ORDER BY r.dtime, r.pid";

  // echo "<!-- $query -->\n"; // debugging
  $res = sqlStatement($query);

  while ($row = sqlFetchArray($res)) {
    // Make the timestamp URL-friendly.
    $timestamp = preg_replace('/[^0-9]/', '', $row['dtime']);
?>
 <tr>
  <td nowrap>
   <a href="javascript:show_receipt(<?php echo $row['pid'] . ",'$timestamp'"; ?>)">
   <?php echo oeFormatShortDate(substr($row['dtime'], 0, 10)) . substr($row['dtime'], 10, 6); ?>
   </a>
  </td>
  <td>
   <?php echo $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'] ?>
  </td>
  <td>
   <?php echo $row['pid'] ?>
  </td>
  <td>
   <?php echo $row['method'] ?>
  </td>
  <td>
   <?php echo $row['source'] ?>
  </td>
  <td align='right'>
   <?php echo bucks($row['amount1']) ?>
  </td>
  <td align='right'>
   <?php echo bucks($row['amount2']) ?>
  </td>
  <td align='right'>
   <?php echo bucks($row['amount1'] + $row['amount2']) ?>
  </td>
 </tr>
<?php
    $total1 += $row['amount1'];
    $total2 += $row['amount2'];
  }
?>

 <tr>
  <td colspan='8'>
   &nbsp;
  </td>
 </tr>

 <tr class="report_totals">
  <td colspan='5'>
   <?php xl('Totals','e'); ?>
  </td>
  <td align='right'>
   <?php echo bucks($total1) ?>
  </td>
  <td align='right'>
   <?php echo bucks($total2) ?>
  </td>
  <td align='right'>
   <?php echo bucks($total1 + $total2) ?>
  </td>
 </tr>

<?php
 }
?>
</tbody>
</table>
</div> <!-- end of results -->
<?php } else { ?>
<div class='text'>
    <?php echo xl('Please input search criteria above, and click Submit to view results.', 'e' ); ?>
</div>
<?php } ?>

</form>
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
