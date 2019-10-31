<?php
	namespace App\Controller;
	
	class Logic {
		public $last_error = "";
		
		private $coin_connection = null;
		private $db = null;
		
		public function __construct() {
			//
		}
		
		public function setdb($db) {
			$this->db = &$db;
		}
		
		public function setUser($user) {
			$this->user = &$user;
		}
		
		public function determineQueryType($data = ""): int {
			if($data == "") {
				$this->last_error = "Empty string passed";
				return 0;
			}
			//public key?
			if(strlen($data) == 64) {
				return 1;
			}
			//block index?
			if(is_numeric($data)) {
				return 2;
			}
			//channel ID?
			if(strlen($data) == 32) {
				return 3;
			}
			//uNS name?
			//TOQ: ??
			return 4;
			//unknown query type
			return -1;
		}
	}
	