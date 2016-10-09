<?php 

//日历类
class calendar {
	//当前的年
	private $year;
	//当前的月
	private $month;
	//一个月中第一天是星期几
	private $start_weekday;
	//当前月的天数
	private $days;
	//最大数与最小年数，最大与最小月数
	private $yearMonth = array(2080, 1900, 12, 1);
	// 日期里放的内容
	private $dayContent = array();
	//构造函数
	function __construct($year, $month, $data=array()) {
		if (isset($year)) {
			$this->year = $year;
		}
		if (isset($month)) {
			$this->month = $month;
		}
		if (isset($data)){
			$this->dayContent = $data;
		}
		$this->pnYm($this->year, $this->month);
		$this->days = date('t', mktime(0, 0, 0, $this->month, 1, $this->year));
		$this->start_weekday = date('w', mktime(0, 0, 0, $this->month, 1, $this->year));
	}
	//输出
	public function style() {		
		$out = $this->daylist();
		return $out;
	}
	//年月参数判断
	private function ymCheck($year, $month) {
		if (!is_numeric($year)) {
			$year = date('Y');
		}
		if (!is_numeric($month)) {
			$month = date('m');
		}
		if ($month < $this->yearMonth[3]) {
			$month = $this->yearMonth[2];
			$year -= 1;
		}
		if ($month > $this->yearMonth[2]) {
			$month = $this->yearMonth[3];
			$year = intval($year) + 1;
		}
		$year = $year < $this->yearMonth[1] ? $this->yearMonth[1] : $year;
		$year = $year > $this->yearMonth[0] ? $this->yearMonth[0] : $year;
		return array($year, $month);
	}
	//上一年、下一年、上一月、下一月
	private function pnYm($year, $month) {
		$ym = $this->ymCheck($year, $month);
		$this->year = $ym[0];
		$this->month = $ym[1];
	}
	//weeklist周列表
	private function weeklist() {
		$week = array('日','一','二','三','四','五','六');
		echo '<tr>';
		foreach ($week as $val) {
			echo '<th>'.$val.'</th>';
		}
		echo '</tr>';
	}
	//daylist天列表
	private function daylist(){
		// 头
		$out  = '<div class="calendar_title">';
		if(mktime(0,0,0,$this->month,1,$this->year)>time()) {
			$out .= '<span class="mui-icon mui-icon-arrowleft mui-pull-left" data-time="'.$this->year.'-'.(intval($this->month)-1).'-1"></span>';
		}
		$out .= $this->year.'年'.$this->month.'月价格日历（选择游玩日期预定）';
		$out .= '<span class="mui-icon mui-icon-arrowright mui-pull-right" data-time="'.$this->year.'-'.(intval($this->month)+1).'-1"></span>';
		$out .= '</div>';

		// 星期
		$out .= '<table border="0">';
		$out .= '<tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr>';
		$out .= '<tr>';
		//输出空格（当前一个月第一天前面要空出来的）
		for($i = 0; $i < $this->start_weekday; $i++) {
			$out .= '<td><div></div></td>';
		}
		for ($k = 1; $k <= $this->days; $k++) {
			$i++;
			$price = '&nbsp;';
			$riqi  = 0;
			if($k < 10)
			{
				$k = '0'.$k;
			}
			if(isset($this->dayContent[$k])){
				$price = $this->dayContent[$k].'点';
				$riqi = $this->year.'-'.$this->month.'-'.$k;
			}

			if ($k == date('d')) {
				$out .= '<td '.($price!='&nbsp;'?'class="calendar_buy" date="'.$riqi.'"':'').'><div>'.$k.'<span class="calendar_price">'.$price.'</span></div></td>';
			}else {
				$out .= '<td '.($price!='&nbsp;'?'class="calendar_buy" date="'.$riqi.'"':'').'><div>'.$k.'<span class="calendar_price">'.$price.'</span></div></td>';
			}
			if ($i % 7 == 0) {
				if ($k != $this->days) {
					$out .= '</tr><tr>';
				}
			}
		}
		$out .= '</tr></table>';

		return $out;
	}
}


