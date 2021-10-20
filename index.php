<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Web Live Updates</title>
	<style type="text/css">
		#submit {
			padding:  5px;
		}
		#newVal {
			padding: 5px;
		}
	</style>
</head>
<body>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="./libs/jscolor/jscolor.min.js"></script>
	<script src="./libs/js-live-color-transformation/src/live-colors.js"></script>
	<script>
		jscolor.presets.default = {
			palette: [
				'#000000', 
				'#7d7d7d', 
				'#870014', 
				'#ec1c23', 
				'#ff7e26', 
				'#fef100', 
				'#22b14b', 
				'#00a1e7', 
				'#3f47cc', 
				'#a349a4', 
				'#ffffff', 
				'#c3c3c3', 
				'#b87957', 
				'#feaec9', 
				'#ffc80d', 
				'#eee3af', 
				'#b5e61d', 
				'#99d9ea', 
				'#7092be', 
				'#c8bfe7',
			],
		}
	</script>
	
	<div>
		<span>Value: 
			<span id="target"></span>
		</span>
	<div>
		<span>Text: 
			<input type="text" name="targetValue" id="newVal">
		</span>
	</div>

	<div>
		<span>Color: 
			<input id="colorChanger" value="#ACACAC" data-jscolor="{}">
			<input id="allowColorChange" type="checkbox" unchecked>
		</span>
	</div>


	<button id="submit">Update</button>

<script type="text/javascript">
	// background color

	var livecolors = new LiveColorsEngine;




	function updateBackground(HEX) {
		livecolors.animateBackground( document.body, livecolors.hex(HEX) )
		// document.body.style.background = ;
	}

	var palette = document.getElementById("colorChanger")
	var pullbackground = true;
	var backgroundValMoved = false
	palette.oninput = function(){
		updateBackground(palette.value)
		pullbackground = false;
		backgroundValMoved = true;
	}
	palette.onchange = function() {
		setTimeout(function(){
			pullbackground = true;
		},5000) 
	}


</script>
<script>
	// main loop

	var targetDiv = document.getElementById("target");

	var config = null;
	var refreshTime = 1000;

	function refresh() {
		jQuery.get('./config.json', function(data) {
			config = data;
			console.log(data);


			targetDiv.innerText = config.value
			
			if (typeof config.refreshTime != "undefined") 
				refreshTime = config.refresh;
			
			if (!refreshTime) 
				refresh();

			if (pullbackground) { 
				if(!backgroundValMoved) 
					palette.value = config.color; 

				updateBackground(config.color)
			};

		});
		if(refreshTime) {
			setTimeout(refresh, refreshTime);
		}
		
	}

	refresh();

</script>
<script>
	// submit

	document.getElementById("submit").onclick = function(){

		var newvalue = document.getElementById("newVal").value
		if( newvalue.length > 250 ) { alert("content is too long"); return; } 

		let newconfig = config; 
			newconfig.value = newvalue;
			if(document.getElementById("allowColorChange").checked) 
				newconfig.color = palette.value;

		$.ajax({
			type: "POST",
			dataType: "text",
			url: "./updateconfig.php",
			data: {config: JSON.stringify(newconfig)},
			// contentType: "application/json; charset=utf-8",
			success: function(data){
				console.log("sent successfully",data)
			},
			error: function(e){
				console.log(e.message);
			}
		});

	};
	


	

</script>
</body>
</html>