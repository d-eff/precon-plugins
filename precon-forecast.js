window.addEventListener ? 
window.addEventListener("load",init,false) : 
window.attachEvent && window.attachEvent("onload",init);

function init() {
	
	var votes = document.querySelectorAll('.voteData');
	
	for(var x = 0; x < votes.length; ++x) {
		var dat = votes[x].dataset.votes.trim().split(' ');
		drawGraph(dat, x);
	}
	//InitChart();
};

function drawGraph(data, count) {
	var graphData = [];
	for(var x = 0; x < data.length; ++x) {
		graphData.push({'x': x, 'y': +data[x]});
	}
	if(count === 0) {
		InitChart0(graphData, count);	
	} else if (count === 1) {
	//	InitChart1(graphData, count);
	} else if (count === 2) {
	//	InitChart2(graphData, count);
	}
	
}


// function InitChart1(lineData, innum) {

//   var vis = d3.select("#visualisation1"),
//     WIDTH = 300,
//     HEIGHT = 200,
//     MARGINS = {
//       top: 20,
//       right: 20,
//       bottom: 20,
//       left: 50
//     },
//     xRange = d3.scale.linear().range([MARGINS.left, WIDTH - MARGINS.right]).domain([d3.min(lineData, function (d) {
//         return d.x;
//       }),
//       d3.max(lineData, function (d) {
//         return d.x;
//       })
//     ]),

//     yRange = d3.scale.linear().range([HEIGHT - MARGINS.top, MARGINS.bottom]).domain([0, 100]),

//     xAxis = d3.svg.axis()
//       .scale(xRange)
//       .tickSize(5)
//       .tickSubdivide(false),

//     yAxis = d3.svg.axis()
//       .scale(yRange)
//       .ticks(5)
//       .tickSize(5)
//       .orient("left")
//       .tickSubdivide(false);

// var area = d3.svg.area()
//     .x(function(d) { return x(d.x); })
//     .y0(200)
//     .y1(function(d) { return y(d.y); });

//   vis.append("svg:g")
//     .attr("class", "x axis")
//     .attr("transform", "translate(0," + (HEIGHT - MARGINS.bottom) + ")")
//     .call(xAxis);

//   vis.append("svg:g")
//     .attr("class", "y axis")
//     .attr("transform", "translate(" + (MARGINS.left) + ",0)")
//     .call(yAxis);

//   var lineFunc = d3.svg.line()
//   .x(function (d) {
//     return xRange(d.x);
//   })
//   .y(function (d) {
//     return yRange(d.y);
//   })
//   .interpolate('linear');

// vis.append("svg:path")
//   .attr("d", lineFunc(lineData))
//   .attr("stroke", "blue")
//   .attr("stroke-width", 2)
//   .attr("fill", "none");

// vis.append("path")
//         .datum(lineData)
//         .attr("class", "area")
//         .attr("d", area);


// }

function InitChart0(lineData, innum) {

  var vis = d3.select("#visualisation0"),
    WIDTH = 300,
    HEIGHT = 200,
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

    yRange = d3.scale.linear().range([HEIGHT - MARGINS.top, MARGINS.bottom]).domain([0, 100]),

    xAxis = d3.svg.axis()
      .scale(xRange)
      .tickSize(2)
      .ticks(d3.time.days, 15)
      .orient('bottom');
      //.tickFormat(d3.date.format(%H))

    yAxis = d3.svg.axis()
      .scale(yRange)
      .tickSize(2)
      .ticks(5)
      .orient("left");

var area = d3.svg.area()
  .x(function (d) {
    return xRange(d.x);
  })
  .y0(HEIGHT-20)
  .y1(function (d) {
    return yRange(d.y);
  });
  vis.append("path")
        .datum(lineData)
        .attr("class", "area")
        .attr("d", area);


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

// function InitChart2(lineData, innum) {

//   var vis = d3.select("#visualisation2"),
//     WIDTH = 300,
//     HEIGHT = 200,
//     MARGINS = {
//       top: 20,
//       right: 20,
//       bottom: 20,
//       left: 50
//     },
//     xRange = d3.scale.linear().range([MARGINS.left, WIDTH - MARGINS.right]).domain([d3.min(lineData, function (d) {
//         return d.x;
//       }),
//       d3.max(lineData, function (d) {
//         return d.x;
//       })
//     ]),

//     yRange = d3.scale.linear().range([HEIGHT - MARGINS.top, MARGINS.bottom]).domain([0, 100]),

//     xAxis = d3.svg.axis()
//       .scale(xRange)
//       .tickSize(5)
//       .tickSubdivide(false),

//     yAxis = d3.svg.axis()
//       .scale(yRange)
//       .tickSize(5)
//       .ticks(5)
//       .orient("left")
//       .tickSubdivide(false);

// var area = d3.svg.area()
//     .xRange(function(d) { return x(d.x); })
//     .y0(200)
//     .y1(function(d) { return y(d.y); });

//   vis.append("svg:g")
//     .attr("class", "x axis")
//     .attr("transform", "translate(0," + (HEIGHT - MARGINS.bottom) + ")")
//     .call(xAxis);

//   vis.append("svg:g")
//     .attr("class", "y axis")
//     .attr("transform", "translate(" + (MARGINS.left) + ",0)")
//     .call(yAxis);

//   var lineFunc = d3.svg.line()
//   .x(function (d) {
//     return xRange(d.x);
//   })
//   .y(function (d) {
//     return yRange(d.y);
//   })
//   .interpolate('linear');

// vis.append("svg:path")
//   .attr("d", lineFunc(lineData))
//   .attr("stroke", "blue")
//   .attr("stroke-width", 2)
//   .attr("fill", "none");

// vis.append("path")
//         .datum(lineData)
//         .attr("class", "area")
//         .attr("d", area);

// }