<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería PDO *******************
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
    

   // Constructor privado  Patron singleton
   
    private function __construct(){
        try {
            $dsn = "mysql:host=".DB_SERVER.";dbname=".DATABASE.";charset=utf8";
            $this->dbh = new PDO($dsn,DB_USER,DB_PASSWD);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }  

    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }


    // Devuelvo cuantos filas tiene la tabla

    public function numClientes ():int {
      $result = $this->dbh->query("SELECT id FROM Clientes");
      $num = $result->rowCount();  
      return $num;
    } 
    

    // SELECT Devuelvo la lista de Usuarios
    public function getClientes ($primero,$cuantos):array {
        $tuser = [];
        // Crea la sentencia preparada
       // echo "<h1> $primero : $cuantos  </h1>";
        if(isset($_SESSION['ordenar'])){
            $orden = $_SESSION['ordenar'];
        }else{
            $orden = "";
        }
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes $orden limit $primero,$cuantos");
        // Si falla termina el programa
        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    
        if ( $stmt_usuarios->execute() ){
            while ( $user = $stmt_usuarios->fetch()){
               $tuser[]= $user;
            }
        }
                // Devuelvo el array de objetos
        return $tuser;
    }

    public function estableceFoto($id){
        if($id<11){
            if($id>0 && $id<10){
                $fotoPerfil = "app/uploads/0000000$id.jpg";
            }else if($id = 10){
                $fotoPerfil = "app/uploads/00000010.jpg";
            }
        }else{
            $fotoPerfil = "https://robohash.org/$id?>.png";
        }
        return $fotoPerfil;
    }
    
    public function estableceBandera($ip) {
        $direccion = unserialize(file_get_contents("http://ip-api.com/php/$ip?fields=countryCode"));
        if(empty($direccion['countryCode'])) {
            $bandera = "https://img.freepik.com/vector-premium/desconectar-o-conexion-icono-acceso-internet_116137-4493.jpg";
        } else {
            $direccion = strtolower($direccion['countryCode']);
            $bandera = "https://flagcdn.com/112x84/$direccion.png";
        }
        return $bandera;
    }

    public function autenticarUsuario($login,$pass) {
        $db = AccesoDatos::getModelo();
        $stmt = $db->dbh->prepare("SELECT * FROM User WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$login) {
            return false; 
        }
        
        if ($pass === $usuario['password']) {
            return true; 
        } else {
            return false;
        }
    }

    public function getRol($login) {
        $resu=false;
        $stmt=$this->dbh->prepare("SELECT rol FROM User WHERE login = :login");
        $stmt->bindParam(':login',$login);

        if($stmt->execute()){
            if($stmt->rowCount()==0){
                $resu=false;
            }else{
            $resu=$stmt->fetch()[0];
            }
        }
        return $resu;
    }

    public function emailRepetido($email,$id) {
        $stmt_email = $this->dbh->prepare("select count(*) from Clientes where email=:email and id!=:id");
        $stmt_email->setFetchMode(PDO::PARAM_STR);
        $stmt_email->bindParam(":email",$email);
        $stmt_email->bindParam(":id",$id);
        $stmt_email->execute();
        $filas = $stmt_email->fetchColumn();
        if($filas == 0){
            return false;
        }else {
            return true;
        }
    }
      
    // SELECT Devuelvo un usuario o false
    public function getCliente (int $id) {
        $cli = false;
        $stmt_cli   = $this->dbh->prepare("select * from Clientes where id=:id");
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        $stmt_cli->bindParam(':id', $id);
        if ( $stmt_cli->execute() ){
             if ( $obj = $stmt_cli->fetch()){
                $cli= $obj;
            }
        }
        return $cli;
    }

     
    public function getClienteSiguiente($id){

        $cli = false;
        if(isset($_SESSION['ordenar'])){
            $orden = $_SESSION['ordenar'];
        }else{
            $orden = "";
        }

        $stmt_cli   = $this->dbh->prepare("select * from Clientes where id >? $orden limit 1");
        // Enlazo $id con el primer ? 
        $stmt_cli->bindParam(1,$id);
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        if ( $stmt_cli->execute() ){
            if ( $obj = $stmt_cli->fetch()){
               $cli= $obj;
           }
       }
        return $cli;

    }

    

    public function getClienteAnterior($id){

        $cli = false;
        if(isset($_SESSION['ordenar'])){
            $orden = $_SESSION['ordenar'];
        }else{
            $orden = "order by id desc";
        }
        
        $stmt_cli   = $this->dbh->prepare("select * from Clientes where id < ? $orden limit 1");
       // Enlazo $id con el primer ? 
        $stmt_cli->bindParam(1,$id, PDO::PARAM_INT);
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        if ( $stmt_cli->execute() ){
           if ( $obj = $stmt_cli->fetch()){
              $cli= $obj;
          }
        }
       
    return $cli;
    }

    public function ordenarClientes($modo){
        $tuser = [];
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes order by $modo");
        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    
        if ( $stmt_usuarios->execute() ){
            while ( $user = $stmt_usuarios->fetch()){
               $tuser[]= $user;
            }
        }
                // Devuelvo el array de objetos
        return $tuser;
    }
    



    // UPDATE TODO
    public function modCliente($cli):bool{
      
        $stmt_moduser   = $this->dbh->prepare("update Clientes set first_name=:first_name,last_name=:last_name".
        ",email=:email,gender=:gender, ip_address=:ip_address,telefono=:telefono WHERE id=:id");
        $stmt_moduser->bindValue(':first_name', $cli->first_name);
        $stmt_moduser->bindValue(':last_name'   ,$cli->last_name);
        $stmt_moduser->bindValue(':email'       ,$cli->email);
        $stmt_moduser->bindValue(':gender'      ,$cli->gender);
        $stmt_moduser->bindValue(':ip_address'  ,$cli->ip_address);
        $stmt_moduser->bindValue(':telefono'    ,$cli->telefono);
        $stmt_moduser->bindValue(':id'          ,$cli->id);

        $stmt_moduser->execute();
        $resu = ($stmt_moduser->rowCount () == 1);
        return $resu;
    }

  
    //INSERT 
    public function addCliente($cli):bool{
       
        // El id se define automáticamente por autoincremento.
        $stmt_crearcli  = $this->dbh->prepare(
            "INSERT INTO `Clientes`( `first_name`, `last_name`, `email`, `gender`, `ip_address`, `telefono`)".
            "Values(?,?,?,?,?,?)");
        $stmt_crearcli->bindValue(1,$cli->first_name);
        $stmt_crearcli->bindValue(2,$cli->last_name);
        $stmt_crearcli->bindValue(3,$cli->email);
        $stmt_crearcli->bindValue(4,$cli->gender);
        $stmt_crearcli->bindValue(5,$cli->ip_address);
        $stmt_crearcli->bindValue(6,$cli->telefono);    
        $stmt_crearcli->execute();
        $resu = ($stmt_crearcli->rowCount () == 1);
        return $resu;
    }

   
    //DELETE 
    public function borrarCliente(int $id):bool {


        $stmt_boruser   = $this->dbh->prepare("delete from Clientes where id =:id");

        $stmt_boruser->bindValue(':id', $id);
        $stmt_boruser->execute();
        $resu = ($stmt_boruser->rowCount () == 1);
        return $resu;
        
    }   
    
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }

    
}



