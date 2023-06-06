<?php
/**
 * @author 
 * @author fundacion Correlibre.org  07/2017
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2013 Infometrika Ltda.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class Menus{
    function __construct($db){
        $this->db = $db;
    }
    function getArchiveCount(){
        $isql = "select count(1) as CONTADOR
							from SGD_EXP_EXPEDIENTE
							where
							sgd_exp_estado=0 ";
        $rs=$this->db->conn->Execute($isql);
        return $rs->fields["CONTADOR"];
    }
    function getUsers($email){
        $isql = "SELECT u.usua_login,u.USUA_CODI,d.depe_codi, d.depe_nomb, u.usua_nomb, u.USUA_EMAIL FROM USUARIO u, dependencia d    WHERE u.depe_Codi=d.depe_codi and upper(u.usua_email) = '".strtoupper($email)."' and u.usua_email is not null  and u.usua_esta = '1' ";       
        $rs = $this->db->conn->query($isql);
        return $rs;
    }

}
?>