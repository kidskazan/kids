<?
	require_once "class/dataTable.class.php";
	
	
	//Получить список всех станций.
	//Возвращает - name, id
	function getStationList()
	{
		$stat = new dataTable("stations");
		
		$stations = $stat->getFields(array("id", "name"));
		$result["status"] = "ok";
		$result["result"] = $stations;
		echo json_encode($result);
	}
	
	//Выбор станции.
	//Входные параметры:
	//	$id - id станции
	//Возвращает hash
	//Ошибки: 101, 102
	function selectStation($id)
	{
		global $error;
		$stat = new dataTable("stations");
		
		$where = "";
		$where = where($where, "id", "=", $id);
		$stations = $stat->getFields(array("*"), $where);
		
		if (count($stations) == 0)
			$r = setError(101);
		elseif ($stations[0]["hash"] != "")
			$r = setError(102);
		else
		{
			$r["status"] = "ok";
			$hash = md5($id+time());
			$r["hash"] = $hash;
			
			$upd["hash"] = $hash;
			$stat->update($upd, $where);
		}
		
		echo json_encode($r);
		
	}
	
	//выход из станции устройства
	//Входные параметры:
	//	$hash - hash станции
	//Возвращает status
	//Ошибки: 101, 103
	function exitStation($hash)
	{
		global $error;
		$stat = new dataTable("stations");
		
		if ($hash == "")
		{
			$r = setError(103);
			
			echo json_encode($r);
			exit;
		}
		
		$where = "";
		$where = where($where, "hash", "=", $hash);
		$stations = $stat->getFields(array("*"), $where);
		
		if (count($stations) == 0)
			$r = setError(101);
		else
		{
			$r["status"] = "ok";
			
			$upd["hash"] = "";
			$stat->update($upd, $where);
		}
		
		echo json_encode($r);
	}
	
	
	//авторизация наставника
	//Входные параметры:
	//	$login - логин наставника
	//	$password - пароль наставника
	//	$hash - hash станции
	//Возвращает token
	//Ошибки: 201, 202, 203, 204, 103, 101
	
	function stationAuth($login, $password, $hash)
	{
		global $error;
		$mentor = new dataTable("mentor");
		
		$where = "";
		$where = where($where, "login", "=", $login);
		$where = where($where, "AND");
		$where = where($where, "password", "=", md5($password));
		
		$r_mentor = $mentor->getFields(array("*"), $where);
		
		$where = "";
		$stat = new dataTable("stations");
		$where = where($where, "hash", "=", $hash);
		$stations = $stat->getFields(array("*"), $where);
		
		if (count($r_mentor) == 0)
			$r = setError(201);
		elseif ($r_mentor[0]["token"] != "")
			$r = setError(202);
		elseif($login == "")
			$r = setError(203);
		elseif($password == "")
			$r = setError(204);
		elseif($hash == "")
			$r = setError(103);
		elseif (count($stations) == 0)
			$r = setError(101);
		else
		{
			$r["status"] = "ok";
			
			$token = md5($r_mentor[0]["id"] + time());
			$r["token"] = $token;
			
			$upd = "";
			$upd["token"] = $token;
			$where = "";
			$where = where($where, "id", "=", $r_mentor[0]["id"]);
			$mentor->update($upd, $where);
			
			$upd = "";
			$upd["id_station"] = $stations[0]["id"];
			$upd["id_mentor"] = $r_mentor[0]["id"];
			$upd["input"] = time();
			$upd["token"] = $token;
			
			$sess_station = new dataTable("sess_station");
			$sess_station->add($upd);
		}
		
		echo json_encode($r);
	}
	
	//выход наставника из станции
	//Входные параметры:
	//	$token - token наставника
	//Возвращает status
	//Ошибки: 205, 206
	function logout($token)
	{
		global $error;
		
		if ($token == "")
		{
			$r = setError(206);
			
			echo json_encode($r);
			exit;
		}
		
		$mentor = new dataTable("mentor");
		$where = "";
		$where = where($where, "token", "=", $token);
		$r_mentor = $mentor->getFields(array("*"), $where);
		
		if (count($r_mentor) == 0)
			$r = setError(205);
		else
		{
			$r["status"] = "ok";
			$id_mentor = $r_mentor[0]["id"];
			
			$upd["token"] = "";
			$where = "";
			$where = where($where, "id", "=", $id_mentor);
			$mentor->update($upd, $where);
			
			$sess_station = new dataTable("sess_station");
			$upd = "";
			$upd["exit"] = time();
			$where = "";
			$where = where($where, "token", "=", $token);
			$sess_station->update($upd, $where);
		}
		
		echo json_encode($r);
	}
	
	//вход ребенка на станцию
	//Входные параметры:
	//	$qr - qr код ребенка
	//	$token - token наставника
	//	$hash - хеш станции 
	//Возвращает status
	//Ошибки: 206, 103, 301, 302, 205, 101, 303
	function enterKids($qr, $token, $hash)
	{
		global $error;
		
		if ($token == "")
		{
			$r = setError(206);
			
			echo json_encode($r);
			exit;
		}
		
		if ($hash == "")
		{
			$r = setError(103);
			
			echo json_encode($r);
			exit;
		}
		
		if ($qr == "")
		{
			$r = setError(301);
			
			echo json_encode($r);
			exit;
		}
		
		$kids = new dataTable("kids");
		$where = "";
		$where = where($where, "qr", "=", $qr);
		$r_kids = $kids->getFields(array("*"), $where);
		
		$mentor = new dataTable("mentor");
		$where = "";
		$where = where($where, "token", "=", $token);
		$r_mentor = $mentor->getFields(array("*"), $where);
		
		$stations = new dataTable("stations");
		$where = "";
		$where = where($where, "hash", "=", $hash);
		$r_stations = $stations->getFields(array("*"), $where);
		
		if (count($r_kids) == 0)
			$r = setError(302);
		elseif (count($r_mentor) == 0)
			$r = setError(205);
		elseif (count($r_stations) == 0)
			$r = setError(101);
		elseif ($r_kids[0]["money"] < $r_stations[0]["price"])
			$r = setError(303);
		else
		{
			$r["status"] = "ok";
			
			$upd = "";
			$upd["money"] = $r_kids[0]["money"] - $r_stations[0]["price"];
			$upd["id_station"] = $r_stations[0]["id"];
			
			$where = "";
			$where = where($where, "id", "=", $r_kids[0]["id"]);
			$kids->update($upd, $where);
			
			$sess_station = new dataTable("sess_station");
			$where = "";
			$where = where($where, "token", "=", $token);
			$count_kids = $sess_station->getFields(array("count_kids"), $where);
			$count_kids = $count_kids[0]["count_kids"] + 1;
			$upd = "";
			$upd["count_kids"] = $count_kids;
			$sess_station->update($upd, $where);
			
			$sess_kids = new dataTable("sess_kids");
			$upd = "";
			$upd["id_kids"] = $r_kids[0]["id"];
			$upd["id_station"] = $r_stations[0]["id"];
			$upd["id_mentor"] = $r_mentor[0]["id"];
			$upd["action"] = 1;
			$upd["date"] = time();
			$sess_kids->add($upd);
		}
		
		echo json_encode($r);
	}
	
	//получить всех детей на станции
	//Входные параметры:
	//	$hash - хеш станции 
	//Возвращает массив всех детей
	//Ошибки: 103, 101
	function getStationKidsList($hash)
	{
		if ($hash == "")
		{
			$r = setError(103);
			
			echo json_encode($r);
			exit;
		}
		
		$stations = new dataTable("stations");
		$where = "";
		$where = where($where, "hash", "=", $hash);
		$id_station = $stations->getFields(array("id"), $where);
		
		if (count($id_station) == 0)
			$r = setError(101);
		else
		{
			$r["status"] = "ok";
			
			$id_station = $id_station[0]["id"];
			$kids = new dataTable("kids");
			$where = "";
			$where = where($where, "id_station", "=", $id_station);
			$r_kids = $kids->getFields(array("*"), $where);
			
			$r["result"] = $r_kids;
		}
		
		echo json_encode($r);
	}
	
	//получить список сценариев на станции 
	//Входные параметры:
	//	$hash - хеш станции 
	//Возвращает массив сценариев
	//Ошибки: 103, 101
	function getListScenario($hash)
	{
		if ($hash == "")
		{
			$r = setError(103);
			
			echo json_encode($r);
			exit;
		}
		
		$stations = new dataTable("stations");
		$where = "";
		$where = where($where, "hash", "=", $hash);
		$r_stations = $stations->getFields(array("id"), $where);
		
		if (count($r_stations) == 0)
			$r = setError(101);
		else
		{
			$id_station = $r_stations[0]["id"];
			
			$scenario = new dataTable("scenario");
			$where = "";
			$where = where($where, "id_station", "=", $id_station);
			$r_scenario = $scenario->getFields(array("id", "name"), $where);
			
			$r["status"] = "ok";
			$r["result"] = $r_scenario;
		}
		
		echo json_encode($r);
	}
	
	//установить сценарий на станции
	//Входные параметры:
	//	$hash - хеш станции 
	//	$id_scenario - id сценария 
	//	$token - token наставника
	//Возвращает id занятия
	//Ошибки: 
	function setScenario($id_scenario, $hash, $token)
	{
		if ($hash == "")
		{
			$r = setError(103);
			
			echo json_encode($r);
			exit;
		}
		
		if ($token == "")
		{
			$r = setError(206);
			
			echo json_encode($r);
			exit;
		}
		
		$stations = getStationByHash($hash);
		$mentor = getMentorByToken($token);
		
		if ($stations == null)
			$r = setError(101);
		elseif ($mentor == null)
			$r = setError(205);
		else
		{
			$sess_scenario = new dataTable("sess_scenario");
			
			$where = "";
			$where = where($where, "id_scenario", "=", $id_scenario);
			$where = where($where, "AND");
			$where = where($where, "end", "=", "");
			
			$end_sess_scenario = $sess_scenario->getFields(array("*"), $where);
			if (count($end_sess_scenario) != 0)
				$r = setError(401);
			else
			{
				$r["status"] = "ok";
			
				$sess_lessons = new dataTable("sess_lessons");
				
				$where = "";
				$where = where($where, "id_mentor", "=", $mentor["id"]);
				$where = where($where, "AND");
				$where = where($where, "id_station", "=", $stations["id"]);
			
				$id_lessons = $sess_lessons->getFields(array("*"), $where);
				
				if ((count($id_lessons) == 0) or ($id_lessons[0]["end"] != ""))
				{
					$upd = "";
					$upd["id_mentor"] = $mentor["id"];
					$upd["id_station"] = $stations["id"];
					$id_lesson = $sess_lessons->add($upd);
				}
				else
					$id_lesson = $id_lessons[0]["id"];
				
				$where = "";
				$where = where($where, "id_lesson", "=", $id_lesson);
				$where = where($where, "AND");
				$where = where($where, "end", "=", "");
				
				$s_scenario = $sess_scenario->getFields(array("*"), $where);
				
				if (count($s_scenario) != 0)
				{
					$upd = "";
					$upd["end"] = time();
					$where = "";
					$where = where($where, "id", "=", $s_scenario[0]["id"]);
					$sess_scenario->update($upd, $where);
				}
				
				$upd = "";
				$upd["id_lesson"] = $id_lesson;
				$upd["id_scenario"] = $id_scenario;
				$upd["start"] = time();
				
				$sess_scenario->add($upd);
				
				$r["id_lesson"] = $id_lesson;
			}
		}
		
		echo json_encode($r);
	}
	
	//получить станцию по hash
	function getStationByHash($hash)
	{
		$stations = new dataTable("stations");
		$where = "";
		$where = where($where, "hash", "=", $hash);
		
		$result = $stations->getFields(array("*"), $where);
		
		if (count($result) == 0)
			$result[0] = null;
		
		return $result[0];
	}
	
	//получить наставника по token
	function getMentorByToken($token)
	{
		$mentor= new dataTable("mentor");
		$where = "";
		$where = where($where, "token", "=", $token);
		
		$result = $mentor->getFields(array("*"), $where);
		
		if (count($result) == 0)
			$result[0] = null;
			
		return $result[0];
	}
	
	//закончить занятие
	//Входные параметры:
	//	$id - занятия
	//Возвращает статус
	//Ошибки: 
	function endLesson($id)
	{
		$r["status"] = "ok";
		
		$sess_lessons = new dataTable("sess_lessons");
		$upd["end"] = time();
		$where = "";
		$where = where($where, "id", "=", $id);
		$sess_lessons->update($upd, $where);
		
		$sess_scenario = new dataTable("sess_scenario");
		$where = "";
		$where = where($where, "id_lesson", "=", $id);
		$where = where($where, "AND");
		$where = where($where, "end", "=", "");
		$sess_scenario->update($upd, $where);
		
		echo json_encode($r);
	}
	
	//выход детей из станции
	//Входные параметры:
	//	$hash - хеш станции
	//  $token - token станции
	//Возвращает статус
	//Ошибки: 
	function exitKidsStation($hash, $token)
	{
		if ($hash == "")
		{
			$r = setError(103);
			
			echo json_encode($r);
			exit;
		}
		
		if ($token == "")
		{
			$r = setError(206);
			
			echo json_encode($r);
			exit;
		}
		
		$stations = new dataTable("stations");
		$where = "";
		$where = where($where, "hash", "=", $hash);
		$id_station = $stations->getFields(array("id"), $where);
		
		if (count($id_station) == 0)
			$r = setError(101);
		else
		{
			$r["status"] = "ok";
			
			$id_station = $id_station[0]["id"];
			$kids = new dataTable("kids");
			$where = "";
			$where = where($where, "id_station", "=", $id_station);
			$r_kids = $kids->getFields(array("*"), $where);
			
			$station = getStationByHash($hash);
			$mentor = getMentorByToken($token);
			
			$sess_kids = new dataTable("sess_kids");
			
			foreach ($r_kids as $val)
			{
				$upd = "";
				$upd["id_kids"] = $val["id"];
				$upd["id_station"] = $station["id"];
				$upd["id_mentor"] = $mentor["id"];
				$upd["action"] = 2;
				$upd["date"] = time();
				$sess_kids->add($upd);
				
				$upd = "";
				$upd["money"] = $val["money"] + $station["price_increment"];
				$upd["id_station"] = "";
				$where = "";
				$where = where($where, "id", "=", $val["id"]);
				$kids->update($upd, $where);
			}
		}
		
		echo json_encode($r);
	}
?>