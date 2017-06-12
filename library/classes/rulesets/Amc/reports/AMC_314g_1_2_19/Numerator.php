<?php
/**
 *
 * AMC 314g_1_2_19 STAGE1 Numerator
 *
 * Copyright (C) 2015 Ensoftek, Inc
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
 * @package LibreEHR
 * @author  Ensoftek
 * @link    http://librehealth.io
 */

class AMC_314g_1_2_19_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_314g_1_2_19 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        //Secure electronic message received by EP using secure electronic messaging function of CEHRT
        $smQry = "SELECT  * FROM `pnotes` WHERE `user` = ? AND `date` >= ? AND `date` <= ?";
        $check = sqlQuery( $smQry, array($patient->id, $beginDate, $endDate) );
        if (!(empty($check))){
            return true;
        }else{
            return false;
        }
    }
}
?>
