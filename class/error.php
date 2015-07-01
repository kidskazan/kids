<?
	global $error;
	
	$error = array(
		101 => "this station does not exist", //не существует данная станция
		102 => "this station is already authorized by", //данная станция уже авторизирована
		103 => "missing or empty hash", //отсутствует hash или пустой
		150 => "id the city does not exist or is empty", //id города не существует или пустой
		201 => "wrong pair of login and password", //неправильный логин или пароль
		202 => "the instructor is already authorized", //данный наставник уже авторизован
		203 => "no login", //отсутствует логин
		204 => "no password", //отсутствует пароль
		205 => "no user(token)", //остутствует данный пользователь по token
		206 => "missing or empty token", //остутствует или пустой token
		207 => "id of the mentor does not exist or is empty", //данного id наставника не существует или пустой
		208 => "the custom is already authorized", //данный таможенник уже авторизован
		209 => "id of the custom does not exist or is empty", //данного id таможенника не существует или пустой
		210 => "Users of this type does not exist", //данного типа пользователя не существует
		301 => "no qr code", //отсутствует qr code
		302 => "a child with this qr code does not exist", //ребенка с данным qr кодом не существует
		303 => "insufficient funds", //недостаточно средств у ребенка для входа на станцию
		304 => "no child(id)", //данного ребенка не существует по id
		305 => "the child already at the station", //ребенок уже на станции
		307 => "the child is not in the city", //данного ребенка нет в городе
		306 => "bug database (more than one records of the child in the city)", //ошибка базы (больше одной записи о ребенке в городе)
		401 => "missing or empty reg_id", //отсутствует reg_id или пустой
		402 => "Let one of the parameters"//пуст один из параметров
	);
?>