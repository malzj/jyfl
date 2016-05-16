<?php
/**
 *  导入/导出 自动转载处理类
 *  
 *  标准的导出只需要以下几项即可
 *  $autoExcels = new autoExcels('Execl2007');		实例化类并传入导出格式（可以不传，默认是2007）
 *  $autoExcels->setSaveName('aototest.xlsx');	 	要保存的文件名（必须）
 *  $autoExcels->setTitle($arrayExcel);				设置数据（必须）
 *  $autoExcels->PHPExcel()							PHPExcel设置其他个性属性，个性使用参考PHPExcel, 这里只是把PHPExcel实例化返回
 *  $autoExcels->execExcel('export');				执行下载 操作（必须） 
 *  
 *  导入只需要以下几步足矣
 *  $PHPExcel = new autoExcels('Excel2007');		实例化类并传入导出格式（可以不传，默认是2007）
	$PHPExcel->setSaveName('autoExcels2.xlsx');		要保存的文件名（必须）
	$PHPExcel->setColsTitle($colsTitle);			设置导入数据对应的字段，下标是从0开始，0代表excel表里的A，1代表B，以此类推，也可以直接写ABCD
	$list = $PHPExcel->execExcel('import');			执行导入操作（必须）
	
	==============================================================================================
	注释： execExcel() 里面的 import 是指导入，而export是导出。
		  $colsTitle 设置的字段名和数据库中的字段名对应，这样可以直接保存数据库，而不用再次修改了
	==============================================================================================
 *	
 *	$colsTitle  数据格式：
 *  array(
 *  	// 第一个sheet
		'0'=>array(
				'A'=>'username',
				'B'=>'sex',
				'c'=>'age',
				'd'=>'chusheng'
			),
		// 第二个sheet
		'1'=>array(
				0=>'city',
				1=>'quyu',
				2=>'address'
			),
		// 第三个sheet
		'2'=>array(
				0=>'nianji',
				1=>'username',
				2=>'age',
				3=>'renyuan',
				4=>'sex',
				5=>'gexing',
				6=>'aihao',
				7=>'xiguan'
				
			)				
		);
 *		
 *  $arrayExcel 数据格式：
 *  array(  
 *     ... ...  多个sheet      
	   array(
	   		// sheet名
			'sheetName'=>'家庭住址',
			// 单元格标题
			'title'=>array(
					'A5'=>'城市',
					'B5'=>'区县',
					'C5'=>'地址',
					),
			// 单元格的内容
			'content'=> array(
							array(
								'A6'=>'中国/北京',
								'B6'=>'大兴',
								'C6'=>'大兴大不列颠共和国15号楼200层1258室',
							),
							array(
								'A7'=>'中国/河北',
								'B7'=>'高碑店',
								'C7'=>'高碑店答不了的共和国25号楼20000层2596521室',
							),
							array(
								'A8'=>'中国/河北',
								'B8'=>'高碑店',
								'C8'=>'高碑店答不了的共和国25号楼20000层2596521室',
							)
					),
			// 单元格的宽
			'widths'=>array(
						'A'=>10,
						'B'=>20,
						'C'=>25
					),
			// 保护单元格					
			'protect'=>array(
						'cells'=>'B5:C:last',  		// 可以输入密码的单元格
						'password'=>'jinbuqu'		// 需要输入的密码
					)
			) ... ...
		)
 *  
 *  
 */
header('Content-type: text/html; charset=utf-8');
class autoExcels{
	
	/** 导入/导出类型，默认是 Excel2007，可以选择其他类型 */
	private $_excelType = 'Excel2007';
	
	
	/** 操作类型， import 导入  export 导出 */
	private $_downType = null;
	
	
	/** 启动筛选功能， 默认开启 */
	private $_autoFilter = true;
	
	
	/** 保存的文件名 */
	private $_saveName = '';
	
	
	/** 支持的导出类型，目前先写这几个 */
	private $_allExcelType = array( 'Excel2007' ,'Excel5');
	
	/** 支持的后缀名 */
	private $_suffixs = array( 'xlsx', 'xls');
	
	/** 错误消息，数组的形式，控制器直接调用错误消息 getMessage();*/
	private $_message = array();
	
	
	/** 安全设置 */
	private $_isLocks = false;
	
	
	/** PHPExcel 类源 */
	private $_PHPExcel = null;
	
	/** PHPExcel 所在目录 */
	private $_excelRoot = '';
	
	/**  */
	private $_titleSheet = array();
	
	/** 导入时标题的定位符 ，默认是 # */
	private $_locator = '#';
	
	/** 导入类型的引导 */
	private static $_importMethedList = array(
		array( 'type'=>'Excel2007', 'methed'=>'import{0}'),
		array( 'type'=>'Excel5', 'methed'=>'import{0}')
	);
	
	/** 内容开始行  */
	private $_firstRow = 0;
	/** 内容结束行  */
	private $_endRow = 0;
	/** 开始列 */
	private $_firstCol = '';
	/** 结束列 */
	private $_endCol = '';
	
	
	/** 导入时对应的标题数据 */
	private $_colsTitle = array();
	
	/** 大写字母对应数字 */
	private $_letterList = array(
		'A'=>0,  'B'=>1,  'C'=>2,  'D'=>3,  'E'=>4,  'F'=>5,  'G'=>6,  'H'=>7,  'I'=>8,  'J'=>9,  'k'=>10,
		'L'=>11, 'M'=>12, 'N'=>13, 'O'=>14, 'P'=>15, 'Q'=>16, 'R'=>17, 'S'=>18, 'T'=>19, 'U'=>20,
		'V'=>21, 'W'=>22, 'S'=>23, 'Y'=>24, 'Z'=>25,
		'AA'=>26, 'AB'=>27, 'AC'=>28, 'AD'=>29, 'AE'=>30, 'AF'=>31, 'AG'=>32, 'AH'=>33, 'AI'=>34, 'AJ'=>35,  'Ak'=>36,
		'AL'=>37, 'AM'=>38, 'AN'=>39, 'AO'=>40, 'AP'=>41, 'AQ'=>42, 'AR'=>43, 'AS'=>44, 'AT'=>45, 'AU'=>46,
		'AV'=>47, 'AW'=>48, 'AS'=>49, 'AY'=>50, 'AZ'=>51,
	);
	/**
	 * @param	获得导入类型
	 * @return 	string
	 */
	public function getExeclType(){
		return $this->_excelType;
	}
	
	/**
	 *  得到标题和sheet信息
	 */
	public function getTitleSheet(){
		return $this->_titleSheet;
	}
	
	/**
	 *  返回开始的行 
	 */
	public function getFirstRow(){
		return $this->_firstRow;
	}
	
	/**
	 *  返回结束的行
	 */
	public function getEndRow(){
		return $this->_endRow;
	}
	/**
	 *  返回开始的列
	 */
	public function getFirstCol(){
		return $this->_firstCol;
	}
	/**
	 *  返回结束的列
	 */
	public function getEndCol(){
		return $this->_endCol;
	}
	
	/**
	 *  返回指定列整行的格式
	 *  
	 *  @param	string 	$p		指定开始列
	 *  @param	string	$e		指定结束列
	 *  @return string			
	 */
	public function getColsFormat($p=null, $e=null){
		$string = '';
		// 都为空，则是整列的格式
		if ($p === null && $e === null){
			$string = $this->_firstCol.$this->_firstRow.':'.$this->_endCol.$this->_endRow;
		}
		// 未指定结束列，默认当前列
		if (!is_null($p) && is_null($e)){
			$string = strtoupper($p).$this->_firstRow.':'.strtoupper($p).$this->_endRow;
		}
		//
		if(!is_null($p) && !is_null($e)){
			$string = strtoupper($p).$this->_firstRow.':'.strtoupper($e).$this->_endRow;
		}
		return $string;
	}
	/**
	 * 开启安全级别，开启后保存下来的文件，不能进行修改的操作，权限是只读
	 */
	public function setLock($t=true){
		$this->_isLocks = $t;
	}
	
	/**
	 * 	设置保存的文件名
	 *  @param 	string $name			输入的文件名
	 */
	public function setSaveName($name){	
		// 有后缀名处理
		$suffixs = explode('.',trim($name));		
		if(isset($suffixs[1])){
			$suffix = $suffixs[1];
			if(in_array($suffix, $this->_suffixs)){
				$this->_saveName = $name;
				return true;
			}
			$this->setMessage('不支持您提供的后缀名文件，请修改！','setSaveName');
			return false;
		}
		
		// 没后缀名加上默认的后缀名 .xlsx
		$this->_saveName = $name.'.xlsx';
		return true;
	}
	
	/**
	 *  设置操作类型 ： 导入(import) 导出(export)
	 *  
	 */
	public function setExcelType($type){
		if( in_array(trim($type), array('import','export')) ){
			return $this->_downType = trim($type);
		}
	}	
	
	/** 
	 * 初始化 
	 * @param unknown_type $type
	 */
	public function __construct($type=null){
		// 设置导入/导出类型  
		if($type != null && $this->_checkExcelType($type)){
			$this->_excelType = trim($type);
		}
		// 装载PHPExcel类库
		$excelRoot = $this->excelRoot();
		$PHPExcel = trim($excelRoot,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'PHPExcel.php';
		if(file_exists($PHPExcel)){
			require_once $PHPExcel;
			$this->_PHPExcel = new PHPExcel();
		}else{
			$this->setMessage('文件：'.$PHPExcel.' 不存在，请检查您的路径'); 
		}
	}
	
	/**
	 *  设置文档列的标题、sheet名称、位置
	 *  
	 *  导出的时候用 @export
	 */
	public function setTitle($data){
		if(!isset($data) && empty($data))  return false;
		
		$PHPExcel = &$this->_PHPExcel;
		
		foreach($data AS $key=>$val){
			// 开始行数
			$this->_firstRow = $this->findNum( key($val['title']) )+1;
			// 结束的行数
			$this->_endRow = ($this->lastNum($val['content'])+$this->_firstRow);
			
			// 开始的列号  例: A 或 B
			$valKeys = array_keys($val['title']); 			
			$this->_firstCol = $this->findNum(current($valKeys), true);			
			// 结束的列号  例: C 或 D
			$this->_endCol = $this->findNum(end($valKeys), true);
			
			//sheet 大于0，创建之
			if($key !==0){
				$PHPExcel->createSheet();
			}
			
			// 设置标题
			foreach($val['title'] AS $colId=>$colName){
				$PHPExcel->setActiveSheetIndex($key)->setCellValue($colId,$colName);
			}
			// 设置内容
			if(isset($val['content']) && !empty($val['content'])){
				$this->setCententInfo( $val['content'], $key );
			}
			
			// 设置单元格的宽
			if(isset($val['widths']) && !empty($val['widths'])){
				$this->setCellWidths( $val['widths'], $key );
			}
			
			/**
			 * 	保护单元格 
			 *	可以保护整列单元格，也可以保护所有单元格
			 *	格式：
			 *			开始列号 : last				例: A:last     整个A列可以输入密码修改
			 *			开始列号 : 结束列号 : last   	例: A:E:last	      从A到E的所有单元格可以输入密码修改
			 */
			
			if(isset($val['protect']) && !empty($val['protect'])){
				$cellName = trim($val['protect']['cells']);
				$password = trim($val['protect']['password']);
				$PHPExcel->getSheet($key)->getProtection()->setSheet(true);
				if( empty($password) ){
					$password = 123456;
				}
				
				$lastNum = $this->lastNum($val['content']);
				$cellNames = explode(':',$cellName);
				$last = $cellNames[1];
				if($cellNames[1]=='last'){
					$letter = $this->findNum($cellNames[0], true);
					$last 	= $letter.$this->_endRow; 
				}
				
				if(isset($cellNames[2]) && $cellNames[2]=='last'){
					$letter = $this->findNum($cellNames[1], true);
					$last 	= $letter.$this->_endRow;
				}
				$PHPExcel->getSheet($key)->protectCells($cellNames[0].':'.$last, $password);
			}
			// 设置sheetName
			$PHPExcel->getActiveSheet()->setTitle($val['sheetName']);
			
			// 设置自动筛选
			$PHPExcel->getActiveSheet()->setAutoFilter($this->_firstCol.($this->_firstRow-1).':'.$this->_endCol.$this->_endRow);
			
			// 保存信息，在内容处调用
			$this->_titleSheet[$key] = array( 'firstRow'=>$this->_firstRow, 'endRow'=>$this->_endRow, 'firstCol'=>$this->_firstCol, 'endCol'=>$this->_endCol, 'sheetId'=>$key, 'sheetName'=>$val['sheetName'] );
		}
		
		//  其他的处理....  后面需要在添加
		
	}
	/**
	 *  设置单个sheet的内容
	 *  
	 **/
	private function setCententInfo($data, $sheetId=0){
		if(count($data) < 1 ) return array();
		foreach($data AS $key=>$val){			
			foreach($val AS $colId=>$colName){
				$this->_PHPExcel->setActiveSheetIndex($sheetId)->setCellValue($colId,$colName);
			}
		}
	}
	
	/**
	 *  在导入 Excel 文件的时候，需要调用的方法，用于整理合法的数据的标题 
	 */
	public function setColsTitle($data){
		for ($i=0; $i<count($data); $i++) {
			foreach ($data[$i] as $rows=>$cols) {
				// 输入的是字母，转成数字
				if (!is_numeric($rows)) {
					$row = $this->_letterList[strtoupper($rows)];
					unset($data[$i][$rows]);
					$data[$i][$row] = $cols;
				}
			}
		}
		$this->_colsTitle = $data;
	}
	/** 执行导入和导出操作 */
	public function execExcel($type){
		// 设置操作类型
		if($this->_downType ===null){
			$this->setExcelType($type);
		}
		// 没有操作类型的时候，保存错误信息
		if($this->_downType ===null){
			$this->setMessage('我不知道您是想要导入还是导出，请设置 execExcel()的值,或setExcelType()的值');
			return false;
		}
		//分配执行
		switch($this->_downType){
			case 'import':
				return $this->importExcel();
				break;
			case 'export':
				return $this->exportExcel();
				break;
		}
	}
	/**
	 *  执行导出操作
	 */
	private function exportExcel(){		
		switch($this->_excelType){
			case 'Excel2007':
				$objWriter = PHPExcel_IOFactory::createWriter($this->_PHPExcel, 'Excel2007');
				$objWriter->setOffice2003Compatibility(true);				
				break;
			case 'Excel5':
				$objWriter = PHPExcel_IOFactory::createWriter($this->_PHPExcel, 'Excel5');				
				break;
		}
		
		if(isset($this->_saveName)){
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");
				header("Content-Type: application/vnd.ms-execl");
				header('Content-Disposition:inline;filename="'.$this->_saveName.'"');
				header("Content-Transfer-Encoding: binary");
				header("Last-Modified: " . date("D, d M Y H:i:s", time()) . " GMT");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: no-cache");
				ob_clean();//关键
				flush();//关键
				$objWriter->save('php://output');
		}
		return true;
	}
	
	/** 执行导入操作 */
	private function importExcel(){
		// 搜索的名
		$searchType = $this->_excelType;
		// 加载指定导入方法
		foreach (self::$_importMethedList as $methed){
			if ($methed['type'] == $searchType) {
				$methed = str_replace('{0}', $searchType, $methed['methed']);
				return $this->$methed();
			}
		}		
	}
	
	/** 导入 excel2007 格式的 */
	private function importExcel2007(){
		
		$result = array();
		// 创建导入类
		$objReader = PHPExcel_IOFactory::createReader($this->_excelType);
		// 读取文件
		$PHPExcel= $objReader->load($this->_saveName);
		// 所有sheet数量
		$sheetCount = $PHPExcel->getSheetCount();
		if($sheetCount < count($this->_colsTitle)){
			array_pop($this->_colsTitle);
		}
		
		//处理数据
		for($i=0; $i<$sheetCount; $i++){
			// 当前sheet转换的数据
			$sheetContent = $PHPExcel->getSheet($i)->toArray();
			// 去除标题之前的空白数据
			$offset = 0;
			foreach($sheetContent as $row=>$content){				
				if(strpos(current($content), $this->_locator) !== false){
					$offset = $row;					
				}
			}
			// 去除内容中穿插的空白行
			$sheetContents = array_splice($sheetContent, $offset);
			foreach($sheetContents as $rows=>$cols){
				$isEmpty = false;
				$current = trim(current($cols));
				if(empty($current)){
					$isEmpty = true;
				}
				if($isEmpty === true){
					unset($sheetContents[$rows]);
				}
			}
			
			// 整理最终的数据
			$colsTitle = $this->_colsTitle[$i];
			$keys = array_keys($colsTitle);
			rsort($keys);
			$maxCol = $keys[0];			
			foreach($sheetContents as $_k=>$_v){
				foreach($_v as $id=>$name){
					// 删除旧的数据
					unset($sheetContents[$_k][$id]);
					//多余的列不进行任何操作				
					if($id > $maxCol) { continue; }			
					$colsName = $colsTitle[$id];					
					$sheetContents[$_k][$colsName] = $name;
					
				}
			}
			
			$result[$i] = $sheetContents;
		}
		return $result;
	}
	/** 导入兼容格式的 */
	private function importExcel5(){
		return true;
	}
	
	// 设置单元格的宽
	private function setCellWidths($widths,$sheetId){
		foreach($widths AS $cell=>$width){
			$this->_PHPExcel->setActiveSheetIndex($sheetId)->getColumnDimension($cell)->setWidth($width);
		}		
	}	
	
	/**  检查导出类型是否正确
	 * 
	 * 	 @return   TRUE|FALSE  
	 **/
	private function _checkExcelType($type){
		if($type == null) return false;
		$type = trim($type);
		if(in_array($type, $this->_allExcelType)){
			return true;
		}
		return false;
	}
	
	/** 
	 * 取出字符串中的数字
	 */
	public function findNum($str,$isStr=false){
		$str = trim($str);
		if(empty($str)) return '';
		$reslut = '';
		for($i=0; $i<strlen($str); $i++){
			// 数字
			if($isStr == false){
				if(is_numeric($str{$i})){
					$reslut .=$str{$i};
				}
			// 字符串
			}else{
				if(!is_numeric($str{$i})) {
					$reslut .= $str{$i};
				}
			}
			
		}
		return $reslut;
	}
	
	private function lastNum($data){ 
		return count($data); 
	}
	/** 返回PHPExcel类**/
	public function PHPExcel(){ 
		return $this->_PHPExcel;
	}
	/** 当前文件路径 */
	public function excelRoot(){ 
		return $this->_excelRoot = dirname(__FILE__).DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR; 
	}
	/** 设置错误消息 */
	private function setMessage($m,$c=''){	
		$this->_message = array_merge($this->_message, array( 'message'=>$m, 'action'=>$c)); 
	}
	/** 返回错误信息 */
	public function getMessage(){ 
		return $this->_message; 
	}
	
	
}