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
					<img src="/logo.png">
					<h3 style="position: relative; bottom: 25px;">Position Paper Submission</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3">
					<?php
					if(isset($formError)) {
					?>
					<span style="color:red;"><?= $formError; ?></span>
					<?php
					}
					?>
					<form enctype="multipart/form-data" action="" method="POST">
						<div class="form-group">
							<label for="delegatename">What is your name?</label>
							<input class="form-control" type="text" name="delegatename" id="delegatename">
						</div>
						<div class="form-group">
							<label for="highschool">What is the name of your high school?</label>
							<input class="form-control" type="text" name="highschool" id="highschool">
						</div>
						<div class="form-group">
							<label for="committee">What is your committee?</label>
							<select class="form-control" name="committee" id="committee">
								<option value="DISEC">DISEC</option>
								<option value="ECOFIN">ECOFIN</option>
								<option value="SOCHUM">SOCHUM</option>
								<option value="SPECPOL">SPECPOL</option>
								<option value="UNHRC">UNHRC</option>
								<option value="WCW">World Commission on Women</option>
								<option value="ICC">International Criminal Court</option>
								<option value="IPD">International Press Delegation</option>
								<option value="Other">Other</option>
							</select>
						</div>
						<div class="form-group">
							<label for="position">What is your country?</label>
							<input class="form-control" type="text" name="position" id="position">
						</div>
						<div class="form-group">
							<label for="paper">Attach your position paper (PDF or Word documents accepted)</label>
							<input class="form-control" type="file" name="paper" id="paper">
						</div>
						<button type="submit" class="btn btn-default">Submit</button>
					</form>
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	</body>
</html>