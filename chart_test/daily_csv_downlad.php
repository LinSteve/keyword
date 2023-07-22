<?php
ini_set("display_errors", 1);

error_reporting(E_ALL ^ E_NOTICE);

error_reporting(E_ALL ^ E_WARNING);

	require_once('../config/db.php');
	
	
//	include('../chk_log_in.php');
	
//	ini_set('max_execution_time', 0); // 最大連線逾時時間 (在php安全模式下無法使用)	 
//	set_time_limit(0);
	// ini_set("memory_limit","2048M");	
	//获取网址
	if($_SERVER["SERVER_PORT"]=="80" or $_SERVER["SERVER_PORT"]==""){
		$url=$_SERVER['SERVER_NAME'];
	}else{
		$url=$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"];
	}
	// 輸入日期，開始結束
	$sql_kw   = "";
	$now_time = date('Y-m-d H:i:s');
	$end_date  = date('Y-m-d');
//	$end_date = date('Y-m-d',strtotime($_GET['st_date']."+1 day"));
	// 給初值
	isset($_GET['keyword']) ? $keyword  = $_GET['keyword'] : $keyword = '';
	if( $keyword ) { $sql_kw .= " AND ticker = '{$keyword}' "; }
	if( isset($_GET['st_date']) && !empty($_GET['st_date']) ) 
	{
		$start_date  = date('Y-m-d', strtotime($_GET['st_date']));
	//	$sql_dt .= " AND res.add_date > '{$s_time}' "; 
	}
			
	
/*	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'enable'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$enable_arr = $rs->fetchAll();
	$enable_map = array();
	
	foreach($enable_arr as $v) {
		$enable_map[$v['custom_id']] = $v['custom_var'];
	}
*/	
	$param_st = array(
	// 抓開始~結束日的時間
		":start_date"=> $start_date." 00:00:00",
		":end_date"=> $end_date." 00:00:00"
	); 
	
//	$sql = "SELECT * FROM `daily_2330` WHERE ddate >= '".$param_st[':start_date']."' and ddate <= '".$param_st[':end_date']."'".$sql_kw." ORDER BY ddate asc";
	$sql = "Select ddate, open, high, low, close, volume from `daily_2330` order by ddate asc";
	$rs   = $PDOLink->query($sql);
	$data = $rs->fetchAll();
	
	if($data) {
		
		$body_head = array("Date", "Open", "High", "Low", "Close", "Volume");
		
	//	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	//	$xls = $spreadsheet->getActiveSheet();
		
		$xls_filename = 'export_'.date('Y-m-d').'.csv'; // Define Excel (.xls) file name
		// Header info settings
		header("Content-Type: application/csv");
		header("Content-Disposition: attachment; filename=$xls_filename");
		header("Pragma: no-cache");
		header("Expires: 0");
		 
		/***** Start of Formatting for Excel *****/
		// Define separator (defines columns in excel &amp; tabs in word)
	//	$sep = "\t"; // tabbed character
		$sep = ","; // tabbed character
		 
		// Start of printing column names as names of MySQL fields
	//	for ($i = 0; $i<mysql_num_fields($result); $i++) {
		foreach($body_head as $i => $v) {
		//	$filedname = mysql_field_name($result, $i);
			if($i==0){
				$name = "Date";
				echo $name . $sep;
			}elseif($i==1){
				$name = "Open";
				echo $name . $sep;
			}elseif($i==2){
				$name = "High";
				echo $name . $sep;
			}elseif($i==3){
				$name = "Low";
				echo $name . $sep;
			}elseif($i==4){
				$name = "Close";
				echo $name . $sep;
			}else{
				$name = "Volume";
				echo $name . $sep;
			}
		}
		print("\n");
		
	//	foreach($data as $k => $row) {
		while($row = mysql_fetch_array($data)) {
			
			$schema_insert = "";
			  for($j=0; $j<mysql_num_fields($data); $j++)
			  {
				if(!isset($row[$j])) {
				  $schema_insert .= "NULL".$sep;
				}elseif ($row[$j] != "") {
					if($j==999){
					}else{
						if($j==0){//申請日期
							$start_date = $row[$j];
							$s_time  = date('Y-m-d', strtotime($start_date));
							$schema_insert .= "$s_time".$sep;
						}elseif($j==1){//會員代碼
							$schema_insert .= "$row[$j]".$sep;
						}elseif($j==2){//會員/名稱
							$schema_insert .= "$row[$j]".$sep;
						}elseif($j==3){//會員/狀態
							$schema_insert .= "$row[$j]".$sep;
						}elseif($j==4){//上1會員代碼
							$schema_insert .= "$row[$j]".$sep;
						}else{//上4會員/狀態
							$schema_insert .= "$row[$j]".$sep;
						}
					}
				}
				else {
				  $schema_insert .= "".$sep;
				}
			  }
			  $schema_insert = str_replace($sep."$", "", $schema_insert);
			  $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			  $schema_insert .= "\t";
			  print(trim($schema_insert));
			  print "\n";
		}
		
	} else {
		header('Location: ../daily_csv.php?error=5');
	}
?>