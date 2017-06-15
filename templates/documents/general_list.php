<html>
<head>
<?php html_header_show(); ?>

<link rel="stylesheet" href="<?php echo $GLOBALS['css_header']; ?>" type="text/css">

<script type="text/javascript" src="library/js/DocumentTreeMenu.js"></script>
</head>
<!--<body bgcolor="{$STYLE.BGCOLOR2}">-->

<!-- ViSolve - Call expandAll function on loading of the page if global value 'expand_document' is set -->
<?php  if ($GLOBALS['expand_document_tree']) {  ?>
  <body class="body_top" onload="javascript:objTreeMenu_1.expandAll();return false;">
<?php } else { ?>
  <body class="body_top">
<?php } ?>

<div class="title">Documents</div>
<div id="documents_list">
<table>
	<tr>
		<td height="20" valign="top">Categories &nbsp;
            (<a href="#" onclick="javascript:objTreeMenu_1.collapseAll();return false;">Collapse all</a>)
		</td>
	</tr>
	<tr>
		<td valign="top"><?php echo $this->tree_html; ?></td>
	</tr>
</table>
</div>
<div id="documents_actions">
		<!--{if $message}
			<div class='text' style="margin-bottom:-10px; margin-top:-8px"><i>{$message}</i></div><br>
		{/if}-->
                                        <?php
                                            if($this->message){ ?>
                                                <div class='text' style="margin-bottom:-10px; margin-top:-8px"><i><?php echo $this->message;    ?></i></div><br>
                                            <?php } ?>
                                                
                                            <?php if($this->messages) {?>    
                                                <div class='text' style="margin-bottom:-10px; margin-top:-8px"><i><?php echo $this->messages;   ?></i></div><br>
                                            <?php } 
                                            echo $this->activity;?>
                                                
                                       <!--{if $messages}
                                                <div class='text' style="margin-bottom:-10px; margin-top:-8px"><i>{$messages}</i></div><br>
		{/if}
		{$activity}
                                        -->
</div>
</body>
</html>
