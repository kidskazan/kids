<?php
require_once "config.php";

class DataBase {

  private static $db = null; // Единственный экземпляр класса, чтобы не создавать множество подключений
  private $mysqli; // Идентификатор соединения
  private $sym_query = "/\{\?\}/"; // "Символ значения в запросе"

  /* Получение экземпляра класса. Если он уже существует, то возвращается, если его не было, то создаётся и возвращается (паттерн Singleton) */
  public static function getDB() {
    if (self::$db == null) self::$db = new DataBase();
    return self::$db;
  }

  /* private-конструктор, подключающийся к базе данных, устанавливающий локаль и кодировку соединения */
  private function __construct() {
	$config = new Config();
	$this->mysqli = new mysqli($config->host, $config->user, $config->pass, $config->db);
    $this->mysqli->query("SET lc_time_names = 'ru_RU'");
    $this->mysqli->query("SET NAMES 'utf8'");
  }

  /* Вспомогательный метод, который заменяет "символ значения в запросе" на конкретное значение, которое проходит через "функции безопасности" */
  private function getQuery($query, $params) {
	if ($params) {
      for ($i = 0; $i < count($params); $i++) {
		$pos = strpos($query, $this->sym_query);
        $arg = "'".$this->mysqli->real_escape_string($params[$i])."'";
        $query = preg_replace($this->sym_query, $arg, $query,1);
    }
    }
	//echo $query."\r\n";
	//print_r($params);
    return $query;
  }

  /* SELECT-метод, возвращающий таблицу результатов */
  public function select($query, $params = false) {
    $result_set = $this->mysqli->query($this->getQuery($query, $params));
    if (!$result_set) return false;
    return $this->resultSetToArray($result_set);
  }
  
  public function dataSelect($table, $fields, $where = false, $order = false, $limit = false)
  {
	$params = false;
	
	$query = "SELECT ";
	
	foreach ($fields as $v)
	{	
		if (($v != '*') AND (!stripos($v, "(")) )
			$query .= "`".$v."`,";
		else
			$query .= $v.",";
	}
			
	$query = substr($query, 0, -1);
	
	$query .= " FROM ".$table." ";
	
	if ($where != false)
	{
		$query .= "WHERE ".$where["query"];
	}
	
	if ($order != false)
	{
		$query .= " ORDER BY ";
		foreach($order as $val)
			$query .= $val.",";
			
		$query = substr($query, 0, -1);
	}
	
	if ($limit != false)
	{
		$query .= " LIMIT ".$limit[0]." ";
		if (isset($limit[1]))
			$query .= "OFFSET ".$limit[1]." ";
	}
	//echo $query."\r\n";
	return $this->select($query, $where["params"]);
  }
  
  /*
  private function getWhere($where, &$params)
  {
	$query = "";
	foreach ($where as $key => $val)
	{
		if ((!is_int($key)) and (!is_array($val["IN"])) and (!is_array($val[0])))
		{
			if ($val["esk"] == true)
				$query .= $key." ".$val[0]." {?}";
			else
				$query .= "`".$key."` ".$val[0]." {?}";
			
			$params[] = $val[1];
		}
		elseif(is_array($val["IN"]))
		{
			$query .= "`".$key."` IN (";
			foreach ($val["IN"] as $v)
			{
				$query .= "{?},";
				$params[] = $v;
			}
			$query = substr($query, 0, -1);
			$query .= ") ";
		}
		elseif (is_array($val[0]))
		{
			foreach ($val as $v)
			{	
				$w = "";
				if (is_array($v))
					$w[$key] = $v;
				else
					$w[]=$v;
				$query .= $this->getWhere($w, $params);
			}
		}
		else
		{
			$query .= " ".$val." ";
		}
	}
		
	return $query;
  }
  */
 

  /* SELECT-метод, возвращающий одну строку с результатом */
  public function selectRow($query, $params = false) {
    $result_set = $this->mysqli->query($this->getQuery($query, $params));
    if ($result_set->num_rows != 1) return false;
    else return $result_set->fetch_assoc();
  }

  /* SELECT-метод, возвращающий значение из конкретной ячейки */
  public function selectCell($query, $params = false) {
    $result_set = $this->mysqli->query($this->getQuery($query, $params));
    if ((!$result_set) || ($result_set->num_rows != 1)) return false;
    else {
      $arr = array_values($result_set->fetch_assoc());
      return $arr[0];
    }
  }

  /* НЕ-SELECT методы (INSERT, UPDATE, DELETE). Если запрос INSERT, то возвращается id последней вставленной записи */
  public function query($query, $params = false) {
    $success = $this->mysqli->query($this->getQuery($query, $params));
    if ($success) {
      if ($this->mysqli->insert_id === 0) return true;
      else return $this->mysqli->insert_id;
    }
    else return false;
  }
  
  public function insert($table, $upd)
  {
	if (count($upd) == 0)
		return false;
		
	$query = "INSERT INTO `".$table."` (";
	foreach ($upd as $k=>$v)
		$query .= "`".$k."`,";
	
	$query = substr($query, 0, -1);
	
	$query .= ") VALUES (";
	foreach ($upd as $k=>$v)
	{
		$query .= "{?},";
		$params[] = $v;
	}
	
	$query = substr($query, 0, -1);
	$query .= ")";
	
	return $this->query($query, $params);
  }
  
  public function update($table, $upd, $where)
  {
	if (count($upd) == 0)
		return false;
		
	$query = "UPDATE `".$table."` SET ";
	foreach ($upd as $k=>$v)
	{
		$query .= "`".$k."` = {?},";
		$params[] = $v;
	}
	$query = substr($query, 0, -1);
	
	if ($where["query"] != "")
	{
		$query .= " WHERE ";
		
		$query .= $where["query"];
		foreach ($where["params"] as $val)
			$params[] = $val;
	}

	return $this->query($query, $params);
  }
  
  public function delete($table, $where)
	{
		$query = "DELETE FROM `".$table."` WHERE ";
		
		$query .= $where["query"];
		
		return $this->query($query, $where["params"]);
		
	}

  /* Преобразование result_set в двумерный массив */
  private function resultSetToArray($result_set) {
    $array = array();
    while (($row = $result_set->fetch_assoc()) != false) {
      $array[] = $row;
    }
    return $array;
  }

  /* При уничтожении объекта закрывается соединение с базой данных */
  public function __destruct() {
    if ($this->mysqli) $this->mysqli->close();
  }
  
  
  #Показать все таблицы текущей базы
  public function showAllTables()
  {
	  $query = "SHOW TABLES;";
	  
	  $row = $this->mysqli->query($query);
	  $result = $this->resultSetToArray($row);
	  
	  foreach($result as $val)
	  {
		  foreach($val as $v)
			$dt[] = $v;
		  
	  }
	  
	  return $dt;
  }
  
  #Создание пустой таблицы с Авто Инкремнтом "id"
  public function createTable($name)
  {
	  
	  $query = "CREATE TABLE `" . $name . "`(
	  `id` INT NOT NULL AUTO_INCREMENT,
	  PRIMARY KEY(`id`))";
	  
	  $this->query($query);
  }
  
  #Удаление таблицы
  public function deleteTable($name)
  {
	  $query = "DROP TABLE `". $name ."`;";
	  
	  $this->query($query);
  }
  
  #Показывает поля таблицы и их характеристики
  public function describe($table, $field = false)
  {
	  $query = "DESCRIBE ". $table .";";
	  
	  if(isset($field))
	  {
		  $query = substr($query, 0, -1);
		  $query .= " `". $field ."`;";
	  }
	  
	  $row = $this->mysqli->query($query);
	  $result = $this->resultSetToArray($row);
	  
	  return $result;
  }
  
  #Изменяет поле в таблице
  public function alterChange($table, $name, $newname, $param, $default = NULL, $auto_increment = 0)
  { 
	  $query = "ALTER TABLE `". $table ."` 
	  CHANGE `". $name ."` `". $newname ."` ". $param;
	  
	  if($default != NULL)
		  $query .= " DEFAULT '". $default ."' ";
	  
	  if($auto_increment != 0)
	  {
		  $query = substr($query, 0, -1);
		  $query .= " AUTO_INCREMENT;";
	  }
	  
	  $this->query($query);
  }
  
  #Добавляет поле в таблицу
  public function alterAdd($table, $name, $param, $default = NULL, $after = NULL, $auto_increment = 0)
  { 
	  $query = "ALTER TABLE `". $table ."` 
	  ADD `". $name ."` ". $param;
	  
	  if($default != NULL)
		  $query .= " DEFAULT '". $default ."' ";
	  
	  if($after == NULL)
		  $query .= ";";
	  else
		$query .= " AFTER ". $after .";";
	
	  if($auto_increment != 0)
	  {
		  $query = substr($query, 0, -1);
		  $query .= " AUTO_INCREMENT;";
	  }
	  
	  $this->query($query);
  }
  
  #Удаляет поле из таблицы
  public function alterDel($table, $name)
  {
	  $query = "ALTER TABLE `". $table ."` 
	  DROP `". $name ."`;";
	  
	  $this->query($query);
  }
}
?>