<?
	require_once "class/dataTable.class.php";
	define("GOOGLE_API_KEY", "AIzaSyCDdQThRcpvp8KzFKF3ktFL32K_8cZAHT0"); 
	
	//Получить список всех станций.
	//Возвращает - name, id
	function getStationList()
	{
		$stat = new dataTable("stations");
		$city = new dataTable("city");
		$stations = $stat->getFields(array("id", "name", "id_city"));
		$r_city = $city->getFields(array("*"));
		
		foreach ($r_city as $val)
		{
			$r["id"] = $val["id"];
			$r["name"] = $val["name"];
			foreach ($stations as $v)
			{
				$r_s["id"] = $v["id"];
				$r_s["name"] = $v["name"];
				
				$r["stations"][] = $r_s;
			}
			
			$res[] = $r;
		}
		
		$result["status"] = "ok";
		$result["cities"] = $res;
		echo json_encode($result);
	}
	
	//Получить список всех городов.
	//Возвращает - name, id
	function getCities()
	{
		$city = new dataTable("city");
		$r_city = $city->getFields(array("id", "name"));
		
		
		$result["status"] = "ok";
		$result["result"] = $r_city;
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
	
	function stationAuth($login, $password, $hash, $reg_id)
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
		elseif($reg_id == "")
			$r = setError(401);
		elseif (count($stations) == 0)
			$r = setError(101);
		else
		{
			$r["status"] = "ok";
			
			$token = md5($r_mentor[0]["id"] + time());
			$r["token"] = $token;
			
			$upd = "";
			$upd["token"] = $token;
			$upd["reg_id"] = $reg_id;
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
	
	//авторизация таможенника
	//Входные параметры:
	//	$login - логин наставника
	//	$password - пароль наставника
	//	$id - id города
	//Возвращает token
	//Ошибки: 
	
	function customAuth($login, $password, $id)
	{
		$customs = new dataTable("customs");
		
		$where = "";
		$where = where($where, "login", "=", $login);
		$where = where($where, "AND");
		$where = where($where, "password", "=", md5($password));
		
		$r_custom = $customs->getFields(array("*"), $where);
		
		$where = "";
		$city = new dataTable("city");
		$where = where($where, "id", "=", $id);
		$r_city = $city->getFields(array("*"), $where);
		
		if (count($r_custom) == 0)
			$r = setError(201);
		elseif ($r_custom[0]["token"] != "")
			$r = setError(208);
		elseif($login == "")
			$r = setError(203);
		elseif($password == "")
			$r = setError(204);
		elseif($id == "")
			$r = setError(209);
		elseif (count($r_city) == 0)
			$r = setError(150);
		else
		{
			$r["status"] = "ok";
			
			$token = md5($r_custom[0]["id"] + time());
			$r["token"] = $token;
			
			$upd = "";
			$upd["token"] = $token;
			$upd["id_city"] = $id;
			$where = "";
			$where = where($where, "id", "=", $r_custom[0]["id"]);
			$custom->update($upd, $where);
			
			$upd = "";
			$upd["id_city"] = $id;
			$upd["id_custom"] = $r_custom[0]["id"];
			$upd["input"] = time();
			$upd["token"] = $token;
			
			$sess_custom = new dataTable("sess_custom");
			$sess_custom->add($upd);
		
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
	
	//выход таможенника из города
	//Входные параметры:
	//	$token - token таможенника
	//Возвращает status
	//Ошибки: 
	function logoutCustom($token)
	{	
		if ($token == "")
		{
			$r = setError(206);
			
			echo json_encode($r);
			exit;
		}
		
		$custom = new dataTable("customs");
		$where = "";
		$where = where($where, "token", "=", $token);
		$r_custom = $custom->getFields(array("*"), $where);
		
		if (count($r_custom) == 0)
			$r = setError(205);
		else
		{
			$r["status"] = "ok";
			$id_custom = $r_custom[0]["id"];
			
			$upd["token"] = "";
			$where = "";
			$where = where($where, "id", "=", $id_custom);
			$custom->update($upd, $where);
			
			$sess_custom = new dataTable("sess_customs");
			$upd = "";
			$upd["exit"] = time();
			$where = "";
			$where = where($where, "token", "=", $token);
			$sess_custom->update($upd, $where);
		}
		
		echo json_encode($r);
	}
	
	//вход ребенка на станцию
	//Входные параметры:
	//	$qr - qr код ребенка
	//	$token - token наставника
	//	$hash - хеш станции 
	//Возвращает status
	//Ошибки: 206, 103, 301, 302, 205, 101, 303, 305
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
		elseif ($r_kids[0]["id_station"] != "")
			$r = setError(305);
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
	//Ошибки: 103, 206, 101, 205, 401
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
	
	//получить ребенка по id
	function getChild($id)
	{
		$kids = new dataTable("kids");
		$where = "";
		$where = where($where, "id", "=", $id);
		
		$result = $kids->getFields(array("*"), $where);
		
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
	//Ошибки: 103, 206, 101
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
	
	//выход ребенка из станции без начисления опытов 
	//Входные параметры:
	//  $id - id ребенка
	//	$hash - хеш станции
	//  $token - token станции
	//Возвращает статус
	//Ошибки: 103, 206, 304
	function extСhildrenStationNoMoney($id, $hash, $token)
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
		
		if ($id == "")
		{
			$r = setError(304);
			
			echo json_encode($r);
			exit;
		}
		
		$child = getChild($id);
		
		if (($child) and ($child["id_station"] != 0))
		{
			$r["status"] = "ok";
			
			$kids = new dataTable("kids");
			$where = "";
			$where = where($where, "id", "=", $id);
			$upd["id_station"] = 0;
			$kids->update($upd, $where);
			
			$upd = "";
			$upd["id_kid"] = $id;
			
			$stat = getStationByHash($hash);
			$upd["id_station"] = $stat["id"];
			
			$mentor = getMentorByToken($token);
			$upd["id_mentor"] = $mentor["id"];
			
			$upd["action"] = 3;
			$upd["date"] = time();
			
			$sess_kids = new dataTable("sess_kids");
			$sess_kids->add($upd);
		}
		
		echo json_encode($r);
	}
	
	//авторизация старшего наставника
	//Входные параметры:
	//	$login - логин наставника
	//	$password - пароль наставника
	//	$hash - hash станции
	//Возвращает token
	//Ошибки: 201, 202, 203, 204, 103, 101
	
	function stationAuthSeniorMentor($login, $password)
	{
		$mentor = new dataTable("senior_mentor");
		
		$where = "";
		$where = where($where, "login", "=", $login);
		$where = where($where, "AND");
		$where = where($where, "password", "=", md5($password));
		
		$r_mentor = $mentor->getFields(array("*"), $where);
		
		$where = "";
	
		if (count($r_mentor) == 0)
			$r = setError(201);
		elseif ($r_mentor[0]["token"] != "")
			$r = setError(202);
		elseif($login == "")
			$r = setError(203);
		elseif($password == "")
			$r = setError(204);
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
		}
		
		echo json_encode($r);
	}
	
	//выход старшего наставника из станции
	//Входные параметры:
	//	$token - token наставника
	//Возвращает status
	//Ошибки: 205, 206
	function logoutSeniorMentor($token)
	{
		if ($token == "")
		{
			$r = setError(206);
			
			echo json_encode($r);
			exit;
		}
		
		$mentor = new dataTable("senior_mentor");
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
		}
		
		echo json_encode($r);
	}
	
	
	//Получить список всех станций старшим наставником.
	//Входные параметры:
	//$token - token наставника
	//Возвращает - *
	//Ошибки: 205, 206
	function getSeniorMentorStationList($token)
	{
		if ($token == "")
		{
			$r = setError(206);
			
			echo json_encode($r);
			exit;
		}
		
		$mentor = new dataTable("senior_mentor");
		$where = "";
		$where = where($where, "token", "=", $token);
		$r_mentor = $mentor->getFields(array("*"), $where);
		
		if (count($r_mentor) == 0)
			$r = setError(205);
		else
		{
			$stat = new dataTable("stations");
		
			$stations = $stat->getFields(array("*"));
			$result["status"] = "ok";
			$result["result"] = $stations;
		}
		
		echo json_encode($result);
	}
	
	
	//отправка push уведомления
	function send_notification($registatoin_ids, $message) 
	{
        
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
 
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
 
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
        echo $result;
    }
	
	//вход ребенка в город
	//Входные параметры:
	//	$qr - qr код ребенка
	//	$id_city - id города
	//Возвращает status
	//Ошибки: 301, 150
	function enterKidsCity($id_city, $qr)
	{	
		if ($qr == "")
		{
			$r = setError(301);
			 
			echo json_encode($r);
			exit;
		}
		
		if ($id_city == "")
		{
			$r = setError(150);
			
			echo json_encode($r);
			exit;
		}
		
		$kids = new dataTable("kids");
		$where = "";
		$where = where($where, "qr", "=", $qr);
		$r_kids = $kids->getFields(array("*"), $where);
		
		$city = new dataTable("city");
		$where = "";
		$where = where($where, "id", "=", $id_city);
		$r_city = $city->getFields(array("*"), $where);
		
		
		if (count($r_kids) == 0)
			$r = setError(302);
		elseif (count($r_city) == 0)
			$r = setError(150);
		else
		{
			$r["status"] = "ok";
			
			$upd = "";
			$upd["id_city"] = $id_city;
			
			$where = "";
			$where = where($where, "id", "=", $r_kids[0]["id"]);
			$kids->update($upd, $where);
			
			$sess_city = new dataTable("sess_city");
			
			$upd = "";
			$upd["id_kids"] = $r_kids[0]["id"];
			$upd["id_city"] = $id_city;
			$upd["input"] = time();
			$sess_city->add($upd);
		}
		
		echo json_encode($r);
	}
	
	//выход ребенка из станции
	//Входные параметры:
	//	$qr - qr код ребенка
	//Возвращает status
	//Ошибки: 301, 302, 305, 306
	
	function exitKidsCity($qr)
	{
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
		
		if (count($r_kids) == 0)
			$r = setError(302);
		else
		{
			$sess_city = new dataTable("sess_city");
			$where = "";
			$where = where($where, "id_kids", "=", $r_kids[0]["id"]);
			$where = where($where, "AND");
			$where = where($where, "exit", "=", "");
			$res = $sess_city->getFields(array("id"), $where);
			
			if (count($res) == 0)
				$r = setError(305);
			elseif (count($res) > 1)
				$r = setError(306);
			else
			{
				$r["status"] = "ok";
				$upd = "";
				$upd["exit"] = time();
				$where = "";
				$where = where($where, "id", "=", $res[0]["id"]);
				$sess_city->update($upd, $where);
				
				$upd = "";
				$upd["id_city"] = "";
				
				$where = "";
				$where = where($where, "id", "=", $r_kids[0]["id"]);
				$kids->update($upd, $where);
			}
		}
		
		echo json_encode($r);
	}
	
	//валидация
	function valid($val)
	{
		if (isset($val))
			$r = htmlspecialchars($val);
		else
			$r = "";
		
		return $r;
	}
	
	//получить всех детей в данном городе
	//Входные параметры:
	//	$id - id города
	//Возвращает * массив из детей
	//Ошибки: 150
	function getKidsOnIDCity($id)
	{
		if ($id == "")
		{
			$r = setError(150);
			 
			echo json_encode($r);
			exit;
		}
		
		$kids = new dataTable("kids");
		$where = "";
		$where = where($where, "id_city", "=", $id);
		$res = $kids->getFields(array("*"), $where);
		
		$r["status"] = "ok";
		$r["result"] = $res;
		
		echo json_encode($r);
	}
	
	//получить всех наставников в данном городе
	//Входные параметры:
	//	$id - id города
	//Возвращает * массив из наставников
	//Ошибки: 150
	function getMentorOnIDCity($id)
	{
		if ($id == "")
		{
			$r = setError(150);
			 
			echo json_encode($r);
			exit;
		}
		
		$mentor = new dataTable("mentor");
		$where = "";
		$where = where($where, "id_city", "=", $id);
		$res = $mentor->getFields(array("*"), $where);
		
		$r["status"] = "ok";
		$r["result"] = $res;
		
		echo json_encode($r);
	}
	
	//установить расписание для наставника
	//Входные параметры:
	//	$id_mentor - id наставника
	//  $id_station - id станции
	//  $date - дата
	//  $change - смена
	//Возвращает статус
	//Ошибки: 207, 101, 402
	function setTimetable($id_mentor, $id_station, $date, $change)
	{
		if ($id_mentor == "")
		{
			$r = setError(207);
			 
			echo json_encode($r);
			exit;
		}
		
		if ($id_station == "")
		{
			$r = setError(101);
			 
			echo json_encode($r);
			exit;
		}
		
		if ($date == "")
		{
			$r = setError(402);
			 
			echo json_encode($r);
			exit;
		}
		
		if ($change == "")
		{
			$r = setError(402);
			 
			echo json_encode($r);
			exit;
		}
		
		$timetable = new dataTable("timetable");
		$upd["id_mentor"] = $id_mentor;
		$upd["id_station"] = $id_station;
		$upd["date"] = strtotime($date);
		$upd["change"] = $change;
		
		$timetable->add($upd);
		$r["status"] = "ok";
		
		echo json_encode($r);
	}
	
	//получить по token расписание для города
	//Входные параметры:
	//	$token - token старшего наставника
	//  $start - начальная цифра с которой нужно показывать
	//  $offset - конечная цифра до которой нужно показывать
	//Возвращает статус
	//Ошибки: 205, 402
	function getTimetable($token, $start, $offset)
	{
		if ($token == "") 
		{
			$r = setError(402);
			 
			echo json_encode($r);
			exit;
		}
		
		if ($start == "") 
		{
			$r = setError(402);
			 
			echo json_encode($r);
			exit;
		}
		
		if ($offset == "") 
		{
			$r = setError(402);
			 
			echo json_encode($r);
			exit;
		}
		
		$senior_mentor = new dataTable("senior_mentor");
		$where = where($where, "token", "=", $token);
		$res = $senior_mentor->getFields(array("*"), $where);
		
		if (count($res) == 0)
			$r = setError(205);
		else
		{
			$id_city = $res[0]["id_city"];
			$stat = new dataTable("stations");
			$where = "";
			$where = where($where, "id_city", "=", $id_city);
			$stations = $stat->getFields(array("id"), $where);
			$r_stations = $stat->getFields(array("*"), $where);
			foreach ($r_stations as $val)
				$dt_station[$val["id"]] = $val;
			
			$mentor = new dataTable("mentor");
			$where = "";
			$where = where($where, "id_city", "=", $id_city);
			$r_mentor = $mentor->getFields(array("*"), $where);
			foreach ($r_mentor as $val)
				$dt_mentor[$val["id"]] = $val;
			
			$timetable = new dataTable("timetable");
			$limit[0] = $start;
			$limit[1] = $offset;
			$where = "";
			$where = where($where, "id_station", "IN", $stations);
			$r_timetable = $timetable->getFields(array("*"), $where, "", $limit);
			
			$i = 0;
			foreach ($r_timetable as $val)
			{
				$result[$i]["date"] = $val["date"];
				$result[$i]["change"] = $val["change"];
				$result[$i]["mentor"] = $dt_mentor[$val["id_mentor"]];
				$result[$i]["station"] = $dt_station[$val["id_station"]];
				
				$i++;
			}
			
			$r["status"] = "ok";
			$r["result"] = $result;
		}
		
		echo json_encode($r);
	}
	
	
?>