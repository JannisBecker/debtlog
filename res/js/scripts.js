$(document).ready(function() {

	/* Load table */
	 $.ajax({
		url: "res/php/query.php",
		method: "POST",
		data: { 
			query: 'indextable',
			rowCount: 20,
		}
	}).done(function(data) {
		if(data.length > 10) {
			$("div.page-content").html(data);
		}

		
		$("table.debt-table-main").click(function() {
			var mainTable = $(this);
			var subTable = $(this).next("div.debt-table-collapsible");
			subTable.siblings("div.debt-table-collapsible.extended").each(function() {
				$(this).slideUp();
				$(this).removeClass("extended");
				$(this).prev("table").find("i.caret-icon").removeClass("uk-icon-caret-down").addClass("uk-icon-caret-right");
			});
			subTable.slideToggle();
			subTable.toggleClass("extended");
			mainTable.find("i.caret-icon").toggleClass("uk-icon-caret-down").toggleClass("uk-icon-caret-right");
		});
	});

	$.ajax({
		url: "res/php/query.php",
		method: "POST",
		data: { 
			query: 'alldebts'
		}
	}).done(function(data) {
		if(data.length > 0) {

			var debtsArray = data.split(";");
			var debtsAG = parseFloat(debtsArray[0]);
			var debtsGW = parseFloat(debtsArray[1]);
			var debtsWA = parseFloat(debtsArray[2]);
			
			var waechter = new User('Waechter','res/images/waechter.png',"user/waechter");
			var anuk = new User('Anuk','res/images/anuk.png',"user/anuk");
			var gwomm = new User('Gwomm','res/images/gwomm.png',"user/gwomm");

			if(debtsAG > 0) {
				anuk.debtList.push(new Debt(gwomm,debtsAG));
			} else {
				gwomm.debtList.push(new Debt(anuk,Math.abs(debtsAG)));
			}

			if(debtsGW > 0) {
				gwomm.debtList.push(new Debt(waechter,debtsGW));
			} else {
				waechter.debtList.push(new Debt(gwomm,Math.abs(debtsGW)));
			}

			if(debtsWA > 0) {
				waechter.debtList.push(new Debt(anuk,debtsWA));
			} else {
				anuk.debtList.push(new Debt(waechter,Math.abs(debtsWA)));
			}

			init([waechter, anuk, gwomm], $('canvas'));
			
			buildGraph();

			$( window ).resize(function() {
				buildGraph();
			});
		}
	});
});