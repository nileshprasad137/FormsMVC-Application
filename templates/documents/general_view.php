<head>
<style type="text/css">@import url(library/dynarch_calendar.css);</style>
<script type="text/javascript" src="library/dialog.js"></script>
<script type="text/javascript" src="library/textformat.js"></script>
<script type="text/javascript" src="library/dynarch_calendar.js"></script>
<?php  include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="library/js/jquery-1.9.1.min.js"></script>
<script language="JavaScript">
 var mypcc = '<?php  echo $GLOBALS['phone_country_code']; ?>';

 // Process click on Delete link.
 function deleteme(docid) {
  dlgopen('interface/patient_file/deleter.php?document=' + docid, '_blank', 500, 450);
  return false;
  }


 // Called by the deleter.php window on a successful delete.
 function imdeleted() {
  top.restoreSession();
  window.location.href='<?php echo $this->refresh_action;?>';
  }


 // Called to show patient notes related to this document in the "other" frame.
 function showpnotes(docid) {
  var othername = (window.name == 'RTop') ? 'RBot' : 'RTop';
  parent.left_nav.forceDual();
  parent.left_nav.loadFrame('pno1', othername, 'patient_file/summary/pnotes.php?docid=' + docid);
  return false;
 }


 function submitNonEmpty( e )   {  
    if ( e.elements['passphrase'].value.length == 0 )    {  
        alert( "{xl t='You must enter a pass phrase to encrypt the document'}" );
          }  
    else {
        e.submit();
           }            
      }   

// For tagging it encounter
function tagUpdate() {
    var f = document.forms['document_tag'];
    if (f.encounter_check.checked)    {   
        if(f.visit_category_id.value==0)   {   
            alert(" {xl t='Please select visit category'}" );
            return false;
        }
      }  else if (f.encounter_id.value == 0 )  {    
        alert(" {xl t='Please select encounter'}");
        return false;   
      }  
    //top.restoreSession();
    document.forms['document_tag'].submit();
  }  

// For new or existing encounter
function set_checkbox()    {   
    var f = document.forms['document_tag'];
    if (f.encounter_check.checked)     {   
        f.encounter_id.disabled = true;
        f.visit_category_id.disabled = false;
       }   else    {  
        f.encounter_id.disabled = false;
        f.visit_category_id.disabled = true;
        f.visit_category_id.value = 0;
       }   
   }  

 // Process click on Import link.
 function import_ccr(docid)  {
  top.restoreSession();
  $.ajax({
    url: "library/ajax/ccr_import_ajax.php",
    type: "POST",
    dataType: "html",
    data:
    {
      ccr_ajax : "yes",
      document_id : docid,
    },
    success: function(data){
      alert(data);
      top.restoreSession();
      document.location.reload();
    },
    error:function(){
      alert("failure");
    }
  });
 }


</script>

</head>

<table valign="top" width="100%">
    <tr>
        <td>
            <a class="css_button" href="{$web_path}" onclick="top.restoreSession()"><span><?php echo xl("Download");?></span></a>
            <a class="css_button" href='' onclick='return showpnotes(<?php $this->file->get_id();?>)'><span><?php echo xl("Show Notes");?></span></a>
            <?php echo $this->delete_string;?>
            
            <?php if(($this->file->get_ccr_type($file->get_id()=="CCR") &&
                    ($this->file->get_mimetype($this->file->get_id()) == "application/xml" )) || 
                    ($this->file->get_mimetype($file->get_id()) ==  "text/xml" && 
                            $this->file->get_imported($file->get_id()) == 0)  ) { ?>
            <a class="css_button" href='javascript:' onclick='return import_ccr(<?php echo $this->file->get_id();?>)'><span>Import<?php echo xl("Import");?></span></a>
            <?php }?>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <?php if( !$this->hide_encryption) {?>
            <div class="text">
                <form method="post" name="document_encrypt" action="<?php echo $this->web_path;?>" onsubmit="return top.restoreSession()">
                <div>
                    <div style="float:left">
                        <b><?php echo xl("Encryption");?></b>&nbsp; 
                    </div>
                    <div style="float:none">
                        <a href="javascript:;" onclick="submitNonEmpty( document.forms['document_encrypt'] );">
                            (<span><?php echo xl("download encrypted file");?></span></a>
                    </div> 
                </div>
                <div>
                   <?php echo xl("Pass Phrase:");?>
                    <input title="Supports TripleDES encryption/decryption only. Leaving the pass phrase blank will not encrypt the document" 
                           type='text' size='20' name='passphrase' id='passphrase' value=''/>
                    <input type="hidden" name="encrypted" value="true"></input>
                </div>
                </form>
            </div>
            <br/>
            <?php } ?>
            <div class="text">
                <form method="post" name="document_validate" action="<?php echo $this->validate_action;?>" onsubmit="return top.restoreSession()">
                <div>
                    <div style="float:left">
                        <b><?php echo xl("Sha-1 Hash:");?></b>&nbsp;
                        <i><?php echo $this->file->get_hash();?></i>&nbsp;
                    </div>
                    <div style="float:none">
                        <a href="javascript:;" onclick="document.forms['document_validate'].submit();">(<span><?php echo xl("validate");?>)</span></a>
                    </div>
                </div>
                </form>
            </div>
            <br/>
            <div class="text">
                <form method="post" name="document_update" action="<?php echo $this->update_action;?>" onsubmit="return top.restoreSession()">
                <div>
                    <div style="float:left">
                        <b><?php echo xl("Update");?></b>&nbsp;
                    </div>
                    <div style="float:none">
                        <a href="javascript:;" onclick="document.forms['document_update'].submit();">(<span><?php echo xl("submit");?>)</span></a>
                    </div>
                </div>
                <div>
                    <?php echo xl("Rename:");?>
                    <input type='text' size='20' name='docname' id='docname' value='<?php echo $this->file->get_url_web();?>'/>
                </div>
                <div>
                   <?php echo xl("Date:");?>
                    <input type='text' size='10' name='docdate' id='docdate'
                     value='<?php echo $this->docdate;?>' title='yyyy-mm-dd document date'
                     onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
                    <img src='interface/pic/show_calendar.gif' id='img_docdate' align='absbottom'
                     width='24' height='22' border='0' alt='[?]' style='cursor:pointer'
                     title='Click here to choose a date' />
                    <select name="issue_id"><?php echo $this->issues_list;?></select>
                </div>
                </form>
            </div>

            <br/>

            <div class="text">
                <form method="post" name="document_move" action="<?php echo $this->move_action;?>" onsubmit="return top.restoreSession()">
                <div>
                    <div style="float:left">
                        <b><?php echo xl("Move");?></b>&nbsp;
                    </div>
                    <div style="float:none">
                        <a href="javascript:;" onclick="document.forms['document_move'].submit();">(<span><?php echo xl("submit");?>)</span></a>
                    </div>
                </div>

                <div>
                        <select name="new_category_id"><?php echo $this->tree_html_listbox;?></select>&nbsp;
                        Move to Patient # <input type="text" name="new_patient_id" size="4" />
                        <a href="javascript:"
                         onclick="top.restoreSession();
                             var URL='controller.php?patient_finder&find&form_id={"document_move['new_patient_id']"|escape:"url"}&form_name={"document_move['new_patient_name']"|escape:"url"}'; window.open(URL, 'document_move', 'toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=450,height=400,left=425,top=250');">
                        <img src="images/stock_search-16.png" border="0" /></a>
                        <input type="hidden" name="new_patient_name" value="" />
                </div>
                </form>
            </div>

            <br/>
            
            <div class="text">
               <form method="post" name="document_tag" id="document_tag" action="<?php echo $this->tag_action;?>" 
                     onsubmit="return top.restoreSession()">

                <div >
                   <div style="float:left">
                       <b><?php echo xl("Tag to Encounter");?></b>&nbsp;
                   </div>

                   <div style="float:none">
                       <a href="javascript:;" onclick="tagUpdate();">(<span><?php echo xl("submit");?>)</span></a>
                   </div>
               </div>

                 <div>
                    <select id="encounter_id"  name="encounter_id"  ><?php echo $enc_list;?></select>&nbsp;
                    <input type="checkbox" name="encounter_check" id="encounter_check"  onclick='set_checkbox(this)'/> 
                    <label for="encounter_check"><b><?php echo xl("Create Encounter");?></b></label>&nbsp;&nbsp;
                      <?php echo xl("Visit Category");?>: &nbsp;<select id="visit_category_id"  name="visit_category_id"  disabled>
                          <?php echo $this->visit_category_list;?></select>&nbsp; 

               </div>
               </form>
           </div>

            <br/>

            <form name="notes" method="post" action="<?php echo $this->note_action;?>" onsubmit="return top.restoreSession()">
            <div class="text">
                <div>
                    <div style="float:left">
                        <b><?php echo xl("Notes");?></b>&nbsp;
                    </div>
                    <div style="float:none">
                        <a href="javascript:;" onclick="document.notes.identifier.value='no';document.forms['notes'].submit();">
                            (<span><?php echo xl("add");?></span>)</a>
                        &nbsp;&nbsp;&nbsp;<b><?php echo xl("Email");?></b>&nbsp;
                        <input type="text" size="25" name="provide_email" id="provide_email" />
                        <input type="hidden" name="identifier" id="identifier" />
                        <a href="javascript:;" onclick="javascript:document.notes.identifier.value='yes';document.forms['notes'].submit();">
                            (<span><?php echo xl("Send");?></span>)
                        </a>
                    </div>
                    <div>
                        
                    </div>
                    <div style="float:none">
                        
                    </div>
                <div>
                    <textarea cols="53" rows="8" wrap="virtual" name="note" style="width:100%"></textarea><br>
                    <input type="hidden" name="process" value="<?php echo self::PROCESS;?>" />
                    <input type="hidden" name="foreign_id" value="<?php echo $this->file->get_id();?>" />

                    <?php if($this->notes) { ?>
                        <div style="margin-top:7px">
                            <?php foreach ($this->notes as $note) { ?>
                            <div>?
                                Note # <?php echo $note->get_id();?>
                                Date: <?php echo $note->get_date();?>
                                <?php echo $note->get_note();?>
                                <!--
                                {if $note->get_owner()}
                                    &nbsp;-{user_info id=$note->get_owner()}
                                {/if}
                                -->
                                <?php if($note->get_owner()) { ?>
                                     &nbsp;-{user_info id=$note->get_owner()}
                                <?php }?>
                            </div>
                            <?php } 
                    }?>

                 
                    
                    </div>
                </div>
            </div>
            </form>

        </td>
    </tr>
    <tr>
        <td>
            <div class="text"><b><?php echo xl("Content");?></b></div>
            <?php if($this->file->get_mimetype() =="image/tiff" ) { ?>
                <embed frameborder="0" type="<?php echo $this->file->get_mimetype();?>" src="<?php echo $this->web_path;?>as_file=false"></embed>
            <?php } else if($this->file->get_mimetype()=="image/png" || $this->file->get_mimetype() == "image/jpg"
                   ||  $file->get_mimetype() == "image/gif" || $file->get_mimetype() == "application/pdf"  ) { ?>
                    <iframe frameborder="0" type="<?php echo $this->file->get_mimetype();?>" src="<?php echo $this->web_path;?>as_file=false"></iframe>
                   <?php } else if($this->file->get_ccr_type($this->file->get_id()) != "CCR" &&
                                 $this->file->get_ccr_type($this->file->get_id()) != "CCD") {?>
                    <iframe frameborder="0" type="<?php echo $this->file->get_mimetype();?>" src="<?php echo $this->web_path;?>as_file=true"></iframe>
                                 <?php }?>
               
        </td>
    </tr>
</table>
<script language='JavaScript'>
 Calendar.setup (   {   inputField:"docdate", ifFormat:"%Y-%m-%d", button:"img_docdate"  }  );
</script>
