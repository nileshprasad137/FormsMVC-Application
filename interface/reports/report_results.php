<?php
/*
 * Report Results
 *
 * Generic script to list stored reports. Part of the module to allow the tracking,
 * storing, and viewing of reports.
 *
 * Copyright (C) 2016-2017 Terry Hill <teryhill@librehealth.io> 
 * Copyright (C) 2012 Brady Miller <brady@sparmy.com>
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
 * @link    http://librehealth.io
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../globals.php");
require_once("../../library/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
require_once "$srcdir/formdata.inc.php";
require_once "$srcdir/clinical_rules.php";
require_once "$srcdir/report_database.inc";
/** Current format of date  */
$DateFormat = DateFormatRead();
$DateLocale = getLocaleCodeForDisplayLanguage($GLOBALS['language_default']);
?>

<html>

<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../library/css/jquery.datetimepicker.css">

<title><?php echo htmlspecialchars( xl('Report Results/History'), ENT_NOQUOTES); ?></title>

<script type="text/javascript" src="../../library/overlib_mini.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery-1.9.1.min.js"></script>
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

<span class='title'><?php echo htmlspecialchars( xl('Report History/Results'), ENT_NOQUOTES); ?></span>

<form method='post' name='theform' id='theform' action='report_results.php' onsubmit='return top.restoreSession()'>

<div id="report_parameters">

<table>
 <tr>
  <td width='470px'>
    <div style='float:left'>

    <table class='text'>

                   <tr>
                      <td class='label'>
                         <?php echo htmlspecialchars( xl('Begin Date'), ENT_NOQUOTES); ?>:
                      </td>
                      <td>
                         <input type='text' name='form_begin_date' id='form_begin_date' size='20'
                                value='<?php echo htmlspecialchars( $_POST['form_begin_date'], ENT_QUOTES); ?>'
                                title='<?php echo htmlspecialchars( xl('yyyy-mm-dd hh:mm:ss'), ENT_QUOTES); ?>'>
                      </td>
                   </tr>

                <tr>
                        <td class='label'>
                              <?php echo htmlspecialchars( xl('End Date'), ENT_NOQUOTES); ?>:
                        </td>
                        <td>
                           <input type='text' name='form_end_date' id='form_end_date' size='20'
                                  value='<?php echo htmlspecialchars( $_POST['form_end_date'], ENT_QUOTES); ?>'
                                  title='<?php echo htmlspecialchars( xl('yyyy-mm-dd hh:mm:ss'), ENT_QUOTES); ?>'>
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
                    <a id='search_button' href='#' class='css_button' onclick='top.restoreSession(); $("#theform").submit()'>
                    <span>
                        <?php echo htmlspecialchars( xl('Search'), ENT_NOQUOTES); ?>
                    </span>
                    </a>
                                        <a id='refresh_button' href='#' class='css_button' onclick='top.restoreSession(); $("#theform").submit()'>
                                        <span>
                                                <?php echo htmlspecialchars( xl('Refresh'), ENT_NOQUOTES); ?>
                                        </span>
                                        </a>
                </div>
            </td>
        </tr>
    </table>
  </td>
 </tr>
</table>

</div>  <!-- end of search parameters -->

<br>



<div id="report_results">
<table>

 <thead>
  <th align='center'>
   <?php echo htmlspecialchars( xl('Title'), ENT_NOQUOTES); ?>
  </th>

  <th align='center'>
   <?php echo htmlspecialchars( xl('Date'), ENT_NOQUOTES); ?>
  </th>

  <th align='center'>
   <?php echo htmlspecialchars( xl('Status'), ENT_NOQUOTES); ?>
  </th>

 </thead>
 <tbody>  <!-- added for better print-ability -->
<?php

 $res = listingReportDatabase(prepareDateBeforeSave($_POST['form_begin_date']), prepareDateBeforeSave($_POST['form_end_date']));
 while ($row = sqlFetchArray($res)) {

  // Figure out the title and link
  if ($row['type'] == "cqm") {
    if (!$GLOBALS['enable_cqm']) continue;
    $type_title = xl('Clinical Quality Measures (CQM)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "cqm_2011") {
    if (!$GLOBALS['enable_cqm']) continue;
    $type_title = xl('2011 Clinical Quality Measures (CQM)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "cqm_2014") {
    if (!$GLOBALS['enable_cqm']) continue;
    $type_title = xl('2014 Clinical Quality Measures (CQM)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "amc") {
    if (!$GLOBALS['enable_amc']) continue;
    $type_title = xl('Automated Measure Calculations (AMC)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "amc_2011") {
    if (!$GLOBALS['enable_amc']) continue;
    $type_title = xl('2011 Automated Measure Calculations (AMC)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "amc_2014") {
    if (!$GLOBALS['enable_amc']) continue;
    $type_title = xl('2014 Automated Measure Calculations (AMC)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "amc_2014_stage1") {
    if (!$GLOBALS['enable_amc']) continue;
    $type_title = xl('2014 Automated Measure Calculations (AMC) - Stage I');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "amc_2014_stage2") {
    if (!$GLOBALS['enable_amc']) continue;
    $type_title = xl('2014 Automated Measure Calculations (AMC) - Stage II');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "process_reminders") {
    if (!$GLOBALS['enable_cdr']) continue;
    $type_title = xl('Processing Patient Reminders');
    $link="../batchcom/batch_reminders.php?report_id=" . attr($row["report_id"]);
  }
  else if ($row['type'] == "process_send_reminders") {
    if (!$GLOBALS['enable_cdr']) continue;
    $type_title = xl('Processing and Sending Patient Reminders');
    $link="../batchcom/batch_reminders.php?report_id=" . attr($row["report_id"]);
  }
  else if ($row['type'] == "passive_alert") {
    if (!$GLOBALS['enable_cdr']) continue;
    $type_title = xl('Standard Measures (Passive Alerts)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "active_alert") {
    if (!$GLOBALS['enable_cdr']) continue;
    $type_title = xl('Standard Measures (Active Alerts)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else if ($row['type'] == "patient_reminder") {
    if (!$GLOBALS['enable_cdr']) continue;
    $type_title = xl('Standard Measures (Patient Reminders)');
    $link="cqm.php?report_id=" . attr($row["report_id"]) . "&back=list";
  }
  else {
    // Not identified, so give an unknown title
    $type_title = xl('Unknown') . "-" . $row['type'];
    $link="";
  }
?>
 <tr>
    <?php if ($row["progress"] == "complete") { ?>
      <td align='center'><a href='<?php echo $link; ?>' onclick='top.restoreSession()'><?php echo text($type_title); ?></a></td>
    <?php } else { ?>
      <td align='center'><?php echo text($type_title); ?></td>
    <?php } ?>
    <td align='center'><?= date(DateFormatRead(true), strtotime($row["date_report"])); ?></td>
    <?php if ($row["progress"] == "complete") { ?>
      <td align='center'><?php echo xlt("Complete") . " (" . xlt("Processing Time") . ": " . text($row['report_time_processing']) . " " . xlt("Minutes") . ")"; ?></td>
    <?php } else { ?>
      <td align='center'><?php echo xlt("Pending") . " (" . text($row["progress_items"]) . " / " . text($row["total_items"]) . " " . xlt("Patients Processed") . ")"; ?></td>
    <?php } ?>

 </tr>

<?php
 } // $row = sqlFetchArray($res) while
?>
</tbody>
</table>
</div>  <!-- end of search results -->

</form>

</body>

<script type="text/javascript" src="../../library/js/jquery.datetimepicker.full.min.js"></script>
<script>
    $(function() {
        $("#form_begin_date").datetimepicker({
            timepicker: false,
            format: "<?= $DateFormat; ?>"
        });
        $("#form_end_date").datetimepicker({
            timepicker: false,
            format: "<?= $DateFormat; ?>"
        });
        $.datetimepicker.setLocale('<?= $DateLocale;?>');
    });
</script>

</html>

