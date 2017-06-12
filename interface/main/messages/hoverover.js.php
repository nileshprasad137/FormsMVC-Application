<?php
/**
 * javascript code responsible for floating popup message notice generation.
 *
 * Copyright (C) 2015-2016 Tony McCormick <tony@mi-squared.com> 
 * Copyright (C) 2012 Julia Longtin <julialongtin@diasp.org>
 *
 * LICENSE: This Source Code is subject to the terms of the Mozilla Public License, v. 2.0
 * See the Mozilla Public License for more details. 
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 *
 * This file is included as an invisible iframe by forms that want to be
 * notified when a new message for the user comes in.
 *
 * @package LibreHealth EHR
 * @author  Julia Longtin <julialongtin@diasp.org>
 * @author  Tony McCormick <tony@mi-squared.com>
 * @link    http://librehealth.io
 */
/* for $GLOBALS[]. */
require_once('../../globals.php'); 
/* set the content type. */
header('Content-Type: application/javascript');
?>

var stickyUUIDs = new Array();
var stickycount = 0;
var reftime="<?php echo attr($GLOBALS['floating_message_alerts_timer']); ?>";
var parsetime=reftime.split(":");
if (parsetime > 30) {
   parsetime=(parsetime[0]*60)+(parsetime[1]*1)*1000;
} else {
   parsetime=(parsetime[0]*60)+(parsetime[1]*1)*1350;
}



hiddenIFrameload = function () {
    try {
        iframeloaded();
    } catch (e) {
        alert('exception: ' + e);
    }
};

// TODO - make this a Global config for the interval  (Time between displays was 30000)
setInterval(function () {
    $('#hiddenIFrame').attr('src', "<?php echo $GLOBALS['webroot']; ?>/interface/main/messages/message_status.php?skip_timeout_reset=1");
}, parsetime);

iframeloaded = function () {
    if ($('#hiddenIFrame').contents().find('#notices').text()) {
        var noticecount = $('#hiddenIFrame').contents().find('#noticecount').text();
        if (noticecount > 0) {
            for (var i = 0; i < noticecount; i++) {
                var parsenotice = $('#hiddenIFrame').contents().find('#notice' + i);
                if (parsenotice.find('.sticky').text() != "") {
                    var foundUUID = 0;
                    for (var j = 0; j <= stickycount; j++) {
                        if (stickyUUIDs[j] == parsenotice.find('.UUID').text()) {
                            foundUUID = 1;
                        }
                    }
                    ;
                    if (foundUUID == 0) {
                        stickyUUIDs[j] = parsenotice.find('.UUID').text();
                        stickycount++;
                        $.gritter.add({
                            title: '<span class="' + parsenotice.find('.colour').text() + '">' + parsenotice.find('.title').text() + '</span>',
                            text: parsenotice.find('.text').text(),
                            sticky: parsenotice.find('.sticky').text(),
                            time: '10000',
                            class_name: 'notice' + i + 'class'
                        });
                    }
                }
                else {
                    $.gritter.add({
                        title: '<span class="' + parsenotice.find('.colour').text() + '">' + parsenotice.find('.title').text() + '</span>',
                        text: parsenotice.find('.text').text(),
                        sticky: parsenotice.find('.sticky').text(),
                        time: '10000',
                        class_name: 'notice' + i + 'class'
                    });
                }
                ;
            }
            ;
        }
        ;
    }
    ;
};
$(function () {
    $('<iframe style="width:0px;height:0px;border:0px;" id="hiddenIFrame" onload="hiddenIFrameload()" src="<?php echo $GLOBALS['webroot']; ?>/interface/main/messages/message_status.php"/>').appendTo('body');
    $('#hiddenIFrame').attr('src', "<?php echo $GLOBALS['webroot']; ?>/interface/main/messages/message_status.php?skip_timeout_reset=1");
});
