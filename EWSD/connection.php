<?php 
	class Connect {
		private $connection;
		public function __construct() {
			$this->connection = mysqli_connect('localhost', 'root', '', 'quality_assurance');
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
