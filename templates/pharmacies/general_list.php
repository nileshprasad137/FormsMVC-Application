<a href="controller.php?practice_settings&<?php echo $this->top_action;?> pharmacy&action=edit" onclick="top.restoreSession()" class="css_button" >
<span>Add a Pharmacy</span></a><br><br>

<table cellpadding="1" cellspacing='0' class="showborder">
    <tr class="showborder_head">
        <th width="170px"><b>Name</b></th>
        <th width="320px"><b>Address</b></th>
        <th><b>Default Method</b></th>
    </tr>
    
    <?php if(is_array($this->pharmacies)) { ?>
    
    <?php foreach ($this->pharmacies as $pharmacy) { ?>
        <tr height="22">
            <td><a href="<?php echo $this->current_action;?>action=edit&id=<?php echo $pharmacy->id;?>" onclick="top.restoreSession()">
                    <?php echo $pharmacy->name;?>&nbsp;</a>
            </td>
            <td>
               <?php 
               if($pharmacy->address->line1 != '') { echo $pharmacy->address->line1.',';}
               if($pharmacy->address->city != '') { echo $pharmacy->address->city; }
               echo $pharmacy->address->state."&nbsp;".$pharmacy->address->zip;            
                ?> 
            </td> 
            <td><?php echo $pharmacy->get_transmit_method_display();?>&nbsp;</td>
        </tr>
    <?php } }
    else { ?>
        <tr class="center_display">
            <td colspan="3"><b>No Pharmacies Found<b></td>
        </tr>
    <?php } ?>
    </table>

     
    
    <!--
    {foreach from=$pharmacies item=pharmacy}
    <tr height="22">
        <td><a href="<?// echo $this->current_action;?>action=edit&id=<?php// echo $pharmacy->id;?>" onclick="top.restoreSession()"><php echo $pharmacy->name;?>&nbsp;</a></td>
        <td>
        {if $pharmacy->address->line1 != ''}{$pharmacy->address->line1}, {/if}
        {if $pharmacy->address->city != ''}{$pharmacy->address->city}, {/if}
            {$pharmacy->address->state|upper} {$pharmacy->address->zip}&nbsp;</td>
        <td>{$pharmacy->get_transmit_method_display()}&nbsp;
    {foreachelse}</td>
    </tr>

    <tr class="center_display">
        <td colspan="3"><b>{xl t='No Pharmacies Found'}<b></td>
    </tr>
    {/foreach}
</table>
    -->