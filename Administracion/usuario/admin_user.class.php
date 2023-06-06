<?php
  class adminUser {
        	var  $db;
        	var  $usua;
        	var  $depe;
        	var  $usuad;
          var  $deped;
          var  $newDepe;
          var  $flag_conserva_radicados=false;

          public function adminuser($db,$loginO,$loginD=null,$newDepe=null){
          	 $this->db=$db;

             $this->user_trasition();
             /* consulta los datos del usuario origen */
             $rs=$this->db->conn->Execute("select * from USUARIO where USUA_LOGIN='".strtoupper ($loginO)."'");
          	 $this->usua=$rs->fields['USUA_CODI'];
          	 $this->depe=$rs->fields['DEPE_CODI'];
             /* consulta los datos del usuario destino si existe */
          	 if(!is_null($loginD)){
               $rs=$this->db->conn->Execute("select * from USUARIO where USUA_LOGIN='".strtoupper ($loginD)."'");
      	    	 $this->usuad=$rs->fields['USUA_CODI'];
      	    	 $this->deped=$rs->fields['DEPE_CODI'];
      	   	 }
             if(!is_null($newDepe)){
               $this->newDepe=$newDepe;
             }
          }
          public function user_trasition($flag_delete=0){
            if ($flag_delete==1){
                 $sql="delete usuario where usua_codi=999 and depe_codi=999";

            }else{
                // $sql="insert into usuario (usua_codi,depe_codi,usua_esta,usua_pasw,usua_login,USUA_FECH_CREA) values (999,999,1,'123456','temp_tras',SYSDATE)";
				$sql = "select * from usuario where usua_codi = 999";
            }
            $this->db->conn->Execute($sql);

          }
          public function modify_key_user($flag_boss=0,$newDepe=0){
              if($newDepe!=0){
                     $rs=$this->db->conn->Execute("select MAX(usua_codi) as CODI from usuario where depe_codi=".$newDepe);
                     $codi=$rs->fields['CODI'] + 1;
                     $sql="update usuario set usua_codi=".$codi.", depe_codi=".$newDepe." where usua_codi=".$this->usua." and depe_codi=".$this->depe; 
                     $this->db->conn->Execute($sql);
                     return $codi;

              }else{
                  if ($flag_boss==1){
                     $sql="update usuario set usua_codi=1 where usua_codi=".$this->usua." and depe_codi=".$this->depe; 
                     $this->db->conn->Execute($sql);
                     return 1;

                  }else{
                     $rs=$this->db->conn->Execute("select MAX(usua_codi) as CODI from usuario where depe_codi=".$this->depe);
                     $codi=$rs->fields['CODI'] + 1;
                     $sql="update usuario set usua_codi=".$codi." where usua_codi=1 and depe_codi=".$this->depe; 
                     $this->db->conn->Execute($sql);
                     return $codi;
                  }

              }
          }


          public function modify_user($usuaOrg,$depeOrg,$usuaDes,$depeDes){


                  $sql="update radicado set 
                      radi_usua_actu=".$usuaDes.", 
                      radi_depe_actu=".$depeDes." 
                        where 
                        radi_usua_actu=".$usuaOrg." and 
                        radi_depe_actu=".$depeOrg;
                  $this->db->conn->Execute($sql);
                  $sql="update informados set 
                              usua_codi=".$usuaDes.", 
                                depe_codi=".$depeDes." 
                                where 
                                usua_codi=".$usuaOrg." and 
                                depe_codi=".$depeOrg;
                  $this->db->conn->Execute($sql);
                  $sql="update carpeta_per set 
                              usua_codi=".$usuaDes.", 
                                depe_codi=".$depeDes."
                                where 
                                usua_codi=".$usuaOrg." and 
                                depe_codi=".$depeOrg;
                  $this->db->conn->Execute($sql);

          }
          public function to_boss_user(){
              $rs=$this->db->conn->Execute("select * from USUARIO where DEPE_CODI=".$this->depe." and USUA_CODI=1");
              if (isset($rs->fields['USUA_CODI']) && $rs->fields['USUA_CODI']==1){
                      $this->modify_user(1,$this->depe,999,999);
                      $newuser=$this->modify_key_user();
                      if ($this->flag_conserva_radicados){
                          $this->modify_user(999,999,$newuser,$this->depe);
                      }

              }
              $this->modify_user($this->usua,$this->depe,999,999);
              $this->modify_key_user(1);
              $this->modify_user(999,999,1,$this->depe);
              $this->user_trasition(1);
          }

          public function move_radicados_to_user(){
              $this->modify_user($this->usua,$this->depe,$this->usuad,$this->deped);
          }
          public function move_user_to_dependence(){
              $this->modify_user($this->usua,$this->depe,999,999);
              $codi=$this->modify_key_user(0,$this->newDepe);
              $this->modify_user(999,999,$codi,$this->newDepe);
              $this->user_trasition(1);
          }
  }
?>
