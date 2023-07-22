<?php
/******************************************************************  
	Name   : func(v1.0) 
	Creater:  chg -2020/12/23
	Modify : 
*******************************************************************/
class func
{
  
	/******************************************************************
		Name   : excSQL - 執行sql語法 回傳搜尋資料 
		# sql_cmd無瀏覽器傳入資料才可使用
		Val    : 
				 @param  string      $sql_cmd  - sql語法
				 @param  object     $PDOLink -  PDO連線資料
				 @param  boolean  $isAll  - true : fetchAll || false : fetch
				 @return  array  -    $data['data'] - 資料['欄位名']  
		Use    :  
		Creater: chg -2020/12/23
		Modify : chg -2021/01/08 - 加入參數 fetchAll 或 fetch
	*******************************************************************/	
 	public static function excSQL($sql_cmd, $PDOLink, $isAll)
	{
	   try{
			$stmt = null;
			$stmt = $PDOLink -> prepare($sql_cmd);
			$stmt -> execute();
			($isAll) ? $data = $stmt -> fetchAll() : $data = $stmt -> fetch();
		}catch(PDOException $ex)
		{
			print $ex->getMessage();
			exit();
		}
		return $data;
	}  
	

    /******************************************************************
		Name   : toLog - 紀錄操作資料至log_center
		Val    : 
				 @param  string  $web_type - 網站類型(自訂)
				 @param  string  $log_type -  執行動作(自訂) action || error || login 
				 @param  string  $content     - 內文(自訂)
				 @param  string  $class_path    - 程式與函式路徑(自訂)
				 @param  object $PDOLink -  PDO連線資料
		Creater: chg -2021/01/06
		Modify : 				  
	*******************************************************************/
	public static function toLog($web_type, $log_type, $content, $class_path,$PDOLink)
	{
		try {
			$sql="
			INSERT INTO log_center (web_type, log_type, content, class_path, add_date) 
			VALUES (:web_type, :log_type, :content, :class_path, now()); 
			";
			$param = array(
			":web_type" => $web_type,
			":log_type" => $log_type,
			":content" => $content,
			":class_path" => $class_path
			);
			$stmt = $PDOLink-> prepare($sql);
			$stmt -> execute($param);
			
		} catch(PDOException $e) {
			echo $sql . "<br>" . $e->getMessage();
		}
	}
	
    /******************************************************************
		Name   : getUserIP -  取得IP
		Val    : 
				 @return string  -連線IP
		Creater: chg -2021/01/11
		Modify : 				  
	*******************************************************************/
	public static function getUserIP()
	{
		$ip = "";
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else 
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}	
	
    /******************************************************************
		Name   : alertMsg - JS警告視窗
		Val    : 
				 @param  string  $msg   -  顯示文字
				 @param  string  $toPage -  導向的頁面
				 @param  boolean $jump -  是(true)||否(false)要導頁
				 @return   
		Creater: chg -2021/02/24
		Modify : 				  
	*******************************************************************/		
	public static function alertMsg($msg , $toPage, $jump)
	{
		if($msg)
		{
			if($jump)
			{
				echo  "<script type ='text/javascript'>alert('{$msg}'); ";
				echo "location.href = '{$toPage}'</script>";	
				exit();
			}else{
				echo  "<script type ='text/javascript'>alert('{$msg}'); </script>";
			}
		}
	}
	
 	/******************************************************************
		Name   : excSQLwithParam - 執行sql語法 或 回傳搜尋資料 
		Val    : 
				 @param  string 	   $type  -  sql動作	
				 @param  string      $sql_cmd  - sql條件
				 @param  array       $param_arr  - 帶入的參數陣列  
				 @param  boolean  $isAll  - true : fetchAll || false : fetch 
				 @param  object     $PDOLink -  PDO連線資料				 
				 @return  boolean || array  $result 執行結果 || 搜尋資料  ( if fetch() $result['data'] - 資料['欄位名'])  
		Use    :  
		Creater : chg -2021/04/21
		Modify : 
	*******************************************************************/	
 	public static function excSQLwithParam($type, $sql_cmd, $param_arr, $isAll, $PDOLink)
	{   
	    $stmt = null; 
		switch($type)
		{
			case 'select':
				try{ 
					$stmt = $PDOLink -> prepare($sql_cmd);
					$stmt -> execute($param_arr);
					($isAll) ? $result = $stmt -> fetchAll() : $result = $stmt -> fetch();
				}catch(PDOException $ex)
				{
					print $ex->getMessage();
					exit();
				} 
			break; 
			case 'insert':
			case 'update':
			case 'delete':
				$result = false;
				try{ 
					$stmt = $PDOLink -> prepare($sql_cmd);
					$result = $stmt -> execute($param_arr); 
				}catch(PDOException $ex)
				{
					print $ex->getMessage();
					exit();
				}			
			break;
		} 
		return $result;
	}
    /******************************************************************
		Name   :  getQueryRowCount  -  查詢的資料筆數
		Val    :  
				 @param  string    $sql_cmd  - sql條件
				 @param  array     $param_arr  - 帶入的參數陣列  
 				 @param  object 	 $PDOLink -  PDO連線資料
				 @return  string  $num  -  查詢的資料筆數
		Creater: chg -2021/04/29
		Modify : 				  
	*******************************************************************/			
	public static function getQueryRowCount($sql_cmd, $param_arr, $PDOLink)
	{
		$num = 0;
		try{ 
			$stmt = $PDOLink -> prepare($sql_cmd);
			if($param_arr)
			{		 
				$stmt -> execute($param_arr);
				$num = $stmt -> rowCount();  
			}else{
				$stmt -> execute();
				$num = $stmt -> rowCount();  	
			}
		}catch(PDOException $ex)
		{
			print $ex->getMessage();
			exit();
		} 
		return $num;
	}
    /******************************************************************
		Name   :  chkLockMode  -  判斷門鎖、鎖舌模式取得reader 
		Val    :  
				 @param  string    $doorlock  -  門鎖 : 自動: 00001010 ; 不自動: 00001000 ; bit2(由右到左)
				 @param  string    $latchmode  -  鎖舌: 自動: 00001100 ; 不自動: 00001000; bit3 (由右到左)
				 @param  string    $sleep  -  睡眠: 不睡: 00001000 ; 睡: 00011000; bit5 (由右到左)
				 @return  string  $reader  - 8bit轉成10進位的值
		Creater: chg -2021/07/12
		Modify : 2021/07/21 增加睡眠模式
	*******************************************************************/			
	public static function chkLockMode($doorlock = '', $latchmode = '', $sleep = '')
	{
		$reader = '';
		$bit = str_pad(0, 8, 0, STR_PAD_LEFT);
		$bit_arr= preg_split('//', $bit, -1, PREG_SPLIT_NO_EMPTY); //2進位toArray
		$bit_arr['3'] = $sleep; // 睡眠
		$bit_arr['4'] = 1; // bit4 固定1
		$bit_arr['6'] = $doorlock; // 門鎖
		$bit_arr['5'] = $latchmode; // 鎖舌
		$result_bit = implode($bit_arr); 
		$reader = base_convert($result_bit, 2, 10); // 2進位轉10進位 
		return $reader;
	}
    /******************************************************************
		Name   :  getLockModeStatus  - 讀取reader取得門鎖或鎖舌狀態
		Val    :   
				 @param  string    $reader  -  門鎖、鎖舌轉成10進位的值
				 @param  string    $doorlock  -  門鎖 : 1自動上鎖；0不自動上鎖 byte2 (由右到左)
				 @param  string    $latchmode  - 鎖舌: 1自動解鎖；0不自動解鎖 byte3 (由右到左)
				 @param string	   $bit_pos bit位置
				 @return  string     $set_mode  -  門鎖或鎖舌狀態
		Creater: chg -2021/07/13
		Modify : 				  
	*******************************************************************/			
	public static function  getLockModeStatus($reader, $bit_pos)
	{
		$set_mode = '';
		if($reader)
		{
			// $byte = base_convert($reader, 10, 2);
			$byte  = decbin($reader);  // 轉為2進位
			$reader_byte = str_pad($byte , 8, 0, STR_PAD_LEFT);  // 補零
			$reader_arr = preg_split('//', $reader_byte, -1, PREG_SPLIT_NO_EMPTY);//轉為 array
			$set_mode = $reader_arr[$bit_pos] ; 
		} 
		return $set_mode; 
	}	
    /******************************************************************
		Name   :  powerMode  - 房間收費設定模式
		Val    :   
				 @param  string    $mode  -   收費設定代號
				 @param  string    $mode_chn  -   收費設定中文
				 @return  string     $mode_chn   
		Creater: chg -2021/07/14
		Modify : 				  
	*******************************************************************/			
	public static function powerMode($mode = '')
	{
		$mode_chn = '';
		if($mode)
		{
			switch($mode)
			{
				case '1':
					$mode_chn = '計費';
					break;
				case '3':
					$mode_chn = '免費';
					break;
				case '4':
					$mode_chn = '停用';
					break;
			}
		} 
		return $mode_chn; 
	}
    /******************************************************************
		Name   :  roomSplit  - 房號床號切割
		Val    :   
				 @param  string    $room_num  -   房號加床號碼
				 @param  string    $type  -   需要房號或床號
				 @return  string     $result   
		Creater: chg -2021/07/16
		Modify : 				  
	*******************************************************************/			
	public static function roomSplit($num='', $type ='')
	{
		$result = ''; 
		$str_arr = '';
		$room_split = substr(chunk_split($num, 5,"_"), 0, -1);
		$str_arr = explode("_", $room_split); 
		switch($type)
		{
			case 'room':
				$result = $str_arr[0];
				break;
			case 'berth':
				$result = $str_arr[1];
				break;
		} 
		return $result; 
	}
    /******************************************************************
		Name   :  chkAdminLevel  - 檢查是否為最高管理員
		Val    :   
				 @param  string     $user_id  -   $_SESSION['admin_user']['id'] 
				 @param  object 	 $PDOLink -  PDO連線資料
				 @return  boolean  $result  - true 為最高管理員 |  false 
		Creater: chg -2021/07/22
		Modify : 				  
	*******************************************************************/			
	public static function chkAdminLevel($user_id, $PDOLink)
	{
		$result = false; 
		if($user_id)
		{
			$sql = "SELECT id FROM member WHERE id =? AND del_mark=0 AND group_id REGEXP '1' ";
			$data = self::excSQLwithParam('select', $sql, array($user_id), false, $PDOLink);
			if($data['id']) $result = true;
		}
		return $result; 
	}
	 
    /******************************************************************
		Name   :  insertSystemSetting  - 寫入system_setting
		Val    :   
				 @param  string     $hw_cmd  -   system_setting 語法
				 @param  object 	$PDOLink -  PDO連線資料  
		Creater: chg -2021/07/30
		Modify : 用同一筆連線
	*******************************************************************/				
	public static function insertSystemSetting($hw_cmd, $PDOLink ) 
	{ 
		$c_code  = json_encode($hw_cmd); 
		$sql = "
					INSERT INTO `system_setting` (`title`, `computer_name`, `c_code`, `M0`, `M1`, `M2`, `M3`, `add_date`) 
					VALUES ('', 'Web', ?, '0', '0', '0', '0', NOW())
					"; 
		$inserted =	self::excSQLwithParam('insert', $sql, array($c_code), false, $PDOLink); 
	}	
    /******************************************************************
		Name   :  kitSettingChange  - 
		Val    :   
				 @param  string     $kit_area_id  -  廚房房間id
				 @param  array      $sel_area  -  選取要修改的廚房
				 @param  boolean     $change -  true || false 有修改或沒修改
		Creater: chg -2021/08/04
		Modify : 	  
	*******************************************************************/				
	public static function kitSettingChange($kit_area_id='', $sel_area) 
	{ 
	    $change = false;
		if(in_array($kit_area_id, $sel_area))
		{
			$change = true;
		}
		return $change;
	}
    /******************************************************************
		Name   :  removeUtf8Char4bytes- 移除utf8 字串裡中文大於4字元的中文字
		Val    :  @param  string  $str  -  字串
					@return  string  $result  - 移除後的字串
		Creater: chg -2021/09/17
		Modify : 	 註:大於4 insert 時報錯, 如能將mysql db utf8改為允許大於4字元的
		編碼就不用用這個方法。
	*******************************************************************/	
	public static function removeUtf8Char4bytes($str) 
	{ 
		if($str)
		{
			$result = '';
			$split_cn = preg_split('~(?<=.)(?=.)~u', $str);
			foreach($split_cn as $v)
			{
				$v = (strlen($v) > 3)? 'O' : $v ; // 以O取代
				$result .= $v;
			}
		}
		return $result;
	}	
    /******************************************************************
		Name   :  memberCardLog  卡號使用紀錄
		Val    :  @param  string  $member_id  -   table: member -> id
					@param  string  $card  -  卡號
					 @param  object $PDOLink -  PDO連線資料  
		Creater: chg -2021/09/27
		Modify : 	  
	*******************************************************************/	
	public static function memberCardLog($member_id, $card, $mod_type, $PDOLink) 
	{  
		if($member_id && ($card != '0000000000') && ($card !='') && is_numeric($card))
		{
			$sql = "SELECT count(*) as total FROM member_card_log WHERE  member_id = ? AND card = ?  ";
			$data = self::excSQLwithParam('select', $sql, array($member_id, $card), false, $PDOLink); 
			if($data['total'] == 0) // 未紀錄的卡號
			{
				$sql = "INSERT INTO member_card_log(member_id, card, mod_type, add_date) VALUES(?,?,?,NOW()); ";
				$inserted = self::excSQLwithParam('insert', $sql, array($member_id, $card, $mod_type), false, $PDOLink); 
			}
		}
	}	 
	
    /******************************************************************
		Name   :  yearOption  查詢條件-年分選項
		Val    :  @param  string $form_name - $_GET['xxx']	-  表單中name屬性，也可用變數放入，EX.$yyy=$_GET['xxx']
		Creater: yung -2023/02/21
		Modify : $opt_start，系統實際上架年份(西元年)為初始值 
	*******************************************************************/	
	public static function yearOption($form_name){
		$opt_start=2020;
		for($i=date('Y'); $i>=$opt_start; $i--) { 
			if(empty($form_name))
			{
				$selected = (date('Y')==$i)? 'selected' : ''; 
			}else{
				$selected = ($form_name==$i)? 'selected' : ''; 
			}  
			print "<option value={$i} {$selected}>{$i}</option>";
		}
	}


	
}

?>