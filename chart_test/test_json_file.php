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
	$sql_dt   = "";
	$now_time = date('Y-m-d H:i:s');
	// 每日用電:輸入日期，開始結束都抓同天
	if( isset($_GET['end_date']) ){ $end_date = $_GET['end_date']; }else{ $end_date  = date('Y-m-d'); }
	if( isset($_GET['end_date']) && !empty($_GET['end_date']) ) 
	{
		$start_date  = date('Y-m-d',strtotime($end_date."- 1 year"));
		$sql_dt .= " AND ddate >= '{$start_date}' "; 
	}
	isset($_GET['keyword']) ? $keyword  = $_GET['keyword'] : $keyword = '';
	if( $keyword ) { $sql_kw .= " AND ticker = '{$keyword}' "; }
			
	
	$param_st = array(
	// 抓開始~結束日的時間
		":start_date"=> $start_date." 00:00:00",
		":end_date"=> $end_date." 00:00:00"
	); 
	
	$sql = "SELECT * FROM `daily` WHERE ddate >= '".$param_st[':start_date']."' and ddate <= '".$param_st[':end_date']."'".$sql_kw." ORDER BY ddate asc";
//	$sql = "Select * from `daily_2330` order by ddate asc";
	$rs   = $PDOLink->query($sql);
	$data = $rs->fetchAll();
	
	if($data) {
		$response['Data'] = array();
		$product = array();
		$response['Title'] = array("日期", "股票代號", "股票名稱", "開盤價", "最高價", "最低價", "收盤價", "成交量");
	//	array_push($response['Title'], $product);
		//	$name = iconv("BIG5","UTF-8", $_GET['stock_name']);
	//	$name = iconv("BIG5","UTF-8", "台積電");
		$name = $_GET['title'];
		foreach($data as $k => $row) {
			$now_time = date('Ymd',strtotime($row["ddate"]));
			
			$product = array($now_time, $row["ticker"], $name, $row["open"], $row["high"], $row["low"], $row["close"], $row["volume"]);
			array_push($response['Data'], $product);
			
		}
		$json_data = json_encode($response);
		$json_file_name = "f";
		$json_file_name = $json_file_name.rand(00000000, 99999999) . "." . "json";
		$json_file_name = "json_data/".$json_file_name;
	//	echo $json_file_name;
		file_put_contents($json_file_name, $json_data);
		
		$json_file_name = "../".$json_file_name;
		
		header('Location: Candlestick2/index.php?title='.$name.'&json_file='.$json_file_name);
		
	/*	echo "<pre>"; 			
		print_r($response);   
		echo "<pre/>";  	
		echo "<br/>"; 	
		echo "<pre>";		
		echo var_dump($json_data);    
		echo "<pre/>";
		echo "<br/>"; 	
		echo "<pre>";		
		print_r($json_data);    
		echo "<pre/>";*/
	} else {
		header('Location: ../daily_stock_json.php?error=5');
	}
?>