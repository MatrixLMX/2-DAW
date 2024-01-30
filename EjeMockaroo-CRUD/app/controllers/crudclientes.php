<?php

function crudBorrar ($id){    
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ( $resu){
         $_SESSION['msg'] = " El usuario ".$id. " ha sido eliminado.";
    } else {
         $_SESSION['msg'] = " Error al eliminar el usuario ".$id.".";
    }

}

function crudTerminar(){
    AccesoDatos::closeModelo();
    session_destroy();
}
 
function crudAlta(){
    $cli = new Cliente();
    $orden= "Nuevo";
    include_once "app/views/formulario.php";
}

function crudDetalles($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $direccion = $db->estableceBandera($cli->ip_address); 
    include_once "app/views/detalles.php";
}

function crudDetallesSiguiente($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    $direccion = $db->estableceBandera($cli->ip_address); 
    include_once "app/views/detalles.php";
}

function crudDetallesAnterior($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    $direccion = $db->estableceBandera($cli->ip_address); 
    include_once "app/views/detalles.php";
}

function crudModificarSiguiente($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    $orden = "Modificar";
    include_once "app/views/formulario.php";
}

function crudModificarAnterior($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    $orden = "Modificar";
    include_once "app/views/formulario.php";
}


function crudModificar($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden="Modificar";
    include_once "app/views/formulario.php";
}

function crudPostAlta(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    // !!!!!! No se controlan que los datos sean correctos 
    $cli = new Cliente();
    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    
    $db = AccesoDatos::getModelo();
    $regex = "/\d{3}-\d{3}-\d{4}/";
    if(preg_match($regex,$cli->telefono)){
        $_SESSION['msg'] = "Teléfono no válido";
    }else if(!filter_var($cli->ip_address, FILTER_VALIDATE_IP)) {
        $_SESSION['msg'] = 'Dirección IP no válido';
    }else if($db->emailRepetido($cli->email)){
        $_SESSION['msg'] = ' El correo está repetido';
    }else if ( $db->addCliente($cli) ) {
           $_SESSION['msg'] = " El usuario ".$cli->first_name." se ha dado de alta ";
        } else {
            $_SESSION['msg'] = " Error al dar de alta al usuario ".$cli->first_name."."; 
        }
    $msg = $_SESSION['msg'];
    
}

function crudPostModificar(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $cli = new Cliente();
    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    
    $db = AccesoDatos::getModelo();
    $regex = "/\d{3}-\d{3}-\d{4}/";
    if(preg_match($regex,$cli->telefono)){
        $_SESSION['msg'] = "Teléfono no válido";
    }else if(!filter_var($cli->ip_address, FILTER_VALIDATE_IP)) {
        $_SESSION['msg'] = 'Dirección IP no válido';
    }else if($db->emailRepetido($cli->email)){
        $_SESSION['msg'] = ' El correo está repetido';
    }else if ( $db->modCliente($cli) ){
        $_SESSION['msg'] = " El usuario ha sido modificado";
    } else {
        $_SESSION['msg'] = " Error al modificar el usuario ";
    }
    $msg = $_SESSION['msg'];
}
