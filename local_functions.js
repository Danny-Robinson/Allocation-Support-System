<!--
// JavaScript Document

// USER INPUT VALIDATION



// validate Date (http://www.webdeveloper.com/forum/showthread.php?190078-Javascript-Date-%28yyyy-mm-dd%29-validation)
function f(inDate){ 
	var regexp= /^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/;
	var checkDate = inDate.match(regexp);
	return checkDate? true : false;
}	




// verify PhD Student or supporting staff registration
verifyRegistrationStaff = function(theForm){ 
	var message="";
	// email regex: http://stackoverflow.com/questions/18156684/jquery-validation-email-regex-not-working
	var email_patt=/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var get_title = theForm.title.value;

	try {

		if (get_title == "") {
			message+="Please choose title\n";
		}
		
		if (theForm.first_name.value=="") {
			message+="Please enter first name\n";
		}
			
		if (theForm.surname.value=="") {
			message+="Please enter surname\n";
		}
		
		if (theForm.email.value=="") {
			message+="Please enter email address\n";
		} else if (!theForm.email.value.match(email_patt)){
			message+="Please enter VALID email address\n";
		}

	} catch (err) {
    	alert(err);
		return false;
	}
		
	
	if(message!=""){
		alert(message);
		return false;
	}
}





// verify Module Leader registration
verifyRegistrationLecturer = function(theForm){ 
	var message="";
	var email_patt=/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var module_code_patt=new RegExp("([a-zA-Z][a-zA-Z])([a-zA-Z]|\\d)(\\d{3,5})");
	var get_title = theForm.title.value;

	try {

		if (get_title == "") {
			message+="Please choose title\n";
		}
		
		if (theForm.first_name.value=="") {
			message+="Please enter first name\n";
		}
			
		if (theForm.surname.value=="") {
			message+="Please enter surname\n";
		}
		
		if (theForm.email.value=="") {
			message+="Please enter email address\n";
		} else if (!theForm.email.value.match(email_patt)){
			message+="Please enter VALID email address\n";
		}
	
		if (theForm.new_module_code.value == "" && theForm.new_module_name.value == ""){
			if (theForm.module.value=="") {
				message+="Please choose Module\n";
			}
		} else if (theForm.new_module_code.value != "" && theForm.new_module_name.value == ""){
			message+="Please enter the New Module's name\n";
		} else if (theForm.new_module_code.value == "" && theForm.new_module_name.value != ""){
			message+="Please enter the New Module's code\n";
		} else {
			if (!theForm.new_module_code.value.match(module_code_patt)){
				message+="Please enter CORRECT New Module code\n";
			}
		}
		
	} catch (err) {
    	alert(err);
		return false;
	}
		
	if(message!=""){
		alert(message);
		return false;
	}
}





// verify Module registration
verifyRegistrationModule = function(theForm){ 
	var message="";
	var module_code_patt=new RegExp("([a-zA-Z][a-zA-Z])([a-zA-Z]|\\d)(\\d{3,5})");
	
	try {
		
		if (theForm.module_code.value=="") {
			message+="Please enter Module Code\n";
		} else if (!theForm.module_code.value.match(module_code_patt)){
			message+="Please enter CORRECT module code\n";
		}
	
		
		if (theForm.module_name.value=="") {
			message+="Please enter full Module Name\n";
		}
		
	} catch (err) {
    	alert(err);
		return false;
	}
	
		
	if(message!=""){
		alert(message);
		return false;
	}
}





// verify text search field
verifySearch = function(theForm){ 
	var message="";

	try {
		
		if (theForm.find_who.value=="") {
			message+="Please enter your search term\n";
		}
	
	} catch (err) {
    	alert(err);
		return false;
	}


	if(message!=""){
		alert(message);
		return false;
	}	
}






// verify edited phd record (by admin)
verifyEditPHD = function(theForm){ 
	var message="";
	
	//today's date:
	var today = new Date().getTime()/1000;
	
	try{
		
		if (theForm.Forename.value=="") {
			message+="Please enter first name\n";
		}
	
		if (theForm.Surname.value=="") {
			message+="Please enter surname\n";
		}
		
		if (theForm.Lab_Training.value !="" && f(theForm.Lab_Training.value) != true) {
			message+="Please enter CORRECT Lab Demonstration training date\n";
		} else if (theForm.Lab_Training.value !="" && f(theForm.Lab_Training.value) == true && (new Date(theForm.Lab_Training.value).getTime()/1000) > today){
			message+="Lab training date cannot be in future\n";
		}
		
		if (theForm.Tutorial_Training.value !="" && f(theForm.Tutorial_Training.value) != true) {
			message+="Please enter CORRECT Tutoring training date\n";
		} else if (theForm.Tutorial_Training.value !="" && f(theForm.Tutorial_Training.value) == true && (new Date(theForm.Tutorial_Training.value).getTime()/1000) > today){
			message+="Tutorial training date cannot be in future\n";
		}
		
		if (theForm.Marking_Training.value !="" && f(theForm.Marking_Training.value) != true) {
			message+="Please enter CORRECT Marking training date\n";
		} else if (theForm.Marking_Training.value !="" && f(theForm.Marking_Training.value) == true && (new Date(theForm.Marking_Training.value).getTime()/1000) > today){
			message+="Marking training date cannot be in future\n";
		}
		
		if (theForm.Paperwork_Renew.value !="" && f(theForm.Paperwork_Renew.value) != true) {
			message+="Please enter CORRECT Legal Paperwork Expiry date\n";
		} else if (theForm.Paperwork_Renew.value !="" && f(theForm.Paperwork_Renew.value) == true && (new Date(theForm.Paperwork_Renew.value).getTime()/1000) <= today){
			alert("Warning: the legal paperwork needs renewing!\n");
		}

	} catch (err) {
    	alert(err);
		return false;
	}


	if(message!=""){
		alert(message);
		return false;
	}	
	
}







// verify file to upload
verifyUpload = function(theForm){ 
	var message="";

	try {
		
		if (theForm.file_name.value=="") {
			message+="Please choose file to upload\n";
		}
	
	} catch (err) {
    	alert(err);
		return false;
	}


	if(message!=""){
		alert(message);
		return false;
	}	
}








// verify leaving student form
verifyCancelStudent = function(theForm){ 
	var message="";
	var get_student = theForm.student.value;

	try {

		if (get_student == "") {			
			message+="Please select a student/support staff\n";
		}
		
		if (theForm.start_date.value=="") {
			message+="Please enter absense start date\n";
		}
		
		if (theForm.start_date.value !="" && f(theForm.start_date.value) != true) {
			message+="Please enter CORRECT absense start date\n";
		} 
		
		if (theForm.end_date.value=="") {
			message+="Please enter absense end date\n";
		}
		
		if (theForm.end_date.value !="" && f(theForm.end_date.value) != true) {
			message+="Please enter CORRECT absense end date\n";
		} 
		
		if (theForm.start_date.value!="" && theForm.end_date.value!="" && f(theForm.start_date.value) == true && f(theForm.end_date.value) == true && theForm.start_date.value > theForm.end_date.value) {
			message+="The end date must be later than the start date\n";
		}

	} catch (err) {
    	alert(err);
		return false;
	}


	if(message!=""){
		alert(message);
		return false;
	}	
}







// verify edited phd record (by student)
verifyStudentEditPHD = function(theForm){ 
	var message="";
	var email_patt=/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

	try{
		
		if (theForm.Forename.value=="") {
			message+="Please enter first name\n";
		} 
		
		if (theForm.Surname.value=="") {
			message+="Please enter surname\n";
		} 
		
		if (theForm.Email.value=="") {
			message+="Please enter email address\n";
		} else if (!theForm.Email.value.match(email_patt)){
			message+="Please enter VALID email address\n";
		} 
		

	} catch (err) {
    	alert(err);
		return false;
	}


	if(message!=""){
		alert(message);
		return false;
	}	
	
}






// verify Module selected
verifyModuleSelected = function(theForm){ 
	var message="";
	
	try {
		
		if (theForm.module.value=="") {
			message+="Please select Module to view its labs\n";
		}

	
	} catch (err) {
    	alert(err);
		return false;
	}
	
		
	if(message!=""){
		alert(message);
		return false;
	}
}





