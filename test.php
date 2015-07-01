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
			$id = valid($_REQUEST["id"]);
			selectStation($id);
			break;
		case "exitStation":
			$hash = valid($_REQUEST["hash"]);
			exitStation($hash);
			break;
		case "stationAuth":
			$login = valid($_REQUEST["login"]);
			$password = valid($_REQUEST["password"]);
			$hash = valid($_REQUEST["hash"]);
			$reg_id = valid($_REQUEST["reg_id"]);
			stationAuth($login, $password, $hash, $reg_id);
			break;
		case "customAuth":
			$login = valid($_REQUEST["login"]);
			$password = valid($_REQUEST["password"]);
			$id = valid($_REQUEST["id"]);
			customAuth($login, $password, $id);
			break;
		case "logout":
			$token = valid($_REQUEST["token"]);
			logout($token);
			break;
		case "enterKids":
			$qr = valid($_REQUEST["qr"]);
			$token = valid($_REQUEST["token"]);
			$hash = valid($_REQUEST["hash"]);
			enterKids($qr, $token, $hash);
			break;
		case "getStationKidsList":
			$hash = valid($_REQUEST["hash"]);
			getStationKidsList($hash);
			break;
		case "getListScenario":
			$hash = valid($_REQUEST["hash"]);
			getListScenario($hash);
			break;
		case "setScenario":
			$id_scenario = valid($_REQUEST["id_scenario"]);
			$token = valid($_REQUEST["token"]);
			$hash = valid($_REQUEST["hash"]);
			setScenario($id_scenario, $hash, $token);
			break;
		case "endLesson":
			$id = valid($_REQUEST["id"]);
			endLesson($id);
			break;
		case "exitKidsStation":
			$token = valid($_REQUEST["token"]);
			$hash = valid($_REQUEST["hash"]);
			exitKidsStation($hash, $token);
			break;
		case "extСhildrenStationNoMoney":
			$token = valid($_REQUEST["token"]);
			$id = valid($_REQUEST["id"]);
			$hash = valid($_REQUEST["hash"]);				
			extСhildrenStationNoMoney($id, $hash, $token);
			break;
		case "stationAuthSeniorMentor":
			$login = valid($_REQUEST["login"]);
			$password = valid($_REQUEST["password"]);
			stationAuthSeniorMentor($login, $password);
			break;
		case "logoutSeniorMentor":
			$token = valid($_REQUEST["token"]);
			logoutSeniorMentor($token);
			break;
		case "getSeniorMentorStationList":
			$token = valid($_REQUEST["token"]);
			getSeniorMentorStationList($token);
		case "send_notification":
			if (isset($_REQUEST["regId"]) && isset($_REQUEST["message"])) {
				$regId = $_REQUEST["regId"];
				$message = $_REQUEST["message"];
				$registatoin_ids = array($regId);
				$message = array("price" => $message);
				send_notification($registatoin_ids, $message);
			}
			break;
		case "enterKidsCity":
			$qr = valid($_REQUEST["qr"]);
			$id_city = valid($_REQUEST["id_city"]);
			enterKidsCity($id_city, $qr);
			break;
		case "exitKidsCity":
			$qr = valid($_REQUEST["qr"]);
			exitKidsCity($qr);
			break;
		case "getKidsOnIDCity":
			$id = valid($_REQUEST["id"]);
			getKidsOnIDCity($id);
			break;
		case "getMentorOnIDCity":
			$id = valid($_REQUEST["id"]);
			getMentorOnIDCity($id);
			break;
		case "setTimetable":
			$id_mentor = valid($_REQUEST["id_mentor"]);
			$id_station = valid($_REQUEST["id_station"]);
			$date = valid($_REQUEST["date"]);
			$change = valid($_REQUEST["change"]);
			setTimetable($id_mentor, $id_station, $date, $change);
			break;
		case "getTimetable":
			$token = valid($_REQUEST["token"]); 
			$start = valid($_REQUEST["start"]);
			$offset = valid($_REQUEST["offset"]);
			getTimetable($token, $start, $offset);
			break;
		case "getCities":
			getCities();
			break;
		case "sendMessage":
			$id_type = valid($_REQUEST["id_type"]);
			$token = valid($_REQUEST["token"]);
			$msg = valid($_REQUEST["msg"]);
			sendMessage($id_type, $token, $msg);
			break;
		case "getCityByToken":
			$token = valid($token);
			getCityByToken($token);
			break;
		default:
			break;
	}
?>