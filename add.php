<html>
<body style="font-family: 'Helvetica', 'Arial', sans-serif;">
<h1>Kühlschrank Schulden</h1>
<fieldset>
Debug output stuff: <br>
<?php
	
		define('DB_servername','localhost');
		define('DB_username', 'root');
		define('DB_password', 'pokemon1');
		define('DB_name', 'debts');
		define('DB_debts', 'debts');
		define('DB_trans', 'transactions');
		define('DEBT_user1', 'Anuk');
		define('DEBT_user2', 'Gwomm');
		define('DEBT_user3', 'Waechter');
		
		date_default_timezone_set('Europe/Berlin');
		
		// Create connection
		$conn = new mysqli(DB_servername, DB_username, DB_password, DB_name);
		// Check connection
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		} 
		
		if (!$conn->set_charset("utf8")) {
    			printf("Error loading character set utf8: %s\n", $conn->error);
    			exit();
		} else {

    			printf("Current character set: %s\n", $conn->character_set_name());
			echo "<br>";			
		}
		
if ($_POST){
	
	if($_POST['Submit']){
				
		$comment=$_POST['comment'];
		$recipient=$_POST['recipient'];
		$billdate= date("Y-m-d", strtotime($_POST['billdate']));	
		if($_POST[DEBT_user1] + $_POST[DEBT_user2] + $_POST[DEBT_user3] + $_POST["WG"] > 0){
			$sql = "INSERT INTO ".DB_trans." (recipient, comment, billdate)
				VALUES ('$recipient', '$comment', '$billdate')";
				
				if ($conn->query($sql) === TRUE) {
				    echo "Transaction created successfully! ID:".$conn->insert_id."<br>";
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}
			$transaction=$conn->insert_id;
		} else {
			echo "No input!";
		}
			
		
		function addData ($debtor, $value) {
			global $conn, $transaction;
			$value=round($value, 2);
			$sql = "INSERT INTO ".DB_debts." (transaction_id, debtor, value)
			VALUES ('$transaction', '$debtor', '$value')";
			
			if ($conn->query($sql) === TRUE) {
			    echo $debtor."'s Debt successful! ID: ".$conn->insert_id."<br>";
			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}
			
			
		}
		
		if($recipient != DEBT_user1 && $_POST[DEBT_user1] + $_POST["WG"] != 0){
			addData (DEBT_user1, $_POST[DEBT_user1] + $_POST["WG"]/3);
		}
		if($recipient != DEBT_user2 && $_POST[DEBT_user2] + $_POST["WG"] != 0){
			addData (DEBT_user2, $_POST[DEBT_user2] + $_POST["WG"]/3);
		}
		if($recipient != DEBT_user3 && $_POST[DEBT_user3] + $_POST["WG"] != 0){
			addData (DEBT_user3, $_POST[DEBT_user3] + $_POST["WG"]/3);
		}
	}
		
		
	elseif($_POST['Undo'] && $_POST['transaction'] != 0){
		
		$transaction=$_POST['transaction'];
		$sql = "DELETE FROM ".DB_debts." WHERE transaction_id='".$transaction."'";
		if ($conn->query($sql) === TRUE) {
		    echo "Record $transaction deleted successfully from debts <br>";
		} else {
		    echo "Error deleting record: " . $conn->error;
		}
		$sql = "DELETE FROM ".DB_trans." WHERE id='".$transaction."'";
		if ($conn->query($sql) === TRUE) {
		    echo "Record $transaction deleted successfully from transactions <br>";
		} else {
		    echo "Error deleting record: " . $conn->error;
		}
	}
		
	
}
$sql = "SELECT( \n"
    . " SELECT SUM(value) AS schulden\n"
    . "    FROM debts t1, transactions t2\n"
    . "    WHERE t2.id = t1.transaction_id AND t2.recipient=\"Gwomm\" AND debtor=\"Anuk\")\n"
    . " -(\n"
    . " SELECT SUM(value) AS schulden\n"
    . "    FROM debts t1, transactions t2\n"
    . "    WHERE t2.id = t1.transaction_id AND t2.recipient=\"Anuk\" AND debtor=\"Gwomm\")\n"
    . "AS Anuk_Owes_Gwomm";
if($result = $conn->query($sql) === TRUE) {
    echo "works" ;
}else {
    echo "debt overview doesnt work yet";
}
$conn->close();	
?>

</fieldset>
<br>

<form method="post">
<fieldset>
	Empfaenger:    
	<input type="radio" id="sel1" name="recipient" value="Anuk" required>
		<label for="sel1"> Anuk</label>
	<input type="radio" id="sel2" name="recipient" value="Gwomm">
                <label for="sel2"> Gwomm</label>
	<input type="radio" id="sel3" name="recipient" value="Waechter">
                <label for="sel3"> Wächter</label>
	<br>
	Anuk: 	    	<input type="text" pattern="^-?\d+(\.\d{1,2})?$" name="Anuk"><br>
	Gwomm: 	    	<input type="text" pattern="^-?\d+(\.\d{1,2})?$" name="Gwomm"><br>
	Waechter:   	<input type="text" pattern="^-?\d+(\.\d{1,2})?$" name="Waechter"><br>
	WG (total): 	<input type="text" pattern="^-?\d+(\.\d{1,2})?$" name="WG"><br>
	Kommentar:  	<input type="text" pattern="^.{5,200}$" name="comment" required><br>
	Datum Zettel: 	<input type="date"  name="billdate">
	<br><br>

	<input type="submit" name="Submit" value="Submit">
</form>
	<form method="post">
	<input type="submit" name="Undo" value="Undo">
	<input type="hidden" name="transaction" value=<?php echo $transaction ?> >
</form>
</fieldset>

<fieldset>
</fieldset>

<fieldset>
Benutzung: <br>
- Oben Empfänger auswählen <br>
- Schulden der einzelnen Leute eintragen (bitte zusammenrechnen, nicht 50 mal irgendwelche Centbeträge buchen) <br>
- WG Schulden werden automatisch durch 3 geteilt und verrechnet (auch hier bitte einmal alles zusammenrechen)<br>
- Kommentar kurz angeben was unter anderem gekauft wurde (Grünkohl, Milch, Seife, ...) insb. bei Großen Dingen! <br>
- Datum auf dem Kassenzettel auswählen (wann das ganze gekauft wurde)<br>
- Submit/Enter drücken und auf "Successful!" überprüfen <br>
- Bei Fehlerhafter eingabe kann mit "Undo" der letzte (und nur der letzte) Schritt gelöscht werden
</fieldset>
</body>
</html>
