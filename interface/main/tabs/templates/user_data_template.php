
<script type="text/html" id="user-data-template">
    <!-- ko with: user -->
        <div id="username" title="<?php echo xla('Authorization group') ?>">
            <span data-bind="text:fname"></span>
            <span data-bind="text:lname"></span>
            <div class="userfunctions">
			    <?php if ($GLOBALS['development_flag'] ) { ?>
                <div data-bind="click: framesMode"><?php echo xlt("Frames Mode");?></div>
				<?php } ?>
                <div data-bind="click: userPrefs"><?php echo xlt("User Preferences");?></div>
                <div data-bind="click: changePassword"><?php echo xlt("Change Pass Phrase");?></div>
                <div data-bind="click: logout"><?php echo xlt("Logout");?></div>
                
            </div>
        </div>
    <!-- /ko -->
</script>