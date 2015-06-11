<?
	require_once "DataBase.class.php";
	require_once "func.php";
	
	class dataTable
	{
		private $db;
		private $name;
		
		public function __construct($name)
		{
			global $DB;
			$this->name = $name;
			$this->db = $DB;
		}
		
		public function getAll()
		{
			$query = "SELECT * FROM ".$this->name;
			$dt = $this->db->select($query);
			return $dt;
		}
		
		public function getFieldsOnID($fields, $id)
		{
			$where = where($where, "id", "=", $id);
			$dt = $this->db->dataSelect($this->name, $fields, $where);
			
			return $dt[0];
		}
		
		public function getFieldOnID($field, $id)
		{
			$res = $this->getFieldsOnID(array($field), $id);
			return $res[$field];
		}
		
		public function getFields($fields, $where = false, $order = false, $limit = false)
		{
			return $this->db->dataSelect($this->name, $fields, $where, $order, $limit);
		}
		
		
		public function getCount($where = false)
		{
			$res = $this->getFields(array("COUNT(*) as count"), $where);
			return $res[0]["count"];
		}
		
		public function getIDOnField($field, $val)
		{
			$where =  where($where, $field, "=", $val);
			$res = $this->getFields(array("id"), $where);
			return $res[0]["id"];
		}
		
		public function add($upd)
		{
			return $this->db->insert($this->name, $upd);
		}
		
		public function update($upd, $where)
		{
			return $this->db->update($this->name, $upd, $where);
		}
		
		public function delete($where)
		{
			return $this->db->delete($this->name, $where);
		}
		
		public function getMaxID($where = false)
		{
			$res = $this->getFields(array("MAX(`id`) as max_id"), $where);
			return $res[0]["max_id"];
		}
				
		public function showAllTables()
		{
			return $this->db->showAllTables();
		}
		
		public function createTable()
		{
			$this->db->createTable($this->name);
		}
		
		public function deleteTable()
		{
			$this->db->deleteTable($this->name);
		}
		
		public function describe($field = false)
		{
			$mas = $this->db->describe($this->name, $field);
			return $mas;
		}
		
		private function f_type($f_type, $length, $link = false)
		{
			if($f_type == 2 || $link == "int")
			{
				$param = " INT(";
				if($length == NULL)
					$param .= "11";
				else
					$param .= $length;
				$param .= ") ";
			}
			else if($f_type == 3)
			{
				$param = " VARCHAR(";
				if($length == NULL)
					$param .= "10";
				else
					$param .= $length;
				$param .= ") COLLATE 'utf8_general_ci' ";
			}
			else if($f_type == 4)
				$param = " text COLLATE 'utf8_general_ci' ";
			else
			{
				$param = " VARCHAR(";
				if($length == NULL)
					$param .= "255";
				else
					$param .= $length;
				$param .= ") COLLATE 'utf8_general_ci' ";
			}
			return $param;
		}
		
		public function alterChange($old_name, $post, $link = false)
		{
			$param = $this->f_type($post["f_type"], $post["length"], $link);
			
			$this->db->alterChange($this->name, $old_name, $post["f_name"], $param, $post["default"], $post["auto_increment"]);
		}
		
		public function alterAdd($post, $link = false)
		{
			$param = $this->f_type($post["f_type"], $post["length"], $link);
			
			$this->db->alterAdd($this->name, $post["f_name"], $param, $post["default"]);
		}
		
		public function alterDel($field)
		{
			$this->db->alterDel($this->name, $field);
		}
	}
?>