<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getData
 *
 * @author Dela
 */
 include ("read.php");
 include ("printt.php");
class getData {
    
   
    
    private $students;
    private $Labs;
   
    
    
     function __getTheData() {
        // read raw data 
         $r = new read;
         $this->students = $r->__readAllInfo("f1.txt");
         $this->Labs = $r->__readAllInfo("f2.txt");
         
         
         
         // Print out students and Labs
         $p = new printt;
         $p->__printStudents($this->students);
         $p->__printLabs($this->Labs);
     }
     
     function __getLabs() {
         return $this->Labs;
         
     }
     function __getStudents() {
         
          return $this->students;
     }
    
    
    //put your code here
}
