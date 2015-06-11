<?
	global $error;
	
	$error = array(
		101 => "this station does not exist", //не существует данная станция
		102 => "this station is already authorized by", //данная станция уже авторизирована
		103 => "missing or empty hash", //отсутствует hash или пустой
		201 => "wrong pair of login and password", //неправильный логин или пароль
		202 => "the instructor is already authorized", //данный наставник уже авторизован
		203 => "no login", //отсутствует логин
		204 => "no password", //отсутствует пароль
		205 => "no user(token)", //остутствует данный пользователь по token
		206 => "missing or empty token", //остутствует или пустой token
		301 => "no qr code", //отсутствует qr code
		302 => "a child with this qr code does not exist", //ребенка с данным qr кодом не существует
		303 => "insufficient funds", //недостаточно средств у ребенка для входа на станцию
	);
?>