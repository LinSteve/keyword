<?php
define('CONFIG_PATH', __DIR__);
require_once(CONFIG_PATH . "/class.func.php");
$web_title = "關鍵字平台 ";

ini_set("display_errors", false); 								          //顯示 Error, true => 開, false => 關
error_reporting(E_ALL^E_NOTICE^E_WARNING);

date_default_timezone_set("Asia/Taipei");   					    //時區(亞洲/台北)
session_cache_expire(28800);											        //session逾時設定; 
ini_set('session.gc_probability',100); 	

session_start();
ob_start();								    					                  //可以解決header有先送出東西的問題
ob_end_clean();							    					                //先ob_start 再進行一次ob_end_clean

header("Cache-Control:no-cache,must-revalidate");   			//強迫更新
header("P3P: CP=".$_SERVER["HTTP_HOST"]."");        			//解決在frame中session不能使用的問題，可填ip或是domain
header('Content-type: text/html; charset=utf-8');				  //指定utf8編碼 
header('Vary: Accept-Language');

$PDOLink = db_conn();

function db_conn() 
{	
	$PDOLink;
	$PDOHostVar       = 'localhost';
	$PDODBnameVar     = 'keyworddb';
	$PDODBuserVar     = 'root';
	$PDODBpasswordVar = 'A0975382327z!@#$';
	
	try {   
		$PDOLink = new PDO("mysql:host={$PDOHostVar};dbname={$PDODBnameVar}",$PDODBuserVar,$PDODBpasswordVar);  
		$PDOLink->query("SET NAMES 'utf8mb4'");
		$PDOLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		print "連線失敗" . $e->getMessage(); 
	}
	
	return $PDOLink;
}

function get_log_list($content)
{
	/* 使用資料庫 */
    // $PDOLink = new PDO('mysql:host=localhost;dbname=ndhu_db','andy','aotech2018');  
	$PDOLink = db_conn();
    $PDOLink->query("SET NAMES 'utf8'");

    /* insert log history */
    //$date = date('d.m.Y h:i:s');
    $date = date("Y-m-d H:i"); 
     
    /* error_log php function */
    error_log($content."\n", 3, "/var/tmp/my-errors.log", MAIL_ACCOUNT);

        /* log db save */
        try {
            $col="`content`,`data_type`,`add_date`";
            $col_data="'".$content."','1','".$date."' ";
            $ins_q="insert into log_list (".$col.") values (".$col_data.") ";
            $PDOLink->exec($ins_q); 
        }

        catch(PDOException $e){
           echo $ins_q . "<br>" . $e->getMessage();
        }

}

// -- 20200707
function insert_system_setting_for_dong($hw_cmd, $dong) 
{	
	$PDOLink = db_conn();
	$nowtime = date('Y-m-d H:i:s');
	
	$sql = "SELECT `name` FROM `host` WHERE dong = '{$dong}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$temp_arr = $rs->fetchAll();
	$dong_arr = array();
	
	foreach($temp_arr as $v) {
		$dong_arr[] = $v['name'];
	}

	$c_code  = json_encode($hw_cmd);

	$sql_hw  = "INSERT INTO `system_setting` (`title`, `computer_name`, `c_code`, `M0`, `M1`, `M2`, `M3`, `add_date`) 
				VALUES ('', 'Web', '{$c_code}', 
				'".(in_array("M0", $dong_arr) ? "0" : "1")."', 
				'".(in_array("M1", $dong_arr) ? "0" : "1")."', 
				'".(in_array("M2", $dong_arr) ? "0" : "1")."', 
				'".(in_array("M3", $dong_arr) ? "0" : "1")."', '{$nowtime}')";
	
	$PDOLink->exec($sql_hw);
}

function insert_system_setting($hw_cmd) 
{
	
	$PDOLink = db_conn();
	
	$c_code  = json_encode($hw_cmd);
	$nowtime = date('Y-m-d H:i:s');
	$sql_hw  = "INSERT INTO `system_setting` (`title`, `computer_name`, `c_code`, `M0`, `M1`, `M2`, `M3`, `add_date`) 
				VALUES ('', 'Web', '{$c_code}', '0', '0', '0', '0', '{$nowtime}')";
	$PDOLink = db_conn();
	$PDOLink->exec($sql_hw);
}
function toLog($data_type, $content, $PDOLink)
{
	$sql="
		INSERT INTO log_list (content, data_type, add_date) 
		VALUES (:content, :data_type, now()); 
	";
	$param = array(
	":content" => $content,
	":data_type" => $data_type
	);	
	
	try {
		$stmt = $PDOLink -> prepare($sql);
		$stmt -> execute($param);
		
	} catch(PDOException $e) {
		echo $e->getMessage();
		exit();
	} 	
}
function alertMsg($msg , $toPage, $jump)
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
?>