<?
	require_once "r.php";
	
	$cmd = "";
	
	if (isset($_REQUEST["cmd"]))
		$cmd = $_REQUEST["cmd"];
	
	switch ($cmd)
	{
		case "getStationList":
			getStationList();
			break;
		case "selectStation":
			if (isset($_REQUEST["id"]))
				$id = htmlspecialchars($_REQUEST["id"]);
			else
				$id = "";
			selectStation($id);
			break;
		case "exitStation":
			if (isset($_REQUEST["hash"]))
				$hash = htmlspecialchars($_REQUEST["hash"]);
			else
				$hash = "";
			exitStation($hash);
			break;
		case "stationAuth":
			if (isset($_REQUEST["login"]))
				$login = htmlspecialchars($_REQUEST["login"]);
			else
				$login = "";
			
			if (isset($_REQUEST["password"]))
				$password = htmlspecialchars($_REQUEST["password"]);
			else
				$password = "";
				
			if (isset($_REQUEST["hash"]))
				$hash = htmlspecialchars($_REQUEST["hash"]);
			else
				$hash = "";
				
			stationAuth($login, $password, $hash);
			break;
		case "logout":
			if (isset($_REQUEST["token"]))
				$token = htmlspecialchars($_REQUEST["token"]);
			else
				$token = "";
				
			logout($token);
			break;
		case "enterKids":
			if (isset($_REQUEST["qr"]))
				$qr = htmlspecialchars($_REQUEST["qr"]);
			else
				$qr = "";
			
			if (isset($_REQUEST["token"]))
				$token = htmlspecialchars($_REQUEST["token"]);
			else
				$token = "";
				
			if (isset($_REQUEST["hash"]))
				$hash = htmlspecialchars($_REQUEST["hash"]);
			else
				$hash = "";
				
			enterKids($qr, $token, $hash);
			break;
		case "getStationKidsList":
			if (isset($_REQUEST["hash"]))
				$hash = htmlspecialchars($_REQUEST["hash"]);
			else
				$hash = "";
				
			getStationKidsList($hash);
			break;
		case "getListScenario":
			if (isset($_REQUEST["hash"]))
				$hash = htmlspecialchars($_REQUEST["hash"]);
			else
				$hash = "";
				
			getListScenario($hash);
			break;
		case "setScenario":
			if (isset($_REQUEST["id_scenario"]))
				$id_scenario = htmlspecialchars($_REQUEST["id_scenario"]);
			else
				$id_scenario = "";
			
			if (isset($_REQUEST["token"]))
				$token = htmlspecialchars($_REQUEST["token"]);
			else
				$token = "";
				
			if (isset($_REQUEST["hash"]))
				$hash = htmlspecialchars($_REQUEST["hash"]);
			else
				$hash = "";
				
			setScenario($id_scenario, $hash, $token);
			break;
		default:
			break;
	}
?>