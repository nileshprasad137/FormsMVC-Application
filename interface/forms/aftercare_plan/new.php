<?php
/**
 *
 * Copyright (C) 2012-2013 Naina Mohamed <naina@capminds.com> CapMinds Technologies
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
 * @author  Naina Mohamed <naina@capminds.com>
 * @link    http://librehealth.io
 */
 
 
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

include_once("../../globals.php");
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
require_once($GLOBALS['srcdir']."/formatting.inc.php");
$DateFormat = DateFormatRead();
$DateLocale = getLocaleCodeForDisplayLanguage($GLOBALS['language_default']);
formHeader("Form:AfterCare Planning");
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
$obj = $formid ? formFetch("form_aftercare_plan", $formid) : array();

?>
<html>
<head>
<?php html_header_show();?>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.9.1.min.js"></script>
<script language="JavaScript">
 $(document).ready(function() {
  var win = top.printLogSetup ? top : opener.top;
  win.printLogSetup(document.getElementById('printbutton'));
 });
</script>

</head>
<body class="body_top">
<p><span class="forms-title"><?php echo xlt('AfterCare Planning'); ?></span></p>
</br>
<?php
echo "<form method='post' name='my_form' " .
  "action='$rootdir/forms/aftercare_plan/save.php?id=" . attr($formid) ."'>\n";
?>
<table  border="0">
<tr>
<td align="left" class="forms" class="forms"><?php echo xlt('Client Name' ); ?>:</td>
        <td class="forms">
            <label class="forms-data"> <?php if (is_numeric($pid)) {
    
    $result = getPatientData($pid, "fname,lname,squad");
   echo htmlspecialchars(text($result['fname'])." ".text($result['lname']));}
   $patient_name=($result['fname'])." ".($result['lname']);
   ?>
   </label>
   <input type="hidden" name="client_name" value="<?php echo attr($patient_name);?>">
        </td>
        <td align="left"  class="forms"><?php echo xlt('DOB'); ?>:</td>
        <td class="forms">
        <label class="forms-data"> <?php if (is_numeric($pid)) {
    
    $result = getPatientData($pid, "*");
   echo htmlspecialchars($result['DOB']);}
   $dob=($result['DOB']);
   ?>
   </label>
     <input type="hidden" name="DOB" value="<?php echo attr($dob);?>">
        </td>
        </tr>
<tr>
    
        
  <td align="left" class="forms"><?php echo xlt('Admit Date'); ?>:</td>
        <td class="forms">
               <input type='text' size='10' name='admit_date' id='admission_date' <?php echo attr($disabled); ?>;
               value='<?php echo htmlspecialchars(oeFormatShortDate(attr($obj{"admit_date"}))); ?>'
               title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'/>
        </td>

    
    
        <td align="left" class="forms"><?php echo xlt('Discharged'); ?>:</td>
        <td class="forms">
               <input type='text' size='10' name='discharged' id='discharge_date' <?php echo attr($disabled); ?>;
             value='<?php echo htmlspecialchars(oeFormatShortDate(attr($obj{"discharged"}))); ?>'
            title='<?php echo xla('yyyy-mm-dd Date of service'); ?>' />
        </td>
    </tr>
    <tr>
        <td align="left colspan="3" style="padding-bottom:7px;"></td>
    </tr>
        <tr>
        
        <td class="forms-subtitle" colspan="4"><B><?php echo xlt('Goal and Methods');?></B></td>
        
    </tr>
    <tr>
        <td align="left colspan="3" style="padding-bottom:7px;"></td>
    </tr>
    <tr>
        
        <td class="forms-subtitle" colspan="4"><B><?php echo xlt('Goal A');?>:</B>&nbsp;<?php echo xlt('Acute Intoxication/Withdrawal'); ?></td>
        
    </tr>
    <tr>
        <td align="right" class="forms">1.</td>
        <td colspan="3"><textarea name="goal_a_acute_intoxication" rows="2" cols="80" wrap="virtual name"><?php echo text($obj{"goal_a_acute_intoxication"});?></textarea></td>
        
    </tr>
    <tr>
        <td align="right" class="forms">2.</td>
        <td colspan="3"><textarea name="goal_a_acute_intoxication_I" rows="2" cols="80" wrap="virtual name"><?php echo text($obj{"goal_a_acute_intoxication_I"});?></textarea></td>
        
    </tr>
    <tr>
        <td align="right" class="forms">3.</td>
        <td colspan="3"><textarea name="goal_a_acute_intoxication_II" rows="2" cols="80" wrap="virtual name"><?php echo text($obj{"goal_a_acute_intoxication_II"});?></textarea></td>
        
    
    <tr>
        
        <td class="forms-subtitle" colspan="4"><B><?php echo xlt('Goal B');?>:</B>&nbsp;<?php  echo xlt('Emotional / Behavioral Conditions & Complications'); ?></td>
        
    </tr>
    <tr>
        <td align="right" class="forms">1.</td>
        <td colspan="3"><textarea name="goal_b_emotional_behavioral_conditions" rows="2" cols="80" wrap="virtual name"><?php echo text($obj{"goal_b_emotional_behavioral_conditions"});?></textarea></td>
        
    </tr>
    <tr>
        <td align="right" class="forms">2.</td>
        <td colspan="3"><textarea name="goal_b_emotional_behavioral_conditions_I" rows="2" cols="80" wrap="virtual name"><?php echo text($obj{"goal_b_emotional_behavioral_conditions_I"});?></textarea></td>
        
    </tr>
    
        
        <td class="forms-subtitle" colspan="4"><B><?php echo xlt('Goal C');?>:</B>&nbsp;<?php  echo xlt('Relapse Potential'); ?></td>
        
    </tr>
    <tr>
        <td align="right" class="forms">1.</td>
        <td colspan="3"><textarea name="goal_c_relapse_potential" rows="2" cols="80" wrap="virtual name"><?php echo text($obj{"goal_c_relapse_potential"});?></textarea></td>
        
    </tr>
    <tr>
        <td align="right" class="forms">2.</td>
        <td colspan="3"><textarea name="goal_c_relapse_potential_I" rows="2" cols="80" wrap="virtual name"><?php echo text($obj{"goal_c_relapse_potential_I"});?></textarea></td>
        
    </tr>

    <tr>
        <td align="left colspan="3" style="padding-bottom:7px;"></td>
    </tr>
    <tr>
        <td></td>
    <td><input type='submit' value='<?php echo xla('Save'); ?>' class='button-css' />&nbsp;
    <input type='button' value='<?php echo xla('Print'); ?>' id='printbutton' class='button-css' />&nbsp;
    <input type='button' class='button-css' value='<?php echo xla('Cancel'); ?>'
 onclick="top.restoreSession();location='<?php echo "$rootdir/patient_file/encounter/$returnurl" ?>'" /></td>
    </tr>
</table>
</form>
<link rel="stylesheet" href="../../../library/css/jquery.datetimepicker.css">
<script type="text/javascript" src="../../../library/js/jquery.datetimepicker.full.min.js"></script>
<script>
    $(function() {
        $("#admission_date, #discharge_date").datetimepicker({
            timepicker: false,
            format: "<?= $DateFormat; ?>"
        });
        $.datetimepicker.setLocale('<?= $DateLocale;?>');
    });
</script>
<?php
formFooter();
?>
