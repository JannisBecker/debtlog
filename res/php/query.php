<?php
	$query = $_POST['query'];

	switch($query) {
		case 'indextable':
			$rowCount = $_POST['rowCount'];

			if(!empty($rowCount)) {
				$conn = sqlConnect();
				buildIndexTable($conn, $rowCount);
			}
				
			break;

		case 'userdebt':
			$debtor = $_POST['debtor'];
			$recipient = $_POST['recipient'];

			if(!empty($debtor) && !empty($recipient)) {
				$conn = sqlConnect();
				getUserDebt($conn, $debtor, $recipient);
			}
			break;

		case 'alldebts':
			$conn = sqlConnect();
			getAllDebts($conn);
			break;

		case 'addentry':
			$recipient = $_POST['recipient'];
			$debtsA = $_POST['debtsA'];
			$debtsG = $_POST['debtsG'];
			$debtsW = $_POST['debtsW'];
			$debtsWG = $_POST['debtsWG'];
			$comment = $_POST['comment'];
			$billdate = $_POST['billdate'];

			if(!isempty($recipient) && !isempty($debtsA.$debtsG.$debtsW.$debtsWG) && !isempty($comment) && !isempty($billdate)) {
				$conn = sqlConnect();
				addEntry($conn, $recipient, $debtsA, $debtsG, $debtsW, $debtsWG, $comment, $billdate);
			}
			break;		
	}

	function sqlConnect() {
		require_once("sql-config.php");
		$conn = mysql_connect($db_host,$db_user,$db_pass);
		if($conn) {
			$db = mysql_select_db("debts",$conn);
			if($db) {
				mysql_query("SET NAMES utf8mb4",$conn);
				return $conn;
			} else die ("couldnt open database");
		} else die("cant connect to mysql server");
	};

	function buildIndexTable($conn, $rowCount) {
		$query = "SELECT t.*, d.debtor, d.value, (SELECT SUM(d2.value) FROM debts d2, transactions t2 WHERE d2.transaction_id = t2.id AND t2.id = t.id) as billValue FROM debts AS d INNER JOIN transactions AS t ON d.transaction_id = t.id ORDER BY t.id DESC";
		$res = mysql_query($query,$conn);
		$numrows = mysql_num_rows($res);
		$html = "";

		if($numrows > 0) {
			$lastId = -1;
			$first = true;

			while($row = mysql_fetch_array($res)){
				$newId = $row['id'];
				
				if($newId != $lastId) { //New Main Table Row
					$lastId = $newId;

					if($first) { //If start of table, echo main header	
						echo("
							<h2>Übersicht</h2>
							<table class=\"uk-table uk-table-hover debt-table debt-table-head\">
								<caption>Die neuesten Schuldeneinträge:</caption>
								<tr>
									<th class=\"uk-width-6-10\">Titel</th>
									<th class=\"uk-width-2-10 uk-text-right\">Datum</th>
									<th class=\"uk-width-2-10 uk-text-right\">Betrag</th>
								</tr>
							</table>
						");
					} else { // If not start of table, then end the last Subrow table
						echo("
								</table>
							</div>
						");
					}

						//Echo Maintable row and first Subtable row
						echo("
							<table class=\"uk-table uk-table-hover debt-table debt-table-main\">
								<tr>
									<td class=\"uk-width-6-10\"><i class=\"caret-icon uk-icon-caret-right\"></i>".$row['comment']."</td>
									<td class=\"uk-width-2-10 uk-text-right\">".date("d.m.y H:i", strtotime($row['date']))."</td>
									<td class=\"uk-width-2-10 uk-text-right\">".$row['billValue']."€</td>
								</tr>
							</table>
							<div class=\"debt-table-collapsible\" hidden>
								<table class=\"uk-table debt-table debt-table-sub\">
									<tr>
										<th class=\"uk-width-2-10\">Schuldner</th>
										<th class=\"uk-width-6-10\">Empfänger</th>
										<th class=\"uk-width-2-10 uk-text-right\">Betrag</th>
									</tr>
									<tr> 
										<td>".$row['debtor']."</td> 
										<td>".$row['recipient']."</td> 
										<td class=\"uk-text-right\">".$row['value']."€</td> 
									</tr>
							"
						);
					$first = false;

				} else { //Echo additional Subtable row
						echo("
								<tr> 
									<td>".$row['debtor']."</td> 
									<td>".$row['recipient']."</td> 
									<td class=\"uk-text-right\">".$row['value']."€</td> 
								</tr>
						");
				}
			}

			//End last subrow at the end of the table
			echo("
					</table>
				</div>
			");
		}

	};

	function getUserDebt($conn, $debtor, $recipient) {
		echo("YAY");
	};

	function getAllDebts($conn) {
		$queryAG = "
			SELECT( 
			    SELECT SUM(value) AS schulden
				FROM  debts t1, transactions t2
				WHERE  t2.id = t1.transaction_id AND t2.recipient='Gwomm' AND debtor='Anuk')
			    -(
			    SELECT SUM(value) AS schulden
				FROM  debts t1, transactions t2
				WHERE  t2.id = t1.transaction_id AND t2.recipient='Anuk' AND debtor='Gwomm')
			AS debt";

		$queryGW = "
			SELECT( 
			    SELECT SUM(value) AS schulden
				FROM  debts t1, transactions t2
				WHERE  t2.id = t1.transaction_id AND t2.recipient='Waechter' AND debtor='Gwomm')
			    -(
			    SELECT SUM(value) AS schulden
				FROM  debts t1, transactions t2
				WHERE  t2.id = t1.transaction_id AND t2.recipient='Gwomm' AND debtor='Waechter')
			AS debt";

		$queryWA = "
			SELECT( 
			    SELECT SUM(value) AS schulden
				FROM  debts t1, transactions t2
				WHERE  t2.id = t1.transaction_id AND t2.recipient='Anuk' AND debtor='Waechter')
			    -(
			    SELECT SUM(value) AS schulden
				FROM  debts t1, transactions t2
				WHERE  t2.id = t1.transaction_id AND t2.recipient='Waechter' AND debtor='Anuk')
			AS debt";

		$resAG = mysql_query($queryAG,$conn);
		$resGW = mysql_query($queryGW,$conn);
		$resWA = mysql_query($queryWA,$conn);

		if(mysql_num_rows($resAG) == 1 && mysql_num_rows($resGW) == 1 && mysql_num_rows($resWA) == 1) {
			$debtAG = mysql_fetch_array($resAG);
			$debtGW = mysql_fetch_array($resGW);
			$debtWA = mysql_fetch_array($resWA);

			echo($debtAG['debt'].";".$debtGW['debt'].";".$debtWA['debt']);
		}
	};	

	function addEntry($conn, $recipient, $debtsA, $debtsG, $debtsW, $debtsWG, $comment, $billdate) {
		echo("wooh!");
	};
?>