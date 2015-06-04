<?php		



	/* A REUSABLE PROCEDURE TO CREATE A MODULE DROPDOWN LIST */

        

	include 'validate_login.php';

    // Provide a list of all registered Modules to select from
    try {
		if ($conn == NULL){ // there is no connection to the DB open
			//connect to DB
			require_once 'dbconfig.php';

			$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
                    
        $query_all_modules = $conn->prepare("SELECT Module_Code, Module_Name
                                             FROM Modules;");
        $query_all_modules->execute();
        echo "<select name=\"module\">";
        echo "<option value=\"$module\" selected>$module</option>";
        
        while ($selected_module = $query_all_modules -> fetch(PDO::FETCH_ASSOC)) {
            echo "<option value=\"$selected_module[Module_Code] $selected_module[Module_Name]\">$selected_module[Module_Code] $selected_module[Module_Name]</option>";
        }

        echo "</select>";

    }
    catch(PDOException $e){
        $err_output = $e->getMessage();				
        echo "Error4: " . $err_output;
        $current .= $err_output;
        $current .= "\n********************* PDO ERROR4 ************************\n";		
        file_put_contents($file, $current);
        $conn = NULL; //close DB connection
    }
    $conn = NULL;
?>
