<?php

if(isset($_GET['file'])){
    $file = $_GET['file'];
    header("Content-type: image");
    header("Content-disposition: attachment; filename= ".$file."");
    readfile($file);
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

