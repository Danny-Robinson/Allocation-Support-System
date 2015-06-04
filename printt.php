<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of printt
 *
 * @author Dela
 */
class printt {
    function __printStudents($student) {
      foreach($student as $x => $x_value) {
              echo "Key=" . $x . "      ";
             for ($i = 0; $i <= 64; $i++) {
                echo $student[$x][$i] . " ";
             }
             echo "<br>";
      }
      
     }
     
     
      function __printlabs($student) {
      foreach($student as $x => $x_value) {
              echo "Key=" . $x . "      ";
             for ($i = 0; $i <= 18; $i++) {
                echo $student[$x][$i] . " ";
             }
             echo "<br>";
      }
      
     }
}
