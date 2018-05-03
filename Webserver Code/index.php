<html>

<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<!-- Chart.js CDN for graphing data -->
	<script src="Chart.min.js"></script>
	<!-- Regression library -->
	<script src="regression.min.js"></script>
</head>

<body>

<div class="container">
	<div class="jumbotron">
		<h1>CSCE 315 Project 2</h1>
	</div>

	<!-- Chart and controls -->
	<div class="card">
	<div class="card-body">
		<div id="chart-container"><canvas id="chart"></canvas></div>
		
		<!-- Controls for chart data + loading bar. -->
		<form id="chart-control">
		<fieldset><legend>Data Controls</legend>
			<label for="mode-select">View by:</label>
			<select id="mode-select" name="mode-select">
				<option value="Day">Day</option>
				<option value="Month">Month</option>
				<option value="Year" selected="selected">Year</option>
			</select>

			<label for="date-select">Date:</label>
			<input type="date" id="date-select" name="date-select" />

			<input type="submit" value="Update" />
			<div id="chart-loading" class="progress" style="visibility:hidden"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">Loading...</div></div>
		</fieldset>
		</form>

		<!-- Controls for regression line. -->
		<form id="regression-control">
		<fieldset><legend>Regression Controls</legend>
			<label for="dataset-select">Dataset:</label>
			<select id="dataset-select" name="dataset-select"></select>

			<label for="start-select">Start:</label>
			<select id="start-select" name="start-select"></select>

			<label for="end-select">End:</label>
			<select id="end-select" name="end-select"></select>

			<label for="order-select">Polynomial Order:</label>
			<input type="number" id="order-select" name="order-select" value="0" min="0" />

			<input type="submit" value="Add" />
			<input type="reset" id="regression-clear" value="Clear" />
		</fieldset>
		</form>
	</div>
	</div>

	<div class="card bg-light">
	<div class="card-header">Instructions</div>
	<div class="card-body">
		<h5 class="card-title">Data controls</h5>
		<p class="card-text"><em>These tools give you control over what data is displayed on the chart.</em></p>
		<dl class="row">
			<dt class="col-sm-3">View by: <em>mode</em></dt>
			<dd class="col-sm-9">Changes how data is broken up. For example, viewing by day will show all traffic for the given day, broken up by hour.</dd>

			<dt class="col-sm-3">Date: <em>dd/mm/yyyy</em></dt>
			<dd class="col-sm-9">Changes the date you are viewing. Note: If you are viewing (for example) by year, then changing the day/month will have no effect.</dd>

			<dt class="col-sm-3">Submit</dt>
			<dd class="col-sm-9">After making any changes, make sure to click the submit button.</dd>
		</dl>

		<h5 class="card-title">Regression controls</h5>
		<p class="card-text"><em>These tools allow you to add and modify a best-fit polynomial line over a particular dataset.</em></p>
		<dl class="row">
			<dt class="col-sm-3">Dataset: <em>choice</em></dt>
			<dd class="col-sm-9">This allows you to choose a dataset to work with.</dd>

			<dt class="col-sm-3">Start/End: <em>time</em></dt>
			<dd class="col-sm-9">These options allow you to specify a range for your data. Any data before <em>start</em> or after <em>end</em> will not be used to generate the regression line.</dd>
			
			<dt class="col-sm-3">Polynomial order: <em>number</em></dt>
			<dd class="col-sm-9">Specifies the polynomial order for the best-fit line. For example, if you wanted to display a porabola, you would select 2 as your order.</dd>

			<dt class="col-sm-3">Add</dt>
			<dd class="col-sm-9">Add the regression line to the chart. Note: If a regression line is already present, it will be replaced with the new one. Also note, if changes are made in "Data controls", you will not see a difference in the regression line until clicking "Add" again.</dd>

			<dt class="col-sm-3">Clear</dt>
			<dd class="col-sm-9">Removes the regression line from the chart. Does nothing if there is no regression line.</dd>
		</dl>
	</div>
	</div>

</div>

<!-- Chart script -->
<script>

// Global variables.
var data_link = '';			 // Link to .php file containing chart data.
var locations = [];			 // List of locations.
var xlabels = [];			 // Labels for x-axis on graph.
var refresh_timeout;		 // Timeout object for data refresh.

// Create chart object.
var ctx = document.getElementById('chart').getContext('2d');
var chart = new Chart(ctx, {

    // The type of chart we want to create
    type: 'line',

    // The data for our dataset
    data: {
        labels: [], // Empty for now.
        datasets: [] // Empty for now.
    },

	// Configuration options go here
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    min: 0
                }
            }]
        },

        legend: {
            display: true
        },

        title: {
            display: true,
            text: '' // No title for now.
        },

        tooltips: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Colors or chart.
solid_colors = [
    'rgb(255, 99, 132)',
    'rgb(54, 162, 235)',
    'rgb(255, 206, 86)',
    'rgb(75, 192, 192)',
    'rgb(153, 102, 255)',
    'rgb(255, 159, 64)'
];

clear_colors = [
    'rgba(255, 99, 132, 0.2)',
    'rgba(54, 162, 235, 0.2)',
    'rgba(255, 206, 86, 0.2)',
    'rgba(75, 192, 192, 0.2)',
    'rgba(153, 102, 255, 0.2)',
    'rgba(255, 159, 64, 0.2)'
];

// Return a regression line dataset for the input dataset.
function getRegressionDataset(dataset, start, end, len, n) {

	start = parseInt(start);
	end = parseInt(end);
	n = parseInt(n);

	// Format data for the regression() function.
	var data = Array(end - start + 1);

	for(var i = start; i <= end; ++i) {
		data[i-start] = dataset.data[i];
	}

	var data = data.map(function(e,i) {
		return [i+start, e];
	});

	// Get the regression object.
	var a = regression('polynomial', data, n);

	// Generate regression line.
	var result = Array(len);
	for(var i = 0; i < len; ++i) {
		var y = 0;
		for(var j = 0; j < a.equation.length; ++j) {
			y += a.equation[j] * Math.pow(i,j);
		}
		result[i] = Math.max(0,y);
	}

	return {
		label : 'Regression (' + dataset.label + ')',
		data : result,
		borderColor : dataset.borderColor,
		backgroundColor : 'rgba(0,0,0,0)',
		borderDash : [5, 5]
	};
}

// Used as a callback for $.ajax().
function dataLoadCallback(globals) {

    var i = globals.i;
	var locations = globals.locations;

    return function(text) {

		var changed = false;

		// Retrieve data.
        var data = text.split("\n");

		// Initialize dataset if needed and/or check if data has changed since last update.
        if(typeof chart.config.data.datasets[i] == "undefined") {
            chart.config.data.datasets[i] = {};
			changed = true;
		}

        if(typeof chart.config.data.datasets[i].data == "undefined") {
            changed = true;
            chart.config.data.datasets[i].data = [];
        } else if(chart.config.data.datasets[i].data.length != data.length) {
			changed = true;
		} else {
            for(j = 0; j < data.length; ++j) {
                
                if(chart.config.data.datasets[i].data[j] != data[j]) {
                    changed = true;
                }

            }
		}

		if(changed) {
			chart.config.data.datasets[i].label = locations[i];
			chart.config.data.datasets[i].data = data;
			chart.config.data.datasets[i].lineTension = 0;
			chart.config.data.datasets[i].borderColor = solid_colors[i%solid_colors.length];
			chart.config.data.datasets[i].backgroundColor = clear_colors[i%solid_colors.length];
		}

    }

}

// Continuously reload data from data_link and refresh chart.
function refreshDataLoop(data_link, locations) {

    var numFinished = 0;

    for(i = 0; i < locations.length; ++i) {

        // Load data for each loacation.
        $.ajax({
            url : data_link + "&loc=" + locations[i],
            dataType : 'text',
            cache : false,
            success : dataLoadCallback({i:i, locations:locations}),
            complete : function(x,y) {
                if(++numFinished >= locations.length) {
					chart.update();
					$("#chart-loading").css("visibility", "hidden");
				}
            }
        });
        
    }

	refresh_timeout = setTimeout(function() { refreshDataLoop(data_link, locations); }, 10000);
    
}

// To be called in updateChart() after loading locations and xlabels.
function updateRegressionForm() {
	// Store previously selected location (index).
	var prev_selected_location = $("#dataset-select").val();

	var location_to_select = 0;

	// Keep the original selected location if possible.
	if(prev_selected_location < locations.length && prev_selected_location != null)
		location_to_select = prev_selected_location;

	$("#dataset-select").html((function() {
		result = '';
		for(var i = 0; i < locations.length; ++i) {
			result += '<option value="' + i + '"';
			if(i == location_to_select)
				result += ' selected="selected"';
			result += '>' + locations[i] + '</option>';
		}
		return result;
	})());

	
	// Store previously selected start/end (index).
	var prev_selected_start = $("#start-select").val();
	var prev_selected_end = $("#end-select").val();

	var start_to_select = 0;
	var end_to_select = xlabels.length-1;

	// Keep the original selected location if possible.
	if(prev_selected_start < xlabels.length && prev_selected_start != null)
		start_to_select = prev_selected_start;

	if(prev_selected_end < xlabels.length && prev_selected_end != null)
		end_to_select = prev_selected_end;

	$("#start-select").html((function() {
		result = '';
		for(var i = 0; i < xlabels.length; ++i) {
			result += '<option value="' + i + '"';
			if(i == start_to_select)
				result += ' selected="selected"';
			result += '>' + xlabels[i] + '</option>';
		}
		return result;
	})());

	$("#end-select").html((function() {
		result = '';
		for(var i = 0; i < xlabels.length; ++i) {
			result += '<option value="' + i + '"';
			if(i == end_to_select)
				result += ' selected="selected"';
			result += '>' + xlabels[i] + '</option>';
		}
		return result;
	})());
}

// Update chart according to values in form.
function updateChart() {

	// Display loading bar.
	$("#chart-loading").css("visibility", "visible");

	var date = new Date($("#date-select").val());
	var mode = $("#mode-select").val().toLowerCase();
	var args = "?mode=" + mode +
		"&day=" + ("0000" + date.getUTCDate()).slice(-2) +
		"&month=" + ("0000" + (date.getUTCMonth()+1)).slice(-2) +
		"&year=" + date.getUTCFullYear();

	// Update chart title.
	var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	if(mode == "day") {
		chart.config.options.title.text = months[date.getUTCMonth()] + " " + date.getUTCDate() + ", " + date.getUTCFullYear();
	} else if(mode == "month") {
		chart.config.options.title.text = months[date.getUTCMonth()] + " " + date.getUTCFullYear();
	} else if(mode == "year") {
		chart.config.options.title.text = date.getUTCFullYear();
	}

	// Update data link.
	data_link = "chart-data.php" + args;
	// Load locations from file and then load xlabels and then refresh data.
	$.ajax({
		url : "chart-locations.php",
		dataType : 'text',
		cache : false,
		success : function(text) {
			var data = text.split("\n");
			locations = data;
		},
		complete : function(x,y) {
			
			$.ajax({
				url : "chart-xlabels.php" + args,
				dataType : 'text',
				cache : false,
				success : function(text) {
					xlabels = text.split("\n");
					chart.config.data.labels = xlabels;
				},
				complete : function(x,y) {
					updateRegressionForm();
					if(typeof refresh_timeout != "undefined") {
						clearTimeout(refresh_timeout);
					}
					refreshDataLoop(data_link, locations);
				}
			})

		}
	});
}

$(document).ready(function(){
	// Change #date-select value to current date.
    var date = new Date();
    $("#date-select").val(date.getFullYear() + "-" + ("0000" + (date.getMonth()+1)).slice(-2) + "-" + ("0000" + date.getDate()).slice(-2));
	updateChart();

	// Various handlers for form submit and reset events.
    $("#chart-control").submit(function(event) {
        if($("#date-select").val() != "")
		updateChart();
		event.preventDefault();
	});
	
    $("#regression-control").submit(function(event) {
		if(typeof chart.config.data.datasets[locations.length] == "undefined")
			chart.config.data.datasets[locations.length] = {};
		
		var regression_data = getRegressionDataset(
			chart.config.data.datasets[4],
			$("#start-select").val(),
			$("#end-select").val(),
			chart.config.data.labels.length,
			$("#order-select").val()
		);

		chart.config.data.datasets[locations.length].label = regression_data.label;
		chart.config.data.datasets[locations.length].data = regression_data.data;
		chart.config.data.datasets[locations.length].borderColor = regression_data.borderColor;
		chart.config.data.datasets[locations.length].backgroundColor = regression_data.backgroundColor;
		chart.config.data.datasets[locations.length].borderDash = regression_data.borderDash;

		chart.update();
		event.preventDefault();
    });
	
    $("#regression-clear").on("click", function(event) {
		chart.config.data.datasets.splice(locations.length,1);
		chart.update();
		event.preventDefault();
    });
});

</script>

</body>
</html>