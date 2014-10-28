window.addEventListener ? 
window.addEventListener("load",init,false) : 
window.attachEvent && window.attachEvent("onload",init);

function init() {
	
	var votes = document.querySelectorAll('.voteData');

	for(var x = 0; x < votes.length; ++x) {
		var dat = votes[x].innerHTML.trim().split(' ');
		drawGraph(dat);
	}
	InitChart();
};

function drawGraph(data) {
	var graphData = [];
	for(var x = 0; x < data.length; ++x) {
		graphData.push({'x': x, 'y': +data[x]});
	}

	var height = 200,
		width = 200;

	var x = d3.time.scale()
		.range([0, width]);

	var y = d3.time.scale()
		.range([0, height]);

	var xAxis = d3.svg.axis()
    	.scale(x)
    	.orient("bottom");

	var yAxis = d3.svg.axis()
	    .scale(y)
	    .orient("left");

	var line = d3.svg.line()
    	.x(function(d) { return x(d.x); })
   		.y(function(d) { return y(d.y); });

 /*  	var svg = d3.select("#test1").append("svg")
 	   	.attr("width", width)
 	   	.attr("height", height)
 	   	.append("g");

 	svg.append("path")
    	.datum(graphData)
    	.attr("class", "line")
    	.attr("d", line);
*/
}


function InitChart() {

  var lineData = [{
    'x': 1,
    'y': 5
  }, {
    'x': 20,
    'y': 20
  }, {
    'x': 40,
    'y': 10
  }, {
    'x': 60,
    'y': 40
  }, {
    'x': 80,
    'y': 5
  }, {
    'x': 100,
    'y': 60
  }];

  var vis = d3.select("#visualisation"),
    WIDTH = 1000,
    HEIGHT = 500,
    MARGINS = {
      top: 20,
      right: 20,
      bottom: 20,
      left: 50
    },
    xRange = d3.scale.linear().range([MARGINS.left, WIDTH - MARGINS.right]).domain([d3.min(lineData, function (d) {
        return d.x;
      }),
      d3.max(lineData, function (d) {
        return d.x;
      })
    ]),

    yRange = d3.scale.linear().range([HEIGHT - MARGINS.top, MARGINS.bottom]).domain([d3.min(lineData, function (d) {
        return d.y;
      }),
      d3.max(lineData, function (d) {
        return d.y;
      })
    ]),

    xAxis = d3.svg.axis()
      .scale(xRange)
      .tickSize(5)
      .tickSubdivide(true),

    yAxis = d3.svg.axis()
      .scale(yRange)
      .tickSize(5)
      .orient("left")
      .tickSubdivide(true);


  vis.append("svg:g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + (HEIGHT - MARGINS.bottom) + ")")
    .call(xAxis);

  vis.append("svg:g")
    .attr("class", "y axis")
    .attr("transform", "translate(" + (MARGINS.left) + ",0)")
    .call(yAxis);

  var lineFunc = d3.svg.line()
  .x(function (d) {
    return xRange(d.x);
  })
  .y(function (d) {
    return yRange(d.y);
  })
  .interpolate('linear');

vis.append("svg:path")
  .attr("d", lineFunc(lineData))
  .attr("stroke", "blue")
  .attr("stroke-width", 2)
  .attr("fill", "none");


}