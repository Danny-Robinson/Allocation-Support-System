<?php



	/* A REUSABLE PROCEDURE TO ALLOCATE SUBSTITUTING STUDENTS TO THE SLOTS-LABS (called from confirm_substituton.php) - 6TH PAGE OF 7*/




	include 'validate_login.php';
	
	
	
	$sql_confirm_substitution = $sql_change_allocations;
	$query_confirm_substitution = $query_find_session = $query_change_allocations = NULL;

	// Confirm that the substituted sessions are cancelled
	$sql_confirm_substitution = "UPDATE $new_table_name
								 SET Confirmed = 1, Substitution_Acc = $acc
								 WHERE Lab_ID = $lab AND Slot_ID = $slot;";
								
	$query_confirm_substitution = $conn->prepare($sql_confirm_substitution);
	$query_confirm_substitution->execute();


	// find slot-lab to substitute
	$query_find_session = $conn -> prepare("SELECT Session_ID 
											FROM Lab_Timetable
											WHERE Lab_ID = $lab AND Slot_ID = $slot;");
	$query_find_session->execute();

	$session = NULL;
	while ($found_session = $query_find_session -> fetch(PDO::FETCH_ASSOC)) {
		$session = $found_session['Session_ID'];
	}
	
	if($session == NULL){
		$current .= "\n*** COULD NOT FIND SESSION FOR SLOT: $slot -> LAB: $lab ***\n";
		file_put_contents($file, $current);
		echo "Could not find Session for Slot: $slot -> Lab: $lab!";
		$conn = NULL; //close DB connection
		header("Location: cancel_substitution.php");
	}

	// allocate the found suitable substitute student to the slot-lab
	$sql_change_allocations = "UPDATE Allocations
							   SET Account_ID = $acc, Status = 'Confirmed'
							   WHERE Session_ID = $session AND Account_ID = $student_ID;";

	$query_change_allocations = $conn->prepare($sql_change_allocations);
					
					
/*************** UNCOMMENT THIS TO REALLY PERFORM SUBSTITUTION!!!! **********************/

	$query_change_allocations->execute();
	
/****************************************************************************************/	

?>
	
