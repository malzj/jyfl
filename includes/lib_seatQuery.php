<?php
/**
 *    座位生成类
 *    
 *    得到座位数组，返回可以循环显示的座位信息 array() 格式
 *    
 *    array(
 *    	[排号] = > array(
 *    				'allWidth'=> 左边到最后一个座位的距离
 *    				'leftsWidth'=> 中间离左边的距离
 *    				'hallId'=> 厅号
 *    				'graphRow'=> 排号
 *    				'columns'=>array(  		座位数组
 *    							0=> array(  左边的座位
 *    									0=> array(    如果是走廊的话是这个数据，否则是正常的数据
 *    										[graphCol] => 1		// 座位位置
		                                    [graphRow] => 1		// 排号
		                                    [hallId] => 4		// 厅号
		                                    [seatEmpty] => true
 *    										)，
 *    									1=>array(
 *    										... ...   正常的座位数据
 *    										)
 *    								),
 *    							1=> array(  右边的座位
 *    									... ...
 *    								)
 *    						) 
 *    				)
 *    );
 */

class seatQueryInfo{
	//中间距离左边的距离
	private $leftsWidth = '';
	
	// 影厅的宽
	private $width = 699;
	
	//座位加左边的宽
	private $allWidth = '';
	
	// 单个座位的宽
	private $colsWidth = 17;
	
	// 厅号的属性名 
	private $hallName = '';
	
	//Y 的属性名
	private $graphRowName = '';
	
	//x 的属性名
	private $graphColName = '';
	
	// 排号
	private $seatRowName = '';
	
	// 座位整理后的数据
	private $seatInfo = array();
	
	// 每排有多少类的统计 array( '排号' => '座位数')
	private $colsCount = array();
	
	// 最多列的倒序 
	private $colsDesc = array();
	
	// 排序方式,		默认是升序，是按照 X 排序的
	private $colsOrder = 'ASC';
	
	// 左边的座位个数
	private $leftCols = '';
	 
	// 给属性赋值
	public function __construct( $info=array() ){
		if(count($info) > 0) {
			foreach($info AS $key=>$val){
				if(property_exists($this,$key) == false){
					continue;
				}
				$this->$key = $val;
 			}
		}
	}
	/** 
	 * 获取单个属性
	 * 
	 * @param	$field	string	 属性名
	 */
	public function getField($field){
		if(!empty($field) && property_exists($this, $field) == true){
			return  $this->$field;
		}
	}
	
	/** 
	 *  获得数据信息
	 *  
	 *  @pamam	$data	array	座位数据
	 */
	public function getSeatInfo($data=array()){
		// 没数据返回空
		if( count($data) == 0 || !is_array($data)){  return array();	}
		
		if( count($this->seatInfo) > 0) { 	return $this->seatInfo; }
		
		$tmpData = array();
		
		foreach($data AS $key=>$val){
			// 把同一行的列放到一起
			$graphRow = $val[$this->graphRowName];
			$tmpData[$graphRow]['columns'][] = $val;
			
			// 统计行里面的列
			if(!array_key_exists($graphRow, $this->colsCount)){
				$this->colsCount[$graphRow] = $val[$this->graphColName];
			}
			if($this->colsCount[$graphRow] < $val[$this->graphColName]){
				$this->colsCount[$graphRow] = $val[$this->graphColName];
			}
		}
		// 按键从小到大排序
		ksort($tmpData);
		
		// 最多的座位数
		$maxCols = (int)max($this->colsCount);
		$this->colsDesc($maxCols);
		
		// 情侣座 左 右
		foreach($tmpData as &$t){
			$isConples = false;
			foreach($t['columns'] as &$t2){
				if($t2['seatType']==1 && $isConples == true) {
					$t2['conplesPos'] = 2;
					$isConples = false;
				}elseif ($t2['seatType']==1 && $isConples == false){
					$t2['conplesPos'] = 1;
					$isConples = true;
				}
			}
		}
		
		//行号（非排号）
		$graphRow = array();
		
		//填充空白的列
		foreach($tmpData as $rows=>&$seats){
			$graphRows = array();
			foreach($seats['columns'] as $graph){
				$graphRows[]=$graph[$this->graphColName];
			}	
			foreach($this->colsDesc as $cols){
				if(!in_array($cols,$graphRows)){
					$emptyRows[$this->graphColName] = $cols;						// 座位的 X 	
					$emptyRows[$this->graphRowName] = $rows;						// 座位的 Y   就是行
					$emptyRows[$this->seatRowName]  = $graph[$this->seatRowName];	// 排号
					$emptyRows[$this->hallName] = $seats['columns'][0][$this->hallName];
					$emptyRows['seatEmpty'] ='true';
					$seats['columns'][] = $emptyRows;
				}
			}
			
			$graphRow[] = $rows;
		}
		
		// 填充空白的行（空白的行肯能是走廊）
		$maxRow = max($graphRow);
		$minRow = min($graphRow);
		for($i=$minRow; $i<=$maxRow; $i++)
		{
			if (!in_array($i, $graphRow))
			{
				$tmpData[$i] = array('columns'=>array(0=>array('seatEmpty' => 'true')));
			}
		}
		
		// 按键从小到大排序
		ksort($tmpData);
		
		// 左边距离第一个座位的宽
		$this->leftsWidth($maxCols);
	
		// 更新座位数据
		foreach($tmpData as &$sea){
			$sea['allWidth'] = $this->allWidth;
			$sea['leftsWidth'] = $this->leftsWidth;
			$sea[$this->hallName] = $sea['columns'][0][$this->hallName];  // 厅号
			$sea[$this->graphRowName] = $sea['columns'][0][$this->graphRowName]; // 行号
			$sea[$this->seatRowName] = $sea['columns'][0][$this->seatRowName]; // 排号
			// 排序
			$sort_desc_asc = array();
			foreach($sea['columns'] as $keys=>$cols2){
				$sort_desc_asc[$keys]=$cols2[$this->graphColName];
			}
			array_multisort($sort_desc_asc, SORT_ASC, $sea['columns']);
				
			$leftArr = array_slice($sea['columns'],0,$this->leftCols,TRUE);
			$rightArr = array_slice($sea['columns'],$this->leftCols,$this->leftCols,TRUE);
			unset($sea['columns']);
			$sea['columns'][0] = $leftArr;
			$sea['columns'][1] = $rightArr;
		}
		
		return $this->seatInfo = $tmpData;
	
		
	}
	
	// 倒序
	private function colsDesc($maxCols){
		$colsDesc = array();
		for($i=$maxCols; $i>0; $i--){  $colsDesc[]=$i; }
		$this->colsDesc = $colsDesc;
	}
	
	//
	private function leftsWidth($maxCols){
		if(empty($maxCols)){
			return '';
		}
		// 座位基偶数，基数就把多的一个座位放到左边		
		$colsEven = ceil($maxCols/2);
		$leftCols = $colsEven;
		if(strpos($colsEven,'.') > 0){
			$leftCols = ceil($colsEven);
		}
		
		// 座位总宽
		$colsTotal = $maxCols*17;
		// 左边座位宽
		$leftTotal = $leftCols*17;
		// 右边座位宽
		$rightTotal = $colsTotal - $leftTotal;
		// 左边的总宽
		$this->leftsWidth = 340 - $leftTotal;
		$this->allWidth = $colsTotal + $this->leftsWidth + 20;
		$this->leftCols = $leftCols;
	}	
}