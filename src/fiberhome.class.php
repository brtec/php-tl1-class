<?php
/**
* Classe de Acesso ao TL1 Server no ANM e UNM2000
*
* @package    FiberHome
* @author     Bruno Rezende
* @version    2.0 BRTec
* @version	  2.0 Zap ANM/UNM
*/

class FiberHome
{
	private $fp;
	private $ipTL1;
	private $DEBUG;


	function __construct($ipAdmin = '', $ipTL1 = '', $User = '', $Pass = '', $debug=true)
	{
		$this->DEBUG = $debug;

		$this->fp = fsockopen($ipAdmin, 3337, $errno, $errstr, 30);
		if (!$this->fp) {
			die("$errstr ($errno)\n");
		} else {
			$login = $this->cmd("LOGIN:::CTAG::UN={$User},PWD={$Pass};");			
			/*if ($this->CMD_OK($login)) {				
			}else{$this->cmd("LOGIN:::CTAG::UN={$User},PWD={$Pass};");	}
			*/ 
			$this->ipTL1 = $ipTL1;
		}
		
		//echo "teste";
	}

	public function cmd($cmd = '')
	{
		$ret = array();

		$this->msg($cmd);
		fwrite($this->fp, "$cmd\n");

		while (true) {
			$c = fread($this->fp, 1);
			if ($this->DEBUG) echo $c;
			if ($c == ';') break;
			$lin = trim($c . fgets($this->fp));
			//var_dump($lin);
			$ret[] = explode("\t", $lin);
			$this->msg($lin);
		}
		return $ret;
	}
	
	function CMD_OK($ret = '') { 
        if (strpos($ret[3][0], 'ENDESC=No error') > 0) { 
            return true; 
        } else { 
            return false; 
        } 
    } 
	

	private function msg($msg = '')
	{
		if ($this->DEBUG) {
			echo trim($msg)."\n";
		}
	}

	 
	 public function ListAllONUs($OLTID ='', $PONID ='')
	 {
		$onus = $this->cmd("LST-ONU::OLTID=$OLTID,PONID=$PONID:CTAG::;");
		$co = 0;
		//echo "<pre>";var_dump($onus);
		
		foreach($onus as $c => $v)
		{
			//echo "<pre>";var_dump($v);
			if(sizeof($v) == "12")
			{
				$vLogin = explode(" ",$v[3]);
				$LOGIN = $vLogin[0];
				$MAC = $v[8];
				$PON_ID_FOUND = $v[1];
				
				//ORGANIZE DATA
				$RETORNO["ONUs"][$LOGIN]["OLTID"] = $v[0];
				$RETORNO["ONUs"][$LOGIN]["PONID"] = $v[1];
				$RETORNO["ONUs"][$LOGIN]["ONUNO"] = $v[2];
				$RETORNO["ONUs"][$LOGIN]["NAME"] = $v[3];
				$RETORNO["ONUs"][$LOGIN]["DESC"] = $v[4];
				$RETORNO["ONUs"][$LOGIN]["ONUTYPE"] = $v[5];
				$RETORNO["ONUs"][$LOGIN]["IP"] = $v[6];
				$RETORNO["ONUs"][$LOGIN]["AUTHTYPE"] = $v[7];
				$RETORNO["ONUs"][$LOGIN]["MAC"] = $v[8];
				$RETORNO["ONUs"][$LOGIN]["LOID"] = $v[9];
				$RETORNO["ONUs"][$LOGIN]["PWD"] = $v[10];
				$RETORNO["ONUs"][$LOGIN]["SWVER"] = $v[11];
				
			}
		}
		//REMOVE TITLE
		unset($RETORNO["ONUs"]["NAME"]);		 
		return $RETORNO;
	 }
	 
	 public function ListAllONUs2($OLTID = NULL)
	 {
		$onus = $this->cmd("LST-ONU::OLTID=$OLTID:CTAG::;");
		$co = 0;
		//echo "<pre>";var_dump($onus);die();
		
		foreach($onus as $c => $v)
		{
			//echo "<pre>";var_dump($v);
			if(sizeof($v) == "12")
			{
				$vLogin = explode(" ",$v[3]);
				$LOGIN = $vLogin[0];
				$MAC = $v[8];
				$PON_ID_FOUND = $v[1];
				
				//ORGANIZE DATA
				$RETORNO["ONUs"][$LOGIN]["OLTID"] = $v[0];
				$RETORNO["ONUs"][$LOGIN]["PONID"] = $v[1];
				$RETORNO["ONUs"][$LOGIN]["ONUNO"] = $v[2];
				$RETORNO["ONUs"][$LOGIN]["NAME"] = $v[3];
				$RETORNO["ONUs"][$LOGIN]["DESC"] = $v[4];
				$RETORNO["ONUs"][$LOGIN]["ONUTYPE"] = $v[5];
				$RETORNO["ONUs"][$LOGIN]["IP"] = $v[6];
				$RETORNO["ONUs"][$LOGIN]["AUTHTYPE"] = $v[7];
				$RETORNO["ONUs"][$LOGIN]["MAC"] = $v[8];
				$RETORNO["ONUs"][$LOGIN]["LOID"] = $v[9];
				$RETORNO["ONUs"][$LOGIN]["PWD"] = $v[10];
				$RETORNO["ONUs"][$LOGIN]["SWVER"] = $v[11];
				
			}
		}
		//REMOVE TITLE
		unset($RETORNO["ONUs"]["NAME"]);		 
		return $RETORNO;
	 }
	
	 
	  public function getUNREGISTERED($OLTID='', $PONID='')
	 {
	 	
		$onus = $this->cmd("LST-UNREGONU::OLTID=$OLTID:CTAG::;");
		$co = 0;
		foreach($onus as $c => $v)
		{
			//echo "<pre>";var_dump($v);
			
			
			if(sizeof($v) == "8")
			{
				$vLogin = explode(" ",$v[2]);
				$LOGIN = $vLogin[0];
				$MAC = $v[2];
				$PON_ID_FOUND = $v[1];
				
				//Open all Data
				
				
				$RETORNO["ONUs"][$LOGIN]["SLOTNO"] = $v[0];
				$RETORNO["ONUs"][$LOGIN]["PONNO"] = $v[1];
				$RETORNO["ONUs"][$LOGIN]["MAC"] = $v[2];
				$RETORNO["ONUs"][$LOGIN]["LOID"] = $v[3];
				$RETORNO["ONUs"][$LOGIN]["PWD"] = $v[4];
				$RETORNO["ONUs"][$LOGIN]["ERROR"] = $v[5];
				$RETORNO["ONUs"][$LOGIN]["AUTHTIME"] = $v[6];
				$RETORNO["ONUs"][$LOGIN]["DT"] = $v[7];
				
			}
			
		}
				
		//Removendo os Titles
		unset($RETORNO["ONUs"]["MAC"]);
		
		//echo "<pre>";var_dump($RETORNO);die();
		return $RETORNO;
	 }
	
	
	  public function adicionarONU($OLTID = '', $PON_ID_FOUND = '', $MAC = '', $NAME = '', $ONUTYPE = '')
	 {
			
		    //$list = $this->cmd("ADD-ONU::OLTID=$OLTID,PONID=$PON_ID_FOUND:CTAG::AUTHTYPE=MAC,ONUID=$MAC,NAME=$NAME,ONUTYPE=$ONUTYPE;"); 
		    $list = $this->cmd("ADD-ONU::OLTID=$OLTID,PONID=$PON_ID_FOUND:CTAG::AUTHTYPE=MAC,ONUID=$MAC,NAME=$NAME,ONUTYPE=$ONUTYPE;"); 
			//var_dump($list);die();
			if ($this->CMD_OK($list)) { 
	            return "ONU MAC:$MAC, PONID: $PON_ID_FOUND, foi registrada na OLT $OLTID.<br><br>"; 
	        } else { 
	            return("ERROR 01"); 
	        } 
	        
	       // return($list); 
	 }
	 
	 

	 
	 
	 public function configuraONU($OLTID = '', $PON_ID_FOUND = '', $MAC = '', $USER = '', $VLAN = '', $UP = '', $DOWN = '')
	 {
	 		
	 	$add = $this->cmd("SET-WANSERVICE::OLTID=$OLTID,PONID=$PON_ID_FOUND,ONUIDTYPE=MAC,ONUID=$MAC:CTAG::STATUS=1,MODE=2,CONNTYPE=2,VLAN=$VLAN,NAT=1,IPMODE=3,COS=1,QOS=2,PPPOEPROXY=2,PPPOEUSER=$USER,PPPOEPASSWD=123456,PPPOENAME=122209204551,PPPOEMODE=1,UPORT=1;"); 	
	 	
		if ($this->CMD_OK($add)) { 
	            return "ONU MAC:$MAC, PONID: $PON_ID_FOUND, Login PPPOE:$USER, registrada na OLT $OLTID, foi configurada com sucesso para PPPOE.<br><br>"; 
	        } else { 
	            return("ERROR 02"); 
	        } 
	        
		
	 }
	 
	 
     public function bandwidthLimit($OLTID = '', $PON_ID_FOUND = '', $MAC = '', $PROFILE = '')
	 {
			
		    $bandwidth_limit = $this->cmd("CFG-ONUBW::OLTID=$OLTID,PONID=$PON_ID_FOUND,ONUIDTYPE=MAC,ONUID=$MAC:CTAG::UPBW=$PROFILE,DOWNBW=$PROFILE;");
			
			//var_dump($bandwidth_limit);
			
			if ($this->CMD_OK($bandwidth_limit)) { 
	            return "OK"; 
	        } else { 
	            return("ERROR 03"); 
	        } 
	        
	       // return($list); 
	 }
	 
	 
	 
	 
	 
	 
	 
	 
	  public function configuraONUBRIDGEI($OLTID = '', $PON_ID_FOUND = '', $MAC = ''){
	 		
	 	$add_bridge = $this->cmd("CFG-LANPORTVLAN::OLTID=$OLTID,PONID=$PON_ID_FOUND,AUTHTYPE=MAC,ONUID=$MAC,ONUPORT=NA-NA-NA-1:CTAG::COS=3,VLANMODE=Tag,CVLAN=101;"); 	
	 	
		if ($this->CMD_OK($add_bridge)) { 
	            return "ONU MAC:$MAC, PONID: $PON_ID_FOUND, registrada na OLT $OLTID, foi configurada com sucesso para BRIDGE.<br><br>"; 
	        } else { 
	            return("ERROR 04"); 
	        } 
	        
		
	 }
	  
	  
	   public function configuraONUBRIDGEV($OLTID = '', $PON_ID_FOUND = '', $MAC = ''){
	 		
	 	$add_bridge = $this->cmd("CFG-LANPORTVLAN::OLTID=$OLTID,PONID=$PON_ID_FOUND,AUTHTYPE=MAC,ONUID=$MAC,ONUPORT=NA-NA-NA-1:CTAG::COS=3,VLANMODE=Tag,CVLAN=110;"); 	
	 	
		if ($this->CMD_OK($add_bridge)) { 
	            return "ONU MAC:$MAC, PONID: $PON_ID_FOUND, registrada na OLT $OLTID, foi configurada com sucesso para BRIDGE.<br><br>"; 
	        } else { 
	            return("ERROR 04"); 
	        } 
	        
		
	 }
	   
	   
	   public function configuraONUBRIDGED($OLTID = '', $PON_ID_FOUND = '', $MAC = ''){
	 		
	 	$add_bridge = $this->cmd("CFG-LANPORTVLAN::OLTID=$OLTID,PONID=$PON_ID_FOUND,AUTHTYPE=MAC,ONUID=$MAC,ONUPORT=NA-NA-NA-1:CTAG::COS=3,VLANMODE=Tag,CVLAN=102;"); 	
	 	
		if ($this->CMD_OK($add_bridge)) { 
	            return "ONU MAC:$MAC, PONID: $PON_ID_FOUND, registrada na OLT $OLTID, foi configurada com sucesso para BRIDGE.<br><br>"; 
	        } else { 
	            return("ERROR 04"); 
	        } 
	        
		
	 }
	   
	
	 public function deletaONU($OLTID = '', $PON_ID_FOUND = '', $MAC = ''){
	 		
	 	
	 	$del = $this->cmd("DEL-ONU::OLTID=$OLTID,PONID=$PON_ID_FOUND:CTAG::ONUIDTYPE=MAC,ONUID=$MAC;"); 
	 	//echo "<pre>";
		//var_dump("DEL-ONU::OLTID=$OLTID,PONID=$PON_ID_FOUND:CTAG::ONUIDTYPE=MAC,ONUID=$MAC;");
	 	//var_dump($del);
	 	//die(); 	
		if ($this->CMD_OK($del)) { 
	            return "ONU MAC:$MAC, PONID: $PON_ID_FOUND, registrada na OLT $OLTID, foi deletada com sucesso<br><br>"; 
	        } else { 
	            return("ERROR 05"); 
	        } 
	        
		
	 }
	
	
	
	
	 public function getONUData($OLTID = '', $PON_ID_FOUND = '', $MAC = '')
	 {
		$onuData = $this->cmd("LST-OMDDM::OLTID=$OLTID,PONID=$PON_ID_FOUND,AUTHTYPE=MAC,ONUID=$MAC:CTAG::;");
		
		
		foreach($onuData as $a => $b)
		{
			if(sizeof($b) == "13")
			{
				$RETORNO["ONU"]["ONUID"] = $b[0];
				$RETORNO["ONU"]["RxPower"] = $b[1];
				$RETORNO["ONU"]["RxPowerR"] = $b[2];
				$RETORNO["ONU"]["TxPower"] = $b[3];
				$RETORNO["ONU"]["TxPowerR"] = $b[4];
				$RETORNO["ONU"]["CurrTxBias"] = $b[5];
				$RETORNO["ONU"]["CurrTxBiasR"] = $b[6];
				$RETORNO["ONU"]["Temperature"] = $b[7];
				$RETORNO["ONU"]["TemperatureR"] = $b[8];
				$RETORNO["ONU"]["Voltage"] = $b[9];
				$RETORNO["ONU"]["VoltageR"] = $b[10];
				$RETORNO["ONU"]["PTxPower"] = $b[11];
				$RETORNO["ONU"]["PRxPower"] = $b[12];
				
			}
		}
		//unset($RETORNO["ONUs"]["NAME"]);		 
		return $RETORNO;
	 }

	




	 public function getONUWANData($OLTID = '', $PON_ID_FOUND = '', $MAC = '')
	 {	
		$wanData = $this->cmd("LST-ONUWANSERVICECFG::OLTID=$OLTID,PONID=$PON_ID_FOUND,AUTHTYPE=MAC,ONUID=$MAC:CTAG::;");
		
		$RETORNO["ONU"]["SVCNAME"] = "";
				$RETORNO["ONU"]["CONNMODE"] = "";
				$RETORNO["ONU"]["CONNTYPE"] = "";
				$RETORNO["ONU"]["VLANID"] = "";
				$RETORNO["ONU"]["VLANCOS"] = "";
				$RETORNO["ONU"]["NATFLAG"] = "";
				$RETORNO["ONU"]["IPOBTAINTYPE"] = "";
				$RETORNO["ONU"]["STATICIPADDRESS"] = "";
				$RETORNO["ONU"]["STATICIPSUBNET"] = "";
				$RETORNO["ONU"]["STATICGATEWAY"] = "";
				$RETORNO["ONU"]["MASTERDNS"] = "";
				$RETORNO["ONU"]["SLAVEDNS"] = "";
				$RETORNO["ONU"]["PPPOEPROXYFLAG"] = "";
				$RETORNO["ONU"]["PPPOEUSERNAME"] = "";
				$RETORNO["ONU"]["PPPOEPASSWD"] = "";
				$RETORNO["ONU"]["PPPOESVCNAME"] = "";
				$RETORNO["ONU"]["PPPOEMODE"] = "";
				$RETORNO["ONU"]["QOSFLAG"] = "";	
				$RETORNO["ONU"]["BINDPORTNO"] = "";		
		
		foreach($wanData as $a => $b)
		{
			if(sizeof($b) == "19")
			{
				$RETORNO["ONU"]["SVCNAME"] = $b[0];
				$RETORNO["ONU"]["CONNMODE"] = $b[1];
				$RETORNO["ONU"]["CONNTYPE"] = $b[2];
				$RETORNO["ONU"]["VLANID"] = $b[3];
				$RETORNO["ONU"]["VLANCOS"] = $b[4];
				$RETORNO["ONU"]["NATFLAG"] = $b[5];
				$RETORNO["ONU"]["IPOBTAINTYPE"] = $b[6];
				$RETORNO["ONU"]["STATICIPADDRESS"] = $b[7];
				$RETORNO["ONU"]["STATICIPSUBNET"] = $b[8];
				$RETORNO["ONU"]["STATICGATEWAY"] = $b[9];
				$RETORNO["ONU"]["MASTERDNS"] = $b[10];
				$RETORNO["ONU"]["SLAVEDNS"] = $b[11];
				$RETORNO["ONU"]["PPPOEPROXYFLAG"] = $b[12];
				$RETORNO["ONU"]["PPPOEUSERNAME"] = $b[13];
				$RETORNO["ONU"]["PPPOEPASSWD"] = $b[14];
				$RETORNO["ONU"]["PPPOESVCNAME"] = $b[15];
				$RETORNO["ONU"]["PPPOEMODE"] = $b[16];
				$RETORNO["ONU"]["QOSFLAG"] = $b[17];	
				$RETORNO["ONU"]["BINDPORTNO"] = $b[18];																											
			}
		}
		//unset($RETORNO["ONUs"]["NAME"]);		 
		return $RETORNO;
	 }
	
	
	public function logOut()
	 {	
		$wanData = $this->cmd("LOGOUT:::CTAG::;");
				
		//$this->fp->fclose();
		fclose();
		if($wanData)
		return "Sessão encerrada";
	 }
	 

	 
	 public function mantemAtivo()
	 {	
		$wanData = $this->cmd("SHAKEHAND:::CTAG::;"); 
		if($wanData)
		return true;
	 }	
	 
	 
	 
}