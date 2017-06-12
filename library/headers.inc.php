<?php

/** 
 *  Common include pathing and related native LHEHR functions
 * 
 * Copyright (C) 2017 Art Eaton
 * SOURCE:  Ken Chapple at mi-squared.com
 * 
 * LICENSE: This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0
 * See the Mozilla Public License for more details. 
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * 
 * @package Librehealth EHR 
 * @author Art Eaton <art@suncoastconnection.com>
 * @link http://librehealth.io
 *  
 * Please help the overall project by sending changes you make to the author and to the LibreEHR community.
 * 
 */

function include_js_library($path)
{
?>
<script type="text/javascript" src="<?php echo $GLOBALS['standard_js_path'].$path;?>"></script>
<?php
}