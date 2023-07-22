<?php	
header("Content-type:text/html;charset=utf-8");

ini_set("display_errors", 1);

error_reporting(E_ALL ^ E_NOTICE);

error_reporting(E_ALL ^ E_WARNING);
	
		/***** EDIT BELOW LINES *****/
		$DB_Server = "db01.coowo.com"; // MySQL Server
		$DB_Username = "keyword"; // MySQL Username
		$DB_Password = "A0975382327z!@#$"; // MySQL Password
		$DB_DBName = "keyworddb"; // MySQL Database Name
		$DB_TBLName = "daily_2330"; // MySQL Table Name
		$xls_filename = 'export_'.date('Y-m-d').'.csv'; // Define Excel (.xls) file name
	 
		/***** DO NOT EDIT BELOW LINES *****/
		// Create MySQL connection
		$st_date = $_GET['st_date'];
		$end_date = $_GET['end_date'];
		$keyword = $_GET['keyword'];
		$sql = "Select ddate, open, high, low, close, volume from $DB_TBLName order by ddate asc";
		$Connect = @mysql_connect($DB_Server, $DB_Username, $DB_Password) or die("Failed to connect to MySQL:<br />" . mysql_error() . "<br />" . mysql_errno());
		mysql_query("SET NAMES 'big5'");
		// Select database
		$Db = @mysql_select_db($DB_DBName, $Connect) or die("Failed to select database:<br />" . mysql_error(). "<br />" . mysql_errno());
		// Execute query
		$result = @mysql_query($sql,$Connect) or die("Failed to execute query:<br />" . mysql_error(). "<br />" . mysql_errno());
		 
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
		for ($i = 0; $i<mysql_num_fields($result); $i++) {
			$filedname = mysql_field_name($result, $i);
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
		while($row = mysql_fetch_array($result))
		{
		  $schema_insert = "";
		  for($j=0; $j<mysql_num_fields($result); $j++)
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
?>