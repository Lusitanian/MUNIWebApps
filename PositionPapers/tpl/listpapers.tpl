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
				<div class="col-sm-6 col-sm-offset-3" style="text-align:center;">
					<img src="/logo.png">
					<h3 style="position: relative; bottom: 25px;">Position Papers</h3>
				</div>
			</div>

			<div class="row">
				<div id="committee-list" class="col-sm-3">
					<ul style="list-style-type: none; font-size: 16px">
					<li style="font-weight: 800;">Select a committee...</li>
					<?php
					foreach($papers as $committee => $positionPapers) {
					?>
						<li><a class="committee-link" href="#" data-committee="<?= $committee; ?>"title="<?= $committee; ?>"><?= $committee; ?></a></li>
					<?php
					}
					?>
					</ul>
				</div>
				<div id="papers" class="col-sm-9">
					<div id="paper-panel" class="panel panel-default">
						<div class="panel-heading">
							<h3 id="paper-panel-title" class="panel-title">Position Papers</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		<script>
			// paper data, indexed by committee
			var paperData = <?= json_encode($papers); ?>;
			$(function() {
				$(".committee-link").click(function() {
					var cmt = $(this).data('committee');
					var papers = paperData[cmt];
					$("#paper-panel-title").text("Position Papers (" + cmt + ")");
					//$("#paper-panel-body").remove();
					$("#papertable").remove();
					// position - delegate - hs - view
					var tableHtml = "<table id='papertable' class='table' border=''><tr><th>Position</th><th>Delegate</th><th>High School</th><th>View</th></tr>";
					$.each(papers, function(index, paper) {
						var link = "<a href='/view-paper/" + paper['id'] + "' title='View'>View</a>";
						var row = "<tr><td>" + paper['position'] + "</td><td>" + paper['delegate'] + "</td><td>" + paper['hs'] + "</td><td>" + link + "</td></tr>";
						tableHtml += row;
					});
					tableHtml += "</table>";
					$("#paper-panel").append(tableHtml);
				});
			});
		</script>
	</body>
</html>