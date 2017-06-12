<?php
/**
 * Encounter form report function.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * LICENSE: This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 * See the Mozilla Public License for more details.
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package LibreHealth EHR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://librehealth.io
 */

include_once(dirname(__file__)."/../../globals.php");

function patient_encounter_report( $pid, $encounter, $cols, $id) {
    $res = sqlStatement("select * from form_encounter where pid=? and id=?", array($pid,$id) );
    print "<table><tr><td>\n";
    while($result = sqlFetchArray($res)) {
        print "<span class=bold>" . xlt('Facility') . ": </span><span class=text>" . text($result{"facility"}) . "</span><br>\n";
        print "<span class=bold>" . xlt('Reason') . ": </span><span class=text>" . nl2br(text($result{"reason"})) . "</span><br>\n";
    }
    print "</td></tr></table>\n";
}

?>
