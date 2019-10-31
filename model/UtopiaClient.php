<?php
	namespace App\Model;
	
	class UtopiaClient {
		public $api_version = "1.0";
		protected $credentials = [];
		protected $client = null; //HttpClient
		
		public function __construct() {
			$this->credentials = [
				'token' => getenv('api_token'),
				'host'  => getenv('api_host'),
				'port'  => getenv('api_port')
			];
			$this->client = new \App\Model\HttpClient();
		}
		
		function api_query($method = "", $params = [], $filter = []) {
			$post_fields = [
				'method' => $method,
				'params' => $params,
				'filter' => $filter,
				'token'  => $this->credentials['token']
			];
			
			$json = $this->client->query(
				"http://" . $this->credentials['host'] . ":" . $this->credentials['port'] . "/api/" . $this->api_version,
				$post_fields
			);
			
			$response = \App\Model\Utilities::json2Arr($json);
			if(!isset($response['result'])) {
				$response['result'] = [];
			}
			
			return $response;
		}
		
		public function getSummary($fromDate = "", $toDate = "", $filter = []) {
			$params = [
				'fromDate' => $fromDate,
				'toDate'   => $toDate
			];
			return $this->api_query("summaryUnsRegisteredNames", $params, $filter);
		}
		
		public function blockID2HEX($dec = 1): string {
			return str_pad(dechex($dec), 8, '0', STR_PAD_LEFT);
		}
		
		public function getBlocks($last_block_n = 0, $limit = 20, $need_convert = false): array {
			if($last_block_n < $limit) {
				$offset = 0;
			} else {
				$offset = $last_block_n - $limit;
			}
			
			$filter = [
				'sortBy' => "",
				"offset" => (string) $offset,
				"limit"  => (string) $limit
			];
			
			$response = $this->api_query("getMiningBlocks", [], $filter);
			if($need_convert) {
				//sort
				$arr = $response['result'];
				usort($arr, function($a, $b){
					return -($a['id'] - $b['id']);
				});
				$response['result'] = $arr;
				//convert dateTime to mysql-timestamp
				for($i = 0; $i < count($response['result']); $i++) {
					$response['result'][$i]['dateTime_raw'] = $response['result'][$i]['dateTime'];
					$response['result'][$i]['dateTime'] = date("Y-m-d H:i:s", strtotime($response['result'][$i]['dateTime']));
					$response['result'][$i]['price'] = number_format($response['result'][$i]['price'], 9, '.', ' ');
					//block index (hex)
					$response['result'][$i]['hex'] = $this->blockID2HEX($response['result'][$i]['id']);
				}
			}
			return $response;
		}
		
		public function getTreasuryRaw(): array {
			$response = $this->api_query("getTreasuryTransactionVolumes");
			exit(json_encode($response));
			//
		}
		
		public function getTreasury($offset = 0, $limit = 20) {
			$filter = [
				'sortBy' => "",
				"offset" => (string) $offset,
				"limit"  => (string) $limit
			];
			$response = $this->api_query("getTreasuryTransactionVolumes", [], $filter);
			if(!isset($response['result'])) {
				return [];
			}
			if(!isset($response['result']['transactions'])) {
				return [];
			}
			$data = [
				'summary'      => $response['result']['summary'],
				'transactions' => []
			];
			//get last $limit entrys
			$entrys_count = count($response['result']['transactions']);
			for($i = $entrys_count-1; $i >= $entrys_count - $limit; $i--) {
				$tr = $response['result']['transactions'][$i];
				$tr['hex'] = $this->blockID2HEX($i);
				$tr['amount'] = number_format($tr['amount'], 9, '.', ' ');
				$data['transactions'][] = $tr;
			}
			return $data;
		}
		
		public function getSystemInfo(): array {
			$response = $this->api_query("getSystemInfo");
			if(!isset($response['result'])) {
				return [
					'uptime'              => '00:00:00',
					'numberOfConnections' => 0
				];
			} else {
				return $response['result'];
			}
		}
		
		public function getFinanceStats(): array {
			$response = $this->api_query("getFinanceSystemInformation");
			if(!isset($response['result'])) {
				return [];
			} else {
				$result = $response['result'];
				
				$result['transferCardFee'] = $result['transferCardFee'] * 100;
				$result['transferExternalFee'] = $result['transferExternalFee'] * 100;
				$result['transferInternalFee'] = $result['transferInternalFee'] * 100;
				$result['vouchersMinAmount'] = number_format($result['vouchersMinAmount'], 9);
				
				return $result;
			}
		}
		
		public function getStats(): array {
			$data_system  = $this->getSystemInfo();
			
			return [
				'uptime'      => $data_system['uptime'],
				'connections' => $data_system['numberOfConnections'],
				'finance'     => $this->getFinanceStats()
			];
		}
		
		public function getOwnerInfo($pubkey = ""): array {
			$params = [
				'owner' => $pubkey
			];
			$response = $this->api_query("getWhoIsInfo", $params);
			//channels
			if(!isset($response['result']['channels'])) {
				$channels_count = 0;
			} else {
				$channels_count = count($response['result']['channels']);
			}
			//uNS
			if(!isset($response['result']['uns'])) {
				$uns_count = 0;
			} else {
				$uns_count = count($response['result']['uns']);
			}
			
			$response['result']['stats'] = [
				'pubkey'         => $pubkey,
				'channels_count' => $channels_count,
				'uns_count'      => $uns_count
			];
			return $response['result'];
		}
		
		public function unsGET($record_name = ""): array {
			$params = [
				'filter' => $record_name
			];
			$response = $this->api_query("unsSearchByNick", $params);
			//exit(json_encode($response));
			if(!isset($response['result']) || !isset($response['result'][0])) {
				return [];
			}
			return $response['result'][0];
		}
		
		public function getChannelInfo($channel_ID = ""): array {
			$params = [
				'channelid' => $channel_ID
			];
			$response = $this->api_query("getChannelInfo", $params);
			$response['result']['channelId'] = $channel_ID;
			//exit(json_encode($response));
			return $response['result'];
		}
		
		/* public function genUCode($hex = "", $size = 128, $coder = "BASE64", $format = "PNG") {
			$params = [
				'hex_code'   => $hex,
				'size_image' => $size,
				'coder'      => $coder,
				'format'     => $format
			];
			$response = $this->api_query("ucodeEncode", $params);
		} */
	}
	