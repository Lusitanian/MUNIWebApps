<!doctype html>
<html>
	<head>
		<title>MUNI Position Papers</title>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
		<link href='//fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic&subset=latin,latin-ext' rel='stylesheet' type='text/css' />
	</head>
	<body style="font-family: Arvo;">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3" style="text-align: center;">
					<img src="/logo.png"><br>
					<?php echo $delegate; ?>, thank you for submitting your position paper.<br>
					<a href="#" id="close" title="Close">You may click here to close this window.</a>
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		<script>
			$("#close").click(function() { window.close(); });
		</script>
	</body>
</html>