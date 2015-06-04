<?php
	
	
	/* EDIT PHD STUDENT/SUPPORT STAFF DETAILS BY THE ADMIN */
	


	$header = 'Edit Staff'; /* use this variable to set the header string */

	include 'template-top.php';

	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 
	

	echo "You are about to edit <strong>PhD Student</strong> or <strong>Support Staff</strong>'s data<br>";
	
	include 'php_functions.php';

	$student_ID = $_GET["student_ID"]; // get the ID of the student record to be edited
	$student_ID = test_input($student_ID);

	$conn = NULL; // connection to the DB is not open
	$err = ""; //error message

	$fields = array(); // array to store DB table field names
	$types = array(); // array to store DB table field types
	$field_value = array(); // array to store DB table field values to be sent to the DB
    $today = date("Y-m-d"); // today's date

	$location = "edit_phd.php";

	$message=$_GET["message"]; //the message is either "" (if this page is hit for the first time) or "Form is succefully submitted"
	$message = test_input($message);
	
	if ($student_ID != "") {
	
?>
    
        <br>
            <div class = "update">
            <form name="edit_phd" method="post" onSubmit="return verifyEditPHD(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF?student_ID=$student_ID&message=&warning="]); ?>">
    
    
<?php
    
        // show the form for updating the chosen PhD Student's details
        try {
            $describe = array(); // temporary array to store DB table field names and types
            $num_precision = array(); // array to store DB table field lengths
                    
            //connect to DB
            require_once 'dbconfig.php';
    
            $conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $query_find_student = $conn -> prepare("SELECT * 
                                                    FROM PHD_Students 
                                                    WHERE Account_ID = $student_ID;"); // get the person's record
            $query_find_student -> execute();
            
            $query_describe = $conn -> prepare("DESCRIBE PHD_Students;"); // find the field names and field types 
            $query_describe -> execute();
                    
            echo "<br>";
            echo "<table>";
            echo "<tr>";
                    
            while ($describe = $query_describe -> fetch(PDO::FETCH_ASSOC)) {
                $field_name = ""; // to store formatted table field name				
                echo "<th>";					
                if ($describe['Type'] == 'date'){ // set the field length for date type
                    $num_precision[$describe['Field']] = 10;
                } else {
                    $num_precision[$describe['Field']] = filter_var($describe['Type'], FILTER_SANITIZE_NUMBER_INT); // find the field length for other field types from the DB (the command is not suitable for example, for decimals with precision (5,2) types)
                }
                $fields[] = $describe['Field'];
                $field_name = str_replace("_", " ", $describe['Field']);
                $types[$describe['Field']] = $describe['Type'];
				if (substr($field_name, 0, 5) == "Skill"){
					$field_name = substr($field_name, 5); // display formatted field name from the DB without 'Skill' in the beginning
				} 
				print_r($field_name); // display formatted field name from the DB
                echo "</th>";
            }
            echo "</tr>";
    
            while ($person = $query_find_student -> fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($fields as $f){
                    $field_value[$f] = $person[$f];
                    echo "<td>";
                    if ($f != "Account_ID"){
                        if ($f == "Title"){ //to select title from drop-down list
                            echo "<select name=\"$f\" title=\"$f\">
                                    <option value=\"$field_value[$f]\" selected>$field_value[$f]</option>
                                    <option value=\"Mr\">Mr</option>
                                    <option value=\"Miss\">Miss</option>
                                    <option value=\"Ms\">Ms</option>
                                    <option value=\"Mrs\">Mrs</option>
                                    <option value=\"Dr\">Dr</option>
                                    <option value=\"Prof\">Prof</option>
                                </select>";
                        } else if ($types[$f] == "date"){ //to input date
                            echo "<input type=\"text\" name=\"$f\" title=\"$f\" placeholder=\"(YYYY-MM-DD)\"  maxlength=\"$num_precision[$f]\" onKeyUp=\"checkLen(this.value)\" value=\"$field_value[$f]\">";
                        } else if (substr($f, 0, 5) == "Skill") { // to select Skills level for each Skill. Will allow to add more Skill fields to the DB without changing this code
                            echo "<select name=\"$f\" title=\"$f\">
                                    <option value=\"$field_value[$f]\" selected>$field_value[$f]</option>
                                    <option value=\"0\">0 - NO</option>
                                    <option value=\"1\">1 - MAYBE</option>
                                    <option value=\"2\">2 - YES</option>
                                </select>";
                        } else if ($f == "Status") { // to select Status of the Phd Student (Confirmed = can be selected for teaching
                                                                                             // Pending = need more info about PhD Student
                                                                                             // Cancelled = student is no longer on the schedule)
                            echo "<select name=\"$f\" title=\"$f\">";
                            
                            echo "<option value=\"$field_value[$f]\" selected>$field_value[$f]</option>";
    
                            $query_all_status_definitions = $conn->prepare("SELECT Status 
                                                                           FROM Status_Definitions;");
                            $query_all_status_definitions->execute();
                            
                            while ($status = $query_all_status_definitions -> fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value=\"$status[Status]\">$status[Status]</option>";
                            }
                            
                            echo "</select>";
                            
                        } else if ($f == "Year") { // to select year of study for PhD students
                            echo "<select name=\"$f\" title=\"$f\">
                                    <option value=\"$field_value[$f]\" selected>$field_value[$f]</option>
                                    <option value=\"\">N/A</option>								
                                    <option value=\"1\">1</option>
                                    <option value=\"2\">2</option>
                                    <option value=\"3\">3</option>
                                    <option value=\"4\">4</option>
                                </select>";
                                
                        } else if ($f == "Supervisor") { // to select Supervisor of the PhD Student
                                
                            $query_who_has_supervisor = $conn->prepare("SELECT DISTINCT S.Account_ID, S.Forename, S.Surname 
                                                                       FROM Supervisors S 
                                                                       WHERE S.Account_ID = '$field_value[$f]';");
                            $query_who_has_supervisor->execute();
                            echo "<select name=\"$f\" title=\"$f\">";
                            
                            if ($field_value[$f] == NULL){
                                echo "<option value=\"$field_value[$f]\" selected></option>";
                            }
                            
                            while ($selected_supervisor = $query_who_has_supervisor -> fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value=\"$field_value[$f]\" selected>$selected_supervisor[Surname] $selected_supervisor[Forename]</option>";
                            }
                            
                            echo "<option value=\"\">N/A</option>";
    
                            $query_all_supervisors = $conn->prepare("SELECT Account_ID, Forename, Surname 
                                                                    FROM Supervisors 
                                                                    ORDER BY Surname;");
                            $query_all_supervisors->execute();
                            
                            while ($supervisor = $query_all_supervisors -> fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value=\"$supervisor[Account_ID]\">$supervisor[Surname] $supervisor[Forename]</option>";
                            }
                            
                            echo "</select>";
                            
                        } else if ($f == "Other_Training"){ // to type other training in a textarea field
                            echo "<textarea name=\"$f\" cols=\"20\" rows=\"5\" title=\"$f\" maxlength=\"$num_precision[$f]\" onKeyUp=\"checkLen(this.value)\" >$field_value[$f]</textarea>";
                        } else { // for all other fields
                            echo "<input type=\"text\" name=\"$f\" title=\"$f\" maxlength=\"$num_precision[$f]\" onKeyUp=\"checkLen(this.value)\" value=\"$field_value[$f]\">";
                        }
                    } else { // un-editable field for Account_ID
                        echo "<input type=\"text\" name=\"$f\" title=\"$f\" value=\"$field_value[$f]\" disabled=\"disabled\">"; 
                    }
                    echo "</td>";
                }
                echo "</tr>";
            } // end while loop
            
            echo "</table>";
            echo "<br>";
            echo "<input name=\"btn_update\" type=\"submit\" title=\"Update PhD student data\" value=\"Update\">";
            echo "</form></div><br>";
			
            if ($field_value["Paperwork_Renew"] != "" && $field_value["Paperwork_Renew"] != NULL && $field_value["Paperwork_Renew"] <= $today){
            	$warning = "*WARNING: the legal paperwork needs renewing!";
				echo "<span class=\"error\">$warning</span>";
			}

        }
        catch(PDOException $e){
            echo "Error: " . $e->getMessage();
            $conn = NULL; //close DB connection
        }
        
        $conn = NULL; //close DB connection
        
        
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") { // test user input and store the "cleaned" input in variables when form is sent
            foreach ($fields as $f){
                if ($f != "Account_ID") {
                    $field_value[$f] = test_input($_POST["$f"]);
                } else {
                    $field_value[$f] = $_POST["$f"];
                }
            }
        }
    
        
        
        //after "Update" button is pressed
        if (isset($_POST['btn_update'])) { 
            $counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
            $err = ""; // construct the input validation error message
            $warning = ""; // construct warning (not an error)
    
            do { // perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
    
                foreach ($fields as $f){
    
                    if ($f == "Forename" && $field_value[$f] == "") {
                        $err .= "*Please enter first name<br>";
                    }
    
                    if ($f == "Initial" && $field_value[$f] == "") {
                        $field_value[$f] = NULL;
                    }
                
                    if ($f == "Surname" && $field_value[$f] == "") {
                        $err .= "*Please enter surname<br>";
                    }
    
                    if ($f == "Lab_Training" && $field_value[$f] != "" && validateDate($field_value[$f]) != true) {
                        $err .= "*Please enter CORRECT Lab Demonstration training date<br>";
                    } else if ($f == "Lab_Training" && $field_value[$f] != "" && validateDate($field_value[$f]) == true && $field_value[$f] > $today){
                        $err .= "*Lab training date cannot be in future<br>";					
                    } else if ($f == "Lab_Training" && $field_value[$f] == "") {
                        $field_value[$f] = NULL;
                    }
    
                    if ($f == "Tutorial_Training" && $field_value[$f] != "" && validateDate($field_value[$f]) != true) {
                        $err .= "*Please enter CORRECT Tutoring training date<br>";
                    } else if ($f == "Tutorial_Training" && $field_value[$f] != "" && validateDate($field_value[$f]) == true && $field_value[$f] > $today){
                        $err .= "*Tutorial training date cannot be in future<br>";
                    } else if ($f == "Tutorial_Training" && $field_value[$f] == ""){
                        $field_value[$f] = NULL;
                    }
                
                    if ($f == "Marking_Training" && $field_value[$f] != "" && validateDate($field_value[$f]) != true) {
                        $err .= "*Please enter CORRECT Marking training date<br>";
                    } else if ($f == "Marking_Training" && $field_value[$f] != "" && validateDate($field_value[$f]) == true && $field_value[$f] > $today){
                        $err .= "*Marking training date cannot be in future<br>";
                    } else if ($f == "Marking_Training" && $field_value[$f] == ""){
                        $field_value[$f] = NULL;
                    }
    
                    if ($f == "Other_Training" && $field_value[$f] == "") {
                        $field_value[$f] = NULL;
                    }
    
                    if ($f == "Paperwork_Renew" && $field_value[$f] != "" && validateDate($field_value[$f]) != true) {
                        $err .= "*Please enter CORRECT Legal Paperwork Expiry date<br>";
                    } else if ($f == "Paperwork_Renew" && $field_value[$f] != "" && validateDate($field_value[$f]) == true && $field_value[$f] <= $today){
                        $warning = "*WARNING: the legal paperwork needs renewing!";					
                    } else if ($f == "Paperwork_Renew" && $field_value[$f] == ""){
                        $field_value[$f] = NULL;
                    }
                    
                    if ($f == "Supervisor" && $field_value[$f] == "") {
                        $field_value[$f] = NULL;
                    }
                
                    if ($f == "Year" && $field_value[$f] == "") {
                        $field_value[$f] = NULL;
                    }
    
                }
                
                if ($err != "") { // if input error occured
                    $message = ""; //remove any "successful update" message from before
                    echo "<p><span class=\"error\">$err</span></p>";
                    break; //exit the do..while loop
                }			
        
                try {
                    //connect to DB
                    require_once 'dbconfig.php';
    
                    $conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
                    //update PHD_Students table by constructing an SQL query dynamically
                    $sql = "UPDATE PHD_Students SET ";
                    $length = count($fields); // total number of fields in the record
                    $i = 1; // current field counter
                    foreach ($fields as $f){
                        if ($f != "Account_ID" && $i < $length) {
                            $sql .= "$f=:$f, ";
                        } else if ($f != "Account_ID" && $i == $length) {
                            $sql .= "$f=:$f ";
                        }
                        $i++;
                    }
                    $sql .= "WHERE Account_ID=:Account_ID;"; // finish constructing query
                    
					
                    $query_update_student = $conn->prepare($sql);
                    
                    foreach ($fields as $f){
                        $query_update_student->bindParam(":$f", $field_value[$f]); // bind all the parameters in query to their values 
                    }
                    $query_update_student->bindParam(":Account_ID", $student_ID);
					
                    $query_update_student->execute();
    
                    //Now redirect to itself with the success message and exit further script
                    $title = $field_value["Title"];
                    $forename = $field_value["Forename"];
                    $initial = $field_value["Initial"];
                    $surname = $field_value["Surname"];
                    
                    $conn = NULL; //close DB connection
    
                    header("Location: $location?message=Data for $title $forename $initial $surname has been successfully updated. $warning&student_ID=$student_ID");
                    exit;
                
                }
                catch(PDOException $e){
                    echo "Error: " . $e->getMessage();
                    $conn = NULL; //close DB connection
                }
                $conn = NULL; //close DB connection
                $counter = 0;
                
            } while ($counter != 0); //end of do...while loop
    
            unset($_POST['btn_update']);
        }
    
        echo "<p><span class = \"info\"> $message </span></p>";
        
	} else { //student ID is not defined
		
		echo "<br><span class = \"error\"> ERROR: No staff can be been edited (staff ID has not been specified)</span><br>";
	}

	echo "<br><br><br><input type=\"button\" name=\"back\" value=\"Back to manage PhD accounts\" title=\"Back to manage PhD accounts\" onClick=\"location.href='manage_phd.php'\">";

	include 'template-bottom.php';

?>

