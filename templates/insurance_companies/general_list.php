<a href="controller.php?practice_settings&<?php echo $top_action;?>insurance_company&action=edit" 
   onclick="top.restoreSession()" class="css_button" >
<span>Add a Company</span></a><br>
<br>
<table cellpadding="1" cellspacing="0" class="showborder">
    <tr class="showborder_head">
        <th width="140px"><b>Name</b></th>
        <th width="300px"><b>City, State</b></th>
        <th><b>Default X12 Partner</b></th>
    </tr>
    
    
    
   <?php if(is_array($this->icompanies)) {?>
    <?php foreach ($this->icompanies as $insurancecompany) { ?>
    <tr height="22">
        <td><a href="<?php echo $this->current_action;?>action=edit&id=<?php echo $insurancecompany->id;?>" onsubmit="return top.restoreSession()">
                <?php echo $insurancecompany->name;?>&nbsp;</a>
        </td>
        <td>
            <td><?php echo $insurancecompany->address->city ;?>
                <?php echo $insurancecompany->address->state;?>
            </td>
        </td>
        <td>
            <td><?php echo $insurancecompany->get_x12_default_partner_name();?>&nbsp;</td>
        </td>
    </tr>
   <?php }   
    }
    else {
    ?>
    <tr class="center_display">
        <td colspan="3">No Insurance Companies Found</td>
    </tr>
</table>

    <?php } ?>