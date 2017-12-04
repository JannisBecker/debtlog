<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link rel="stylesheet" href="res/css/uikit.min.css" />
	<link rel="stylesheet" href="res/css/uikit.almost-flat.min.css" />
	<link rel="stylesheet" href="res/css/components/datepicker.almost-flat.min.css" />
	<link rel="stylesheet" href="res/css/style.css" />

	<script src="res/js/jquery-3.1.1.min.js"></script>
	<script src="res/js/jcanvas.min.js"></script>
	<script src="res/js/uikit.min.js"></script>
	<script src="res/js/components/datepicker.min.js"></script>
	<script src="res/js/debtGraph.js"></script>
	<script src="res/js/scripts.js"></script>
</head>
<body>
	<div class="head-container">
		<canvas></canvas>
	</div>



	<div id="modal-addentry" class="uk-modal">
		<div class="uk-modal-dialog">
			<div class="uk-modal-header">Eintrag hinzufügen</div>
			<form class="uk-form uk-form-horizontal">
				<div class="uk-form-row">
					<legend>Empfänger</legend>
					<div class="uk-grid">
						<div class="uk-width-medium-1-3">
							<input type="radio" id="radio-recp-anuk" class="form-radio" value="Anuk" name="radio" required>
							<label for="radio-recp-anuk">Anuk</label>
						</div>
						<div class="uk-width-medium-1-3">
							<input type="radio" id="radio-recp-gwomm" class="form-radio" value="Gwomm" name="radio">
							<label for="radio-recp-gwomm">Gwomm</label>
						</div>
						<div class="uk-width-medium-1-3">
							<input type="radio" id="radio-recp-waechter" class="form-radio"  value="Waechter" name="radio">
							<label for="radio-recp-waechter">Wächter</label>
						</div>
					</div>
				</div>


				<div class="uk-form-row">
					<legend>Schulden</legend>
					<label class="uk-form-label" for="text-debt-anuk">Anuk</label>
					<div class="uk-form-controls">
						<input type="number" id="text-debt-anuk" class="uk-width-small-1-1">
					</div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label" for="text-debt-gwomm">Gwomm</label>
					<div class="uk-form-controls">
						<input type="number" id="text-debt-gwomm" class="uk-width-small-1-1">
					</div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label" for="text-debt-waechter">Wächter</label>
					<div class="uk-form-controls">
						<input type="number" id="text-debt-waechter" class="uk-width-small-1-1">
					</div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label" for="text-debt-wgtotal">WG (total)</label>
					<div class="uk-form-controls">
						<input type="number" id="text-debt-wgtotal" class="uk-width-small-1-1">
					</div>
				</div>


				<div class="uk-form-row">
					<legend>Weiteres</legend>
					<label class="uk-form-label" for="text-comment">Kommentar</label>
					<div class="uk-form-controls">
						<input type="text" id="text-comment" class="uk-width-small-1-1" required>
					</div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label" for="text-date">Datum Zettel</label>
					<div class="uk-form-controls">
						<input type="text" id="text-date" class="uk-width-small-1-1" data-uk-datepicker="{format:'DD.MM.YYYY'}">
					</div>
				</div>

				<div class="uk-form-row">
					<div class="uk-alert uk-alert-success form-infopanel">
						<span>Der Eintrag wurde erfolgreich hinzugefügt!</span>
					</div>
				</div>
			</form>

			<div class="uk-modal-footer">
				<div class="uk-text-right">
					<button type="button" class="uk-button uk-modal-close ">Cancel</button>
					<button type="button" class="uk-button uk-button-danger">Undo last</button>
					<button type="button" class="uk-button uk-button-primary">Add Entry</button>
				</div>
			</div>
		</div>
	</div>




	<div class="content">
		<div class="navbar-container">
			<div class="uk-container uk-container-center">
				<nav class="uk-navbar">
					<ul class="uk-navbar-nav">
						<li class="uk-active"><a href="">Übersicht</a></li>
						<li><a href="">Meine Schulden</a></li>
						<li><a href="add.php" data-uk-modal-disabled="{target:'#modal-addentry'}">+Eintrag</a></li>
					</ul>
					<div class="uk-navbar-flip">
						<ul class="uk-navbar-nav">
							<li><a href="">Ausloggen</a></li>
						</ul>
					</div>
				</nav>
			</div>
		</div>
		<div class="uk-container uk-container-center">
			<div class="uk-block page-content">

			</div>
		</div>
	</div>
</body>
</html>