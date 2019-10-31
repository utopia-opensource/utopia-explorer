<?php
	namespace App\Model;
	
	class TRIDcode {
		public $code_arr = [];
		//for example
		protected $code_hash =		"5ca17b85ce10702fd5ce8f63a5657905ae8ba41c7b7a886e8d13b317386dda06";
		//первый символ - номер версии
		//остальное пока не используется
		protected $system_info = "100000";
		protected $form = "square";
		
		/**
		* Инициализирует новый TRID код
		* @param string $hash
		*/
		public function __construct($hash = "", $form = "square") {
			//TODO: проверку на hex
			if(empty($hash) || strlen($hash) != 64) {
				throw new \Exception("Invalid hash");
			} else {
				//самый простой вариант
				$this->form = $form;
				$this->code_hash = $this->system_info . $hash . "ff";
				$this->regenerate();
			}
		}
		
		/**
		* Конвертирует hex-представление цвета в rgb
		* @param string $hex
		* @return array
		*/
		function hex2rgb($hex) {
			list($r, $g, $b) = sscanf($hex, "%02x%02x%02x");
			return [$r, $g, $b];
		}
		
		/**
		* Генерирует код и создает представление в виде массива
		*/
		public function regenerate() {
			if(strlen($this->code_hash) < 64 && strlen($this->code_hash) > 0) {
				$this->code_hash = str_pad($this->code_hash, 64, "f");
			} else {
				if(strlen($this->code_hash) == 0) {
					$this->code_hash = str_pad('', 64, "f");
				}
			}
			$result = [];
			$arr = str_split($this->code_hash, 6);
			for($i=0; $i < count($arr); $i++) {
				$result[] = [
					'HEX' => $arr[$i],
					'RGB' => $this->hex2rgb($arr[$i])
				];
			}
			$this->code_arr = $result;
		}
		
		/**
		* Отрисовывает PNG изображение с кодом
		* @return mixed
		*/
		public function render($block_width = 10, $padding = 10) {
			header('Content-Type: image/png');
			//TODO: заменить на aspect_ratio
			//TODO: проверку ошибок
			$arr = $this->code_arr;
			$img_width  = $block_width * count($this->code_arr) + $padding * 2;
			$img_height = $block_width + $padding * 2;
			
			$code_img = imagecreatetruecolor($img_width, $img_height);
			$color_bg = imagecolorallocate($code_img, 255, 255, 255);
			//imageantialias($code_img, false);
			imagefill($code_img, 0, 0, $color_bg);
			//alpha not working - wtf?!
			//$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
			//imagefill($code_img, 0, 0, $transparent);
			//imagesavealpha($code_img, true);
			
			for($i=0; $i < count($arr); $i++) {
				//координаты
				$x1 = $padding + $i * $block_width;
				$y1 = $padding;
				$x2 = $x1 + $block_width;
				$y2 = $y1 + $block_width;
				//цвет
				$rgb = $arr[$i]['RGB'];
				$color = imagecolorallocate($code_img, $rgb[0], $rgb[1], $rgb[2]);
				//рисование цветного блока
				switch($this->form) {
					default:
						imagefilledrectangle($code_img, $x1, $y1, $x2, $y2, $color);
						break;
					case 'circle':
						$cx = $x1 + round($block_width / 2);
						$cy = $y1 + round($block_width / 2);
						imagefilledellipse($code_img, $cx, $cy, $block_width, $block_width, $color);
						break;
				}
			}
			imagepng($code_img);
		}
	}
	