<?php
session_start();
define ('FPAG',10); // Número de filas por página


require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';

//---- PAGINACIÓN ----
$midb = AccesoDatos::getModelo();
$totalfilas = $midb->numClientes();
if ( $totalfilas % FPAG == 0){
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if ( !isset($_SESSION['posini']) ){
  $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//------------

// Borro cualquier mensaje "
$_SESSION['msg']=" ";

ob_start(); // La salida se guarda en el bufer
if ($_SERVER['REQUEST_METHOD'] == "GET" ){
    // Proceso las ordenes de navegación
    if ( isset($_GET['nav'])) {
        switch ( $_GET['nav']) {
            case "Primero"  : $posAux = 0; break;
            case "Siguiente": $posAux +=FPAG; if ($posAux > $posfin) $posAux=$posfin; break;
            case "Anterior" : $posAux -=FPAG; if ($posAux < 0) $posAux =0; break;
            case "Ultimo"   : $posAux = $posfin;
        }
        $_SESSION['posini'] = $posAux;
    }


     // Proceso las ordenes de navegación en detalles
    if ( isset($_GET['nav-detalles']) && isset($_GET['id']) ) {
     switch ( $_GET['nav-detalles']) {
        case "Siguiente": 
            if($_GET['id']==$totalfilas){
                $db = AccesoDatos::getModelo();
                $cli = $db->getCliente($_GET['id']);
                include_once "app/views/detalles.php";
            }else{
                crudDetallesSiguiente($_GET['id']); break;
            }
            
        case "Anterior" : 
            if($_GET['id'] == 1){
                $db = AccesoDatos::getModelo();
                $cli = $db->getCliente($_GET['id']);
                include_once "app/views/detalles.php";
            }else{
                crudDetallesAnterior($_GET['id']); break;
            }
            
        
    }
     }

    


    // Proceso de ordenes de CRUD clientes
    if ( isset($_GET['orden'])){
        switch ($_GET['orden']) {
            case "Nuevo"    : crudAlta(); break;
            case "Borrar"   : crudBorrar   ($_GET['id']); break;
            case "Modificar": crudModificar($_GET['id']); break;
            case "Detalles" : crudDetalles ($_GET['id']);break;
            case "Terminar" : crudTerminar(); break;
        }
    }
} 
// POST Formulario de alta o de modificación
else {
    if (  isset($_POST['orden'])){
         switch($_POST['orden']) {
             case "Nuevo"    : crudPostAlta(); break;
             case "Modificar": crudPostModificar(); break;
             case "Detalles":; // No hago nada
         }
    }

    if ( isset($_POST['nav-modificar']) && isset($_POST['id']) ) {
        switch ( $_POST['nav-modificar']) {
            case "Siguiente": 
                if($_POST['id']==$totalfilas){
                    $db = AccesoDatos::getModelo();
                    $cli = $db->getCliente($_POST['id']);
                    $orden = "Modificar";
                    include_once "app/views/formulario.php";
                }else{
                    crudModificarSiguiente($_POST['id']); break;
                }
                
            case "Anterior" : 
                if($_POST['id'] == 1){
                    $db = AccesoDatos::getModelo();
                    $cli = $db->getCliente($_POST['id']);
                    $orden = "Modificar";
                    include_once "app/views/formulario.php";
                }else{
                    crudModificarAnterior($_POST['id']); break;
                }
            
        }
     }
}

// Si no hay nada en la buffer 
// Cargo genero la vista con la lista por defecto
if ( ob_get_length() == 0){
    $db = AccesoDatos::getModelo();
    if(isset($_GET['id'])){
        if($_GET['id']=='asc'){
            $_SESSION['ordenar'] = 'order by id asc';
        }else{
            $_SESSION['ordenar'] = 'order by id desc';
        }
    }
    if(isset($_GET['first_name'])){
        if($_GET['first_name']=='asc'){
            $_SESSION['ordenar'] = 'order by first_name asc';
        }else{
            $_SESSION['ordenar'] = 'order by first_name desc';
        }
    } 
    if(isset($_GET['email'])){
        if($_GET['email']=='asc'){
            $_SESSION['ordenar'] = 'order by email asc';
        }else{
            $_SESSION['ordenar'] = 'order by email desc';
        }
    }
    if(isset($_GET['gender'])){
        if($_GET['gender']=='asc'){
            $_SESSION['ordenar'] = 'order by gender asc';
        }else{
            $_SESSION['ordenar'] = 'order by gender desc';
        }
    }
    if(isset($_GET['ip_address'])){
        if($_GET['ip_address']=='asc'){
            $_SESSION['ordenar'] = 'order by ip_address asc';
        }else{
            $_SESSION['ordenar'] = 'order by ip_address desc';
        }
    }



    $posini = $_SESSION['posini'];
    
    $tvalores = $db->getClientes($posini,FPAG);
    require_once "app/views/list.php";    
}
$contenido = ob_get_clean();
$msg = $_SESSION['msg'];
// Muestro la página principal con el contenido generado
require_once "app/views/principal.php";



