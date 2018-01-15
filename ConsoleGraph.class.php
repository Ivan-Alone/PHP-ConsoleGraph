<?php
	/*
		ConsoleGraph PHP Class
		Copyright Ivan_Alone, 2018
		GNU General Public License 3
	*/
	
	class ConsoleGraph {
		private $useStarsAsWinBuilders = false;
		
		private $slide = 0;
		
		private function read() {
			return trim(fgets(STDIN));
		}
		private function exeR($aim) {
			self::exe($aim);
		}
		private function exeW($aim) {
			@pclose(@popen($aim,'w'));
		}
		private function exe($aim) {
			@pclose(@popen($aim,'r'));
		}
		private function getSlideSpaces() {
			$str = '';
			for ($i = 0; $i < $this->slide; $i++) $str .= ' ';
			return $str;
		}
	
		public function graphSetSlide($slide) {
			$this->slide = $slide;
		}
		
		public function __construct($useStarsAsWinBuilders = false) {
			if (is_string($useStarsAsWinBuilders)) {
				if ($useStarsAsWinBuilders == '__do_not_configure_window') {
					$this->useStarsAsWinBuilders = false;
					return;
				}
			}
			$this->useStarsAsWinBuilders = $useStarsAsWinBuilders;
			self::configureWindowSize();
			self::clear();
		}
		
		private function configureWindowSize() {
			if (PHP_OS == 'WINNT')
				self::exeW('mode con:cols=120 lines=30');
			else 
				echo "\e[8;30;120t";
		}
		
		public function graphClear() {
			self::exeW(PHP_OS == 'WINNT' ? 'cls' : 'reset');
		}
		
		public function clear() {
			self::graphClear();
		}
		
		public function graphTitle($title) {
			$title = $title==null?'Console Graph Class App':trim($title);
			if (PHP_OS == 'WINNT') {
				self::exe('title '.$title);
			} else {
				echo "\033]2;{$title}\007";
			}
		}		
		
		public function graphColor($bg, $txt) {
			if (PHP_OS == 'WINNT') {
				self::exeW('color '.dechex(max(0, min(15, $bg))).dechex(max(0, min(15, $txt))));
				return true;
			} else {
				return false;
			}
		}
		
		public function graphColorReset() {
			if (PHP_OS == 'WINNT') {
				self::exeW('color 07');
				return true;
			} else {
				return false;
			}
		}
		
		public function graphReadLn($text = null) {
			self::graphLine();
			echo ($text==null?null:$text.' ').self::getSlideSpaces().'> ';
			$try = self::read();
			echo chr(0x0D);
			return $try;
		}
		
		public function graphReadPassword($text = null) {
			self::graphLine();
			echo ($text==null?null:$text.' ').self::getSlideSpaces().'> ';
			$try = self::read();
			$mask = '';
			if (PHP_OS == 'WINNT')
				for ($i = 0; $i < 117-$this->slide-2-($text==null?0:strlen($text)+1); $i++) {
					echo chr(0x08);
					if ($i < strlen($try))
					$mask .= '▒';
				}
			else {
				echo "\033[1A";
				$ccc = 3 + strlen($text.'') + 2 + $this->slide;
				echo "\033[{$ccc}C";
				for ($i = 0; $i < strlen($try); $i++) {
					$mask .= '▒';
				}
			}
			echo $mask;
			echo chr(0x0A);
			echo chr(0x0D);
			return $try;
		}
		
		public function graphDottedLine() {
			$gen = '';
			for ($i = 0; $i < 114; $i++) {
				$gen .= ($i%2) ? ' ' : ($this->useStarsAsWinBuilders?'*':'=');
			}
			self::graphLine();
			self::graphWrite($gen, true);
		}
		
		private function graphLine($isFull = false, $isEmpty = false, $start_marker = -1) {
			
			echo $this->useStarsAsWinBuilders?' *':($start_marker == -1 ? ($isFull?' ╠':' ║') : ($start_marker == 0 ? ' ╔' : ' ╚')); 
			for($i=0;$i<116;$i++)
				echo $isFull?($this->useStarsAsWinBuilders?'*':'═'):' ';
			echo $this->useStarsAsWinBuilders?'*':($start_marker == -1 ? ($isFull?'╣':'║') : ($start_marker == 0 ? '╗' : '╝')); 
			if ($isFull) {
				echo chr(0x0A);
				echo chr(0x0D);
				return;
			}
			if (!$isEmpty) {
				for($i=0;$i<116;$i++) 
					echo chr(0x08);
			} else {
				echo chr(0x0A);
				echo chr(0x0D);
			}
		}
		
		public function graphFilledLine() {
			self::graphLine(true);
		}
		
		public function graphEmptyLine() {
			self::graphLine(false, true);
		}
		
		public function graphStartingLine() {
			self::graphLine(true, false, 0);
		}
		
		public function graphEndingLine() {
			self::graphLine(true, false, 1);
		}
		
		private function graphWrite($text, $isCenter=false) {
			$text = ($isCenter ? null:self::getSlideSpaces()).$text;
			$str = '';
			for ($i = 0; $i < min(114, strlen($text)); $i++) {
				$str .= $text[$i];
			} 
			echo $str;
			echo chr(0x0A);
			echo chr(0x0D);
		}
		
		public function graphWriteToCenterLine($text) {
			self::graphLine();
			$text = trim($text);
			$str = '';
			for ($i = 0; $i < min(114, strlen($text)); $i++) {
				$str .= $text[$i];
			} 
			$d = (strlen($str)/2.0);
			$r = round($d);
			$d = $r < $d ? $r+1 : $r;
			for ($i = 0; $i < 57-$d; $i++) {
				$str = ' '.$str;
			}
			self::graphWrite($str, true);
		}
		
		private $progress_pos;
		
		public function graphProgressBarUpdate($current, $count) {
			$sym = $this->useStarsAsWinBuilders?'#':'▓';			
			if ($current > $count) {
				$cur_txt = $count;
			} else {
				$cur_txt = $current;
				for ($i = 0; $i < strlen(''.$count)-strlen(''.$current); $i++) {
					$cur_txt = ' '.$cur_txt;
				}
			}
			
			$counter = $cur_txt.' / '.$count.'  ';
			
			$len = strlen($counter);
			$_100_perc = 114-$len;
			
			$blocks = round(($current/$count)*$_100_perc);
			
			for ($i = 0; $i < $blocks; $i++) {
				$counter .= $sym;
			}
			
			echo $counter;
			
			for ($i = 0; $i < strlen($counter)-($this->useStarsAsWinBuilders?0:2*$blocks); $i++) {
				
				echo chr(0x08);
			}
			
		}
		
		public function graphProgressBarCreate() {
			self::graphLine();
		}
		
		public function graphProgressBarClose() {
			echo chr(0x0A);
			echo chr(0x0D);
		}
		
		public function graphWriteToLine ($text) {
			self::graphLine();
			self::graphWrite($text);
		}
		
		public function graphFinish() {
			self::graphPause();
			exit;
		}
		
		public function graphPause() {
			shell_exec(PHP_OS == 'WINNT' ? 'pause' : "read a");
		}
	}
?>