<?php 
	ini_set("display_errors", 1);

	error_reporting(E_ALL ^ E_NOTICE);

	error_reporting(E_ALL ^ E_WARNING);
	ob_start();
	include('header_layout.php');
	include('nav.php');
//	include('chk_log_in.php');  
	ini_set('max_execution_time', 0); // 最大連線逾時時間 (在php安全模式下無法使用)	 
	set_time_limit(0);
	//获取网址
	if($_SERVER["SERVER_PORT"]=="80" or $_SERVER["SERVER_PORT"]==""){
		$url=$_SERVER['SERVER_NAME'];
	}else{
		$url=$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"];
	}
	// ini_set("memory_limit","2048M");	
	// 輸入日期，開始結束
	$sql_kw   = "";
	$now_time = date('Y-m-d H:i:s');
	$st_date  = $_GET['st_date'];
	$end_date = date('Y-m-d',strtotime($_GET['st_date']."+1 day"));
	isset($_GET['keyword']) ? $keyword  = $_GET['keyword'] : $keyword = '';
	// 給初值
	if($st_date == "") $st_date = date('Y-m-d');
	if($keyword)     { $sql_kw .= " AND keyword = '{$keyword}' "; }
	

	if($_GET['search']){
		$param_st = array(
			// 抓開始~結束日的時間
			":st_date"=> $st_date." 00:00:00",
			":end_date"=> $end_date." 00:00:00",
			":dong" => $dong
		); 
		$total_count = 0;
		$inner_html = "";
		$sql = "SELECT id, add_date, sno, keyword, jsondata_path FROM `twcms_keywordpost` where add_date >= '".$param_st[':st_date']."' and add_date <= '".$param_st[':end_date']."'".$sql_kw." ORDER BY add_date asc,sno asc";
	//	echo $sql;
		
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$keyword_log_arr  = $rs->fetchAll();
		if($keyword_log_arr) {
				foreach($keyword_log_arr as $r) {
					$add_date = $r['add_date']; //關鍵字 日期
					$total_count += 1; // 關鍵字個數
					$keyword	 = $r['keyword'];
					$jsondata_content 	 = $r['jsondata_content'];
					$jsondata_path 		 = $r['jsondata_path'];
					$inner_html .=" 
					<div class='col-lg-4 card-group'>
						<div class='card mb-4 card-green text-green fz-18 h-auto'>
							<div class='py-2 nowsystem'>
								<ul class='px-1'>
									<li >日  期：<span id ='st_time'>{$add_date}</span></li>
									<li >關鍵字：<span id ='ed_time'>{$keyword}</span> </li>
									<li class='total-meter' ><span id ='total'><a href="."http://{$url}/tree_test/index.php?jsonfilename={$jsondata_path}&pgm_return=2 target='_parent' style='color:#c30008;'>樹狀圖</a></span></li>
								</ul>
							</div>
						</div>
					</div>	 
					";
				}  	
		} else {
			die(header("Location: query_keyword.php?error=5&st_date=".$st_date."&keyword=".$keyword."&end_date=".$end_date));  
		}
	}
?> 
 <section id="main" class="wrapper">
<!--	<div class='col-12 btn-back'><a href='powersearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>-->

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">關鍵字查詢</h1>
    </div>
	
	<div class="row mar-center2 mb-4" style="margin: 0 auto;">
		<?php if($_GET['error'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>開始月份無資料</strong>
			</div>
		<?php } elseif ($_GET['error'] == 2) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>結束月份無資料</strong>
			</div>			
		<?php } elseif ($_GET['error'] == 3) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>請勿空白</strong>
			</div>
		<?php } elseif ($_GET['error'] == 4) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>年/月份錯誤</strong>
			</div>
		<?php } elseif ($_GET['error'] == 5) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>查無資料</strong>
			</div>	
		<?php } elseif ($_GET['error'] == 6) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>無資料匯出</strong>
			</div>			
		<?php } elseif ($_GET['success']) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
			<strong>Success 成功設置！！</strong>
			</div>
		<?php } ?>
	</div>
	

	<!-- 查詢&顯示結果 -->
	<div class="container">
			<form id='mform2' method="get" class='col-12'>
					<div class="form-group row select-mar3">
						<label class="col-sm-2 col-form-label label-right">輸 入 日 期</label>
						<div class="col-sm-9"> 
							<input class="form-control form-control2" type="date" placeholder="yyyy-mm-dd" size="20" name="st_date" value="<?php echo $st_date ?>">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label label-right"><?php echo $lang->line("index.keyword_query");?></label>	
						<div class="col-sm-9 form-inline">
							
							 <div class='col-sm-9'> 
								 <input  type='text' class='form-control' name='keyword' placeholder='輸入關鍵字' value='<?php if(isset($_GET['keyword'])){ echo $_GET['keyword'];}else{ echo '';} ?>'>
							 </div>
						</div>
					</div>
					<br>
					<input type='hidden' id='end_date' name='end_date'  value=''>
					<input type='hidden' name='search' value='1'>
					<div class='col-12'>
						<button type='submit' id="search-btn" class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
					</div>
			</form>
			<?php 
				if($_GET['search'] == 1) {
					print 	"<div class='col-12 mt-4'>";
				} else {
					print 	"<div class='col-12 mt-4' style='display:none'>";
				}
			?>
	
			<h1 class="jumbotron-heading text-center h1-mar">查詢結果</h1>
			<h5 class="text-gray-900 font-weight-bold">查詢時間:<?php echo $now_time ?></h5>
			<div id="power-total" class="col-12 alert alert-info text-green">總計關鍵字：<?php echo $total_count ?>個</div>
			<div class="row">
				<?php echo $inner_html; ?>
			</div>
	</div>
	<!-- 查詢&顯示結果 END-->

</section>


<?php include('footer_layout.php'); ?>