<?php /* Smarty version 2.6.29, created on 2017-03-20 04:59:15
         compiled from C:/wamp64/www/libre_work/LibreEHR/templates/insurance_numbers/general_list.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'xl', 'C:/wamp64/www/libre_work/LibreEHR/templates/insurance_numbers/general_list.html', 3, false),)), $this); ?>
<table cellpadding="1" cellspacing="0" class="showborder">
        <tr class="showborder_head">
                <th width="130px"><?php echo smarty_function_xl(array('t' => 'Name'), $this);?>
</th>
                <th width="80px">&nbsp;</th>
                <th width="100px"><?php echo smarty_function_xl(array('t' => 'Provider'), $this);?>
 #</th>
                <th width="100px"><?php echo smarty_function_xl(array('t' => 'Rendering'), $this);?>
 #</th>
                <th width="100px"><?php echo smarty_function_xl(array('t' => 'Group'), $this);?>
 #</th>
        </tr>
        <?php $_from = $this->_tpl_vars['providers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['provider']):
?>
        <tr height="22">
                <td><a href="<?php echo $this->_tpl_vars['CURRENT_ACTION']; ?>
action=edit&id=default&provider_id=<?php echo $this->_tpl_vars['provider']->id; ?>
"><?php echo $this->_tpl_vars['provider']->get_name_display(); ?>
</a></td>
                <td><?php echo smarty_function_xl(array('t' => 'Default'), $this);?>
&nbsp;</td>
                <td><?php echo $this->_tpl_vars['provider']->get_provider_number_default(); ?>
&nbsp;</td>
                <td><?php echo $this->_tpl_vars['provider']->get_rendering_provider_number_default(); ?>
&nbsp;</td>
                <td><?php echo $this->_tpl_vars['provider']->get_group_number_default(); ?>
&nbsp;</td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="center_display">
                <td colspan="5"><?php echo smarty_function_xl(array('t' => 'No Providers Found'), $this);?>
</td>
        </tr>
        <?php endif; unset($_from); ?>
</table>