<?php
	$title = $_GET['title'];//2018/12/09 亞泥, Inc. (1104)
	$json_file = $_GET['json_file'];
?>
<!DOCTYPE html>
<meta charset="utf-8">

<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <button onclick="javascript:location.href='../../daily_stock_json.php'">Back</button>
        <p>關鍵字資訊標記： <?php echo $title;?></p>
        <p>K線圖</p>
    <!--    <p>時間：2018/11/07 ~ 2019/01/30</p>
        <p>深藍線: 5MA</p>
        <p>淺藍線: 20MA</p>
        <p>橘線：  60MA</p>
        <p>灰色Bar: 成交量</p>-->
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="techanjs.js"></script>
<!--    <script type="text/javascript" src="app.js"></script>-->
    <script type="text/javascript">
		var margin = {top: 20, right: 50, bottom: 30, left: 50},
				width = 960 - margin.left - margin.right,
				height = 500 - margin.top - margin.bottom;

		var parseDate = d3.timeParse("%Y%m%d");

		var x = techan.scale.financetime()
				.range([0, width]);
		var crosshairY = d3.scaleLinear()
				.range([height, 0]);

		var y = d3.scaleLinear()
				.range([height - 60, 0]);

		var yVolume = d3.scaleLinear()
				.range([height , height - 60]);


		var sma0 = techan.plot.sma()
				.xScale(x)
				.yScale(y);

		var sma1 = techan.plot.sma()
				.xScale(x)
				.yScale(y);
		var ema2 = techan.plot.ema()
				.xScale(x)
				.yScale(y);
		var candlestick = techan.plot.candlestick()
				.xScale(x)
				.yScale(y);


		var volume = techan.plot.volume()
				.accessor(candlestick.accessor())
				.xScale(x)
				.yScale(yVolume);
		var xAxis = d3.axisBottom()
				.scale(x);

		var yAxis = d3.axisLeft()
				.scale(y);
		var volumeAxis = d3.axisRight(yVolume)
				.ticks(3)
				.tickFormat(d3.format(",.3s"));
		var ohlcAnnotation = techan.plot.axisannotation()
				.axis(yAxis)
				.orient('left')
				.format(d3.format(',.2f'));
		var timeAnnotation = techan.plot.axisannotation()
				.axis(xAxis)
				.orient('bottom')
				.format(d3.timeFormat('%Y-%m-%d'))
				.translate([0, height]);

		var crosshair = techan.plot.crosshair()
				.xScale(x)
				.yScale(crosshairY)
				.xAnnotation(timeAnnotation)
				.yAnnotation(ohlcAnnotation)

				.on("move", move);
		var textSvg = d3.select("body").append("svg")
				.attr("width", width + margin.left + margin.right)
				.attr("height", margin.top + margin.bottom)
				.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		var svgText = textSvg.append("g")
					.attr("class", "description")
					.append("text")
		//            .attr("x", margin.left)
					.attr("y", 6)
					.attr("dy", ".71em")
					.style("text-anchor", "start")
					.text("");

		var svg = d3.select("body").append("svg")
				.attr("width", width + margin.left + margin.right)
				.attr("height", height + margin.top + margin.bottom)
				.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		var dataArr;

		d3.json("<?php echo $json_file;?>", function(error, data) {
			var accessor = candlestick.accessor();
			var jsonData = data["Data"];
			
			data = 
				jsonData
				.map(function(d) {
				return {
					date: parseDate(d[0]),
					open: +d[3],
					high: +d[4],
					low: +d[5],
					close: +d[6],
					volume: +d[7]/*,
					change: +d[7],
					percentChange: +d[8],
					fiveMA: +d[10],
					twentyMA: +d[11],
					sixtyMA: +d[12]*/
				};
			}).sort(function(a, b) { return d3.ascending(accessor.d(a), accessor.d(b)); });
			
			svg.append("g")
					.attr("class", "candlestick");
			svg.append("g")
					.attr("class", "sma ma-0");
			svg.append("g")
					.attr("class", "sma ma-1");
			svg.append("g")
					.attr("class", "ema ma-2");
			svg.append("g")
					.attr("class", "volume");
			svg.append("g")
					.attr("class", "volume axis");
			
			svg.append("g")
					.attr("class", "x axis")
					.attr("transform", "translate(0," + height + ")");

			svg.append("g")
					.attr("class", "y axis")
					.append("text")
					.attr("transform", "rotate(-90)")
					.attr("y", 6)
					.attr("dy", ".71em")
					.style("text-anchor", "end")
					.text("Price ($)");
			svg.append('text')
						.attr("x", 380)
						.attr("y", 15)
						.text("<?php echo $title;?>");
			
			
			// Data to display initially
			draw(data.slice(0, data.length));
			// Only want this button to be active if the data has loaded
			d3.select("button").on("click", function() { draw(data); }).style("display", "inline");
		});

		function draw(data) {
		//   console.log(data); 
			x.domain(data.map(candlestick.accessor().d));
			y.domain(techan.scale.plot.ohlc(data, candlestick.accessor()).domain());
			dataArr = data;
			
			svg.selectAll("g.x.axis").call(xAxis.ticks(7).tickFormat(d3.timeFormat("%m/%d")).tickSize(-height, -height));
			svg.selectAll("g.y.axis").call(yAxis.ticks(10).tickSize(-width, -width));
			yVolume.domain(techan.scale.plot.volume(data).domain());
			var volumeData = data.map(function(d){return d.volume;});
			svg.append("g")
				.attr("class", "crosshair")
				.call(crosshair)
			
			svg.select("g.volume").datum(data)
				.call(volume);
			
			var state = svg.selectAll("g.candlestick").datum(data);
			state.call(candlestick);
			
			
			svg.select("g.sma.ma-0").datum(techan.indicator.sma().period(10)(data)).call(sma0);
			svg.select("g.sma.ma-1").datum(techan.indicator.sma().period(20)(data)).call(sma0);
			svg.select("g.ema.ma-2").datum(techan.indicator.sma().period(50)(data)).call(sma0);

			svg.select("g.volume.axis").call(volumeAxis);

		}


		function move(coords) {
			var i;
			for (i = 0; i < dataArr.length; i ++) {
				if (coords.x === dataArr[i].date) {
					svgText.text(d3.timeFormat("%Y/%m/%d")(coords.x) + ", 開盤：" + dataArr[i].open + ", 高：" + dataArr[i].high + ", 低："+ dataArr[i].low + ", 收盤："+ dataArr[i].close + ", 成交量： " + dataArr[i].volume );
				//	svgText.text(d3.timeFormat("%Y/%m/%d")(coords.x) + ", 開盤：" + dataArr[i].open + ", 高：" + dataArr[i].high + ", 低："+ dataArr[i].low + ", 收盤："+ dataArr[i].close + ", 漲跌：" + dataArr[i].change + "(" + dataArr[i].percentChange + "%)" + ", 成交量： " + dataArr[i].volume + ", 5MA: " + dataArr[i].fiveMA + ", 20MA: " + dataArr[i].twentyMA + ", 60MA: " + dataArr[i].sixtyMA );
				}
			}
		}

	</script>
    </body>
</html>
