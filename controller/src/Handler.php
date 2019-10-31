<?php
	namespace App\Controller;
	//класс для связывания Logic, Database и User
	class Handler {
		public $logic = null;
		public $user  = null;
		public $renderT = null;
		public $last_error = "";
		
		private $db      = null;
		private $enviro  = null;
		private $client  = null;
		
		public function __construct() {
			$this->enviro  = new \App\Model\Environment();
			//$this->db      = new \App\Model\DataBase();
			$this->logic   = new \App\Controller\Logic();
			$this->user    = new \App\Controller\User();
			$this->renderT = new \App\Controller\Render([]);
			
			//$this->logic->setdb($this->db);
			//$this->user->setdb($this->db);
			$this->logic->setUser($this->user);
		}
		
		public function render($data = []) {
			$this->renderT = new \App\Controller\Render($data);
			$this->renderT->twigRender();
		}
		
		public function utopia_unit() {
			$this->client = new \App\Model\UtopiaClient();
		}
		
		public function get_last_blocks(): array {
			$response = $this->client->getBlocks(0, 1);
			if(!isset($response['resultExtraInfo']) || !isset($response['resultExtraInfo']['total'])) {
				return [];
			}
			
			$blocks_count = $response['resultExtraInfo']['total'];
			
			$response = $this->client->getBlocks($blocks_count, 20, true);
			$data = $response['result'];
			//add "total"
			$data[0]['blocks'] = $response['resultExtraInfo']['total'];
			return $data;
		}
		
		public function get_block($block_index = 1): array {
			$response = $this->client->getBlocks($block_index, 1);
			//exit(json_encode($response['result']));
			if(!isset($response['result']) || !isset($response['result'][0])) {
				return [];
			} else {
				$block_data = $response['result'][0];
				$block_data['hex'] = $this->client->blockID2HEX($block_data['id']);
				$block_data['full_amount'] = \App\Model\Utilities::format_amount($block_data['price'] * $block_data['numberMiniers']);
				return $block_data;
			}
		}
		
		public function get_summary(): array {
			$response = $this->client->getSummary();
			if(isset($response['result']['summary'])) {
				return $response['result']['summary'];
			} else {
				return [];
			}
		}
		
		public function get_transactions(): array {
			return $this->client->getTreasury(0, 20);
		}
		
		public function get_transactions_raw(): array {
			return $this->client->getTreasuryRaw();
		}
		
		public function get_stats(): array {
			return $this->client->getStats();
		}
		
		public function get_owner_info($pubkey = ""): array {
			return $this->client->getOwnerInfo($pubkey);
		}
		
		public function get_uns($record_name = ""): array {
			return $this->client->unsGET($record_name);
		}
		
		public function get_channel($channel_id = ""): array {
			return $this->client->getChannelInfo($channel_id);
		}
	}
