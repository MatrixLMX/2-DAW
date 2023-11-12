<?php 
function usuarioOK($name,$pass){
    $verpass = "";
    for($i = strlen($pass)-1;$i>=0;$i--){
        $verpass .= $pass[$i];
    }
    if($verpass == $name){
        return true;
    } else {
        return false;
    }
}


    
    

?>