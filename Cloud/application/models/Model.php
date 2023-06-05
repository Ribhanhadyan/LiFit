<?php
class Model extends CI_Model
{

	function getAll($id)
	{
		$q = $this->db_kedua->query("select * from Users where id='$id'");
		return $q->result();
	}
	
}
