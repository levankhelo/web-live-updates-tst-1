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
		#blocker {
			padding:  -10px;
			margin:  -10px;
			height: 100000px;
			width: 100000px;
			top:  -100px;
			left:  -100px;
			background-color: black;
			z-index: 100;
		}
	</style>
</head>
<body>
	<div id="blocker"></div>
	<script>
		setTimeout(function(){
			document.getElementById("blocker").remove();
		},2000)
	</script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="./libs/js-live-color-transformation/src/live-colors.js"></script>
	
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
			<input type="color" id="colorChanger" name="colorChanger" value="#ACACAC">
			<span><input id="allowColorChange" type="checkbox" unchecked> Update</span>
			<span><input id="lockColor" type="checkbox" unchecked> Lock</span>
			
		</span>
	</div>


	<button id="submit">Update</button>

<script type="text/javascript">
	// background color

	var livecolors = new LiveColorsEngine;


	var palette = document.getElementById("colorChanger")
	var pullbackground = true;
	var backgroundValMoved = false
	palette.oninput = function(){
		document.body.style.background = palette.value;
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
	// submit
	var newValDiv = document.getElementById("newVal");
	document.getElementById("submit").onclick = function(){

		var newvalue = newValDiv.value
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
<script>
	// main loop

	var targetDiv = document.getElementById("target");
		

		newValueEntered = false;
		newValDiv.oninput = function(){
			newValueEntered = true;
		}
		newValDiv.oninput = function(){
			newValueEntered = true;
		}

	var config = null;
	var refreshTime = 1000;

	function refresh() {
		jQuery.get('./config.json', function(data) {
			config = data;
			console.log(data);


			targetDiv.innerText = config.value
			if (!newValueEntered) newValDiv.value = config.value;
			
			if (typeof config.refreshTime != "undefined") 
				refreshTime = config.refresh;
			
			if (!refreshTime) 
				refresh();

			if (pullbackground) { 
				if(!backgroundValMoved) 
					palette.value = config.color; 

				livecolors.animateBackground( document.body, livecolors.hex(config.color) );
			};

		});
		if(refreshTime) {
			setTimeout(refresh, refreshTime);
		}
		
	}

	refresh();

</script>

</body>
</html>