<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of read
 *
 * @author Dela
 */
class read {

    function __readAllInfo($file) {
   $file = fopen($file,"r");

        while(! feof($file))
            {
            $students = fgetcsv($file);
            $arrlength = count($students );
             for($x = 0; $x < $arrlength - 1; $x++) {
                 $student[$students[0]][$x] = $students[$x + 1];
             }
            //print_r($students);
            //echo "<br>";
            }
      
    fclose($file);
   return $student;
    }
    
}    

