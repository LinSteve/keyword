<?php 
	include_once('header_layout.php');
	include_once('nav.php');
//	include('chk_log_in.php');  

	ini_set("display_errors", 1);

	error_reporting(E_ALL ^ E_NOTICE);

	error_reporting(E_ALL ^ E_WARNING);
	//获取网址
	if($_SERVER["SERVER_PORT"]=="80" or $_SERVER["SERVER_PORT"]==""){
		$url=$_SERVER['SERVER_NAME'];
	}else{
		$url=$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"];
	}

	if(isset($_GET['search'])){$search  = $_GET['search'];}else{$search  = '9';};

	$sql = "SELECT id, add_date, sno, keyword, jsondata_path FROM `twcms_keywordpost` ORDER BY add_date desc,sno asc LIMIT 5";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$keyword_log_arr  = $rs->fetchAll();
	
	$now_time = date('Y-m-d H:i:s');

?>
<!-- 輸入關鍵字首頁  -->  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $(document).ajaxStart(function(){
        $("#wait").css("display", "block");
    });
    $(document).ajaxComplete(function(){
        $("#wait").css("display", "none");
    });
    $("button").click(function(){
		var e = document.getElementById("keyword");
		var value = e.value;
		if( value == '' )
		{
			alert("請輸入關鍵字!");
			document.mform1.keyword.focus()
			return false;
			
		}
		$('#mform1').submit();
        $("#txt").load("update_keyword.php");
    });
});
</script>
<body>
<div id="txt">
<section id="main" class="wrapper">
	

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">關鍵字更新</h1>
    </div>

	<div class="inner">
		<div class="row">
			<form id='mform1' action="tree_test/test_updatekeyword2.php" method="post" class='col-12'>
		<!--	<form id='mform1' action="http://crazy.lgood94.com.tw/test_updatekeyword2.php" method="post" class='col-12'>-->
					<div class='col-12'>
						<section class='panel panel-noshadow'>
	             			<div class='panel-body'>
							 
							 <div class='form-group row '>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.keyword_input");?></label>
							 <div class='col-sm-9'> 
								 <input  type='text' class='form-control' name='keyword' id='keyword' placeholder='輸入關鍵字組(如:台積電,股票)' value=''>
							 </div>
						 	</div>


  
							<br><br>
						<!--	<input type='hidden' name='search' value='1'>
							<input type='hidden' name='status' value='9'>-->
							<button type='button' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm") ?></button>
	             			</div>
	             		</section>
	             	</div>
				</form>
			
		</div>
	</div>
<!-- SEARCH END-->

<!--表格 -->

<div class='inner3' style="display:<?php echo ($search != '') ? 'block' : 'none'; ?>">
	<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($search != '') ? "關鍵字組更新(POST)" : "" ; ?></h1>
	<div class="col-12">
					<div class="col-12 alert alert-info d-inline-block">
						<h4 class="mb-0">以下只列出最近五個POST的關鍵字</h4>
					</div>
					<h5 class="text-gray-900 font-weight-bold">目前時間:<?php echo $now_time ?></h5>
				  	<div id="nested-table-custom" class="col">
						<table class="table text-center font-weight-bold" data-toggle="table">
						  <thead class="thead-green">
						    <tr class="text-center">
							  <th scope="col">#</th>
							  <th scope="col">日期</th>
							<!--  <th scope="col">項次</th> -->
							  <th scope="col">關鍵字</th>
							  
						      <th scope="col">查看樹狀圖</th> 
						<!--      <th scope="col">查看Jsondata</th> -->
						     
						    </tr>
						  </thead>
						  <tbody>
							<?php 
								foreach($keyword_log_arr as $k=>$row) 
								{
							?>
								<tr>
									<td scope='col'><?php echo $k+1; ?></td>
									<td scope='col'><?php echo $row["add_date"]; ?></td>
								<!--	<td scope='col'><?php echo $row["sno"]; ?></td>-->
									<td scope='col'><?php echo $row["keyword"]; ?></td>
									<td scope='col'><a href="http://<?php echo $url; ?>/tree_test/index.php?jsonfilename=<?php echo $row['jsondata_path']; ?>" target="_parent" style="color:#c30008;">查看D3.js樹狀圖</a></td>
								<!--	<td scope='col'><a href="http://<?php //echo $url; ?>/json_view.php?id=<?php //echo $row['id']; ?>" target="_parent" style="color:#0000ff;">查看Jsondata</a></td>-->
									
								</tr>
							<?php
								}
						    ?>
						  </tbody>
						</table>
					</div>

				</div>
	</div>

<!--表格 END-->
</section>
</div>
<div id="wait" style="display:none;width:130px;height:89px;position:absolute;top:0%;left:50%;padding:20px;"><img src="./loader.gif" width="64" height="64" /><br/>請稍候...</div>
</body>

<?php  require_once('footer_layout.php'); ?>