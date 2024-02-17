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

// function crudEstableceFoto($id) {
//     $db = AccesoDatos::getModelo();
//     $cli = $db->getClienteSiguiente($id);
    
// }

function imprimirPDF($id) {
    ob_start();
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    include_once "app/views/postImprimir.php";
    $contenido = ob_get_clean();


    require_once "vendor/autoload.php";

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($contenido);
    $mpdf->Output();
}

function crudDetalles($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $bandera = $db->estableceBandera($cli->ip_address); 
    $fotoPerfil = $db->estableceFoto($id);
    include_once "app/views/detalles.php";
    
}


function crudDetallesSiguiente($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    $bandera = $db->estableceBandera($cli->ip_address); 
    $fotoPerfil = $db->estableceFoto($id+1);
    include_once "app/views/detalles.php";
}

function crudDetallesAnterior($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    $bandera = $db->estableceBandera($cli->ip_address); 
    $fotoPerfil = $db->estableceFoto($id-1,);
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
    
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_nombre = $_FILES['foto']['name'];
        $foto_tamano = $_FILES['foto']['size'];
        $foto_tipo = $_FILES['foto']['type'];

        $permitidos = array('image/jpeg', 'image/png');
        if(in_array($foto_tipo, $permitidos) && $foto_tamano <= 500 * 1024) { 
            $ruta_destino = 'app/uploads' . $foto_nombre; 
            move_uploaded_file($foto_temp, $ruta_destino);
            
            $cli->ruta_foto = $ruta_destino;
        } else {
            $_SESSION['msg'] = 'Error: El archivo debe ser una imagen jpg o png y tener un tamaño inferior a 500 Kbps.';
            $orden = 'Nuevo';
            include_once 'app/views/formulario.php';
            return;
        }
    }
    $regex = "/\d{3}-\d{3}-\d{4}/i";
    if(preg_match($regex,$cli->telefono)){
        $_SESSION['msg'] = "Teléfono no válido";
        $orden = 'Nuevo';
        include_once 'app/views/formulario.php';
    }else if(!filter_var($cli->ip_address, FILTER_VALIDATE_IP)) {
        $_SESSION['msg'] = 'Dirección IP no válido';
        $orden = 'Nuevo';
        include_once 'app/views/formulario.php';
    }else if($db->emailRepetido($cli->email,$cli->id)){
        $_SESSION['msg'] = ' El correo está repetido';
        $orden = 'Nuevo';
        include_once 'app/views/formulario.php';
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
    $regex = "/^\d{3}-\d{3}-\d{4}$/";
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_nombre = $_FILES['foto']['name'];
        $foto_tamano = $_FILES['foto']['size'];
        $foto_tipo = $_FILES['foto']['type'];

        $permitidos = array('image/jpeg', 'image/png');
        if(in_array($foto_tipo, $permitidos) && $foto_tamano <= 500 * 1024) { 
            $ruta_destino = 'app/uploads' . $foto_nombre; 
            move_uploaded_file($foto_temp, $ruta_destino);
            
            $cli->ruta_foto = $ruta_destino;
        } else {
            $_SESSION['msg'] = 'Error: El archivo debe ser una imagen jpg o png y tener un tamaño inferior a 500 Kbps.';
            $orden = 'Modificar';
            include_once 'app/views/formulario.php';
            return;
        }
    }
    if(!preg_match($regex,$cli->telefono)){
        $_SESSION['msg'] = "Teléfono no válido";
        $orden = 'Modificar';
        include_once 'app/views/formulario.php';
    }else if(!filter_var($cli->ip_address, FILTER_VALIDATE_IP)) {
        $_SESSION['msg'] = 'Dirección IP no válido';
        $orden = 'Modificar';
        include_once 'app/views/formulario.php';
    }else if($db->emailRepetido($cli->email,$cli->id)){
        $_SESSION['msg'] = ' El correo está repetido';
        $orden = 'Modificar';
        include_once 'app/views/formulario.php';
    }else if ( $db->modCliente($cli) ){
        $_SESSION['msg'] = " El usuario ha sido modificado";
        $orden = 'Modificar';
        $posini = 0;
        
        $db = AccesoDatos::getModelo();
        $tvalores = $db->getClientes($posini,FPAG);
        include_once 'app/views/list.php';
    } else {
        $_SESSION['msg'] = " Error al modificar el usuario ";
        $orden = 'Modificar';
        include_once 'app/views/formulario.php';
    }
    $msg = $_SESSION['msg'];
}
