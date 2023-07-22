<!-- nav.php -->

<header id='header'>
		<nav class='left'>
			<!--<a href='#menu'><span>Menu</span></a>-->
			<a href='#menu' class='for_mobile'><span class='glyphicon glyphicon-option-horizontal' style='font-size:42px'></span></a> 
		</nav>
		<!-- LOGO -->
	<!--	<a style='color: #fff;' href='index.php' class='logo'>
			
			<img class='school_image' src='img/logo.png'>  
			 ".$lang->line("index.scllo_title")." -->
		</a>
		
<?php

	print " 
	</header>
	<nav id='menu'> 
		<!--<a href='#menu'><span class='glyphicon glyphicon-option-horizontal' style='font-size:42px; margin-top:-30px; color: #066'></span></a>-->
		<ul class='links'>";
   	    	print "
				<li><a href='update_keyword.php'><span class='fas fa-refresh'></span>&nbsp;&nbsp;&nbsp;".$lang->line("index.keyword_update")." POST</a></li>
				<hr class='hr-style'>
			<!--	<li><a href='getkeyword.php'><span class='fas fa-cloud'></span>&nbsp;&nbsp;".$lang->line("index.keyword_query")."</a></li> 
				<hr class='hr-style'>-->
				
				<li><a href='index.php'><span class='fas fa-file'></span>&nbsp;&nbsp;&nbsp;".$lang->line("index.keyword_report")." GET</a></li>
				<hr class='hr-style'>   
				 
				<li><a href='query_keyword.php'><span class='fas fa-question-circle'></span>&nbsp;&nbsp;".$lang->line("index.keyword_query")." 依日期</a></li>
				<hr class='hr-style'>
				
				<li><a href='daily_stock_json.php'><span class='fas fa-chart-line'></span>&nbsp;&nbsp;".$lang->line("index.keyword_test")." K線圖</a></li>
				<hr class='hr-style'>
				
				";
			print "
				
				</ul>";
				print "<ul class='actions vertical '>";
			//側選單登入適用手機板
	        

	print " </ul>
	</nav>";

?>