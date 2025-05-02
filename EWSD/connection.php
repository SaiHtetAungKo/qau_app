<?php 
	class Connect {
		private $connection;
		public function __construct() {
			$this->connection = mysqli_connect('localhost', 'root', 'password', 'qa_new');
			if (!$this->connection) {
				die("Connection failed: " . mysqli_connect_error());
			}
		}	
		public function getConnection()
		{
			return $this->connection;
		}	
	}
?>
