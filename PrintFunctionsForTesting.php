<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PrintFunctionsForTesting
 *
 * @author Dela
 */
class PrintFunctionsForTesting {
   function __printWeight($weight) {
       
       echo "<br>";
       echo "................weightings..................";
        foreach($weight as $x => $x_value) {
             for ($i = 0; $i < 5; $i++) {
                echo $weight[$x][$i] . " ";
             }
             echo "<br>";
             }   
   }
}
