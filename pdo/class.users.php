<?PHP
#CONEXION
require('conexion.class.php');
#require methods
require('require.php');

class users EXTENDS db{
 public $access;
 public $msg_index;
 public function __construct(){
  //verificar si hay acceso a toda la matriz.
  $valid = $this->connect()->query("SELECT access,msg_index FROM notification")->fetch(PDO::FETCH_OBJ);

  if($valid->access > 0 ){
  $this->access = true;
 }else{
 $this->access = false;
 $this->msg_index = $valid->msg_index;
 }
 }
 function get_users($num,$pass){
 $sql = $this->connect()->prepare("SELECT id,rol FROM usernames WHERE numer = :num and password = :pass");
 $sql->bindParam(':num',$num,PDO::PARAM_STR);
 $sql->bindParam(':pass',$pass,PDO::PARAM_STR);
 $sql->execute();
 return $sql;

 //cerrar recurso.
 $sql->closeCursor();
 $slq = null;
 }

 function get_user($id_user){
 //validar el ID.
 
 $sql = $this->connect()->prepare("SELECT * FROM usernames WHERE id = :id_user");
 $sql->execute([":id_user" => preg_replace('#[^0-9]#','',$id_user)]);
 return
 $sql->fetch(PDO::FETCH_OBJ);
 //cerrar recurso.
 $sql->closeCursor();
 $slq = null;
 }

 #SESSION
 function session($sesion,$cook,$rol){
  if($sesion > 0 AND $this->access == true ){
//hay session.
 if($sesion > 0 AND $this->access == true AND $rol >0){
 header("location:/");
}
}else if($cook > 0 AND $this->access == true){
if(empty($sesion)){
$_SESSION["username"] = $cook;
header("location:{$_SERVER['PHP_SELF']}");
 }
}else if($sesion > 0 AND $this->access == false AND $rol >0){
//hay acceso para usuario autorizado.
echo "<div class='notif'><p>modo: DEMO.</p><a class='btn btn-1' href='../admin'>volver al panel.</a></div>";
}else{
header('location: /');
}

 }

 public function notif(){
 #extrer todo los detalles del sistema.
 return $this->connect()->query("SELECT * FROM notification")->fetch(PDO::FETCH_ASSOC)["notif"];
 }

#GET COUNT COMENTS
 function get_coment($token){
  return
  $sql = $this->connect()->query("SELECT  *  FROM coments WHERE id_token = '$token'");
 //cerrar recurso.
 $sql->closeCursor();
 $slq = null;
 }
#COMENT
 function coment($id_token){
  $sql = $this->connect()->query("SELECT  *  FROM public WHERE id_token = '$id_token'");
  return $sql->fetch(PDO::FETCH_OBJ);
 //cerrar recurso.
 $sql->closeCursor();
 $slq = null;
 }
#insert coment
 function insert_coment($arg){
  $array = json_decode($arg);
  $sql = $this->connect()->query("INSERT INTO coments(id_token,id_user,texts,timestamp) VALUES('{$array->id_token}','{$array->id_user}','{$array->texts}','{$array->timestamp}') ");
  //cerrar recurso.
 $sql->closeCursor();
 $slq = null;
 }
 #mostrar todos los likes
 function get_likes($id_token){
  return 
  $sql = $this->connect()->query("SELECT * FROM likes WHERE id_token = '$id_token'");
  //cerrar recurso.
 $sql->closeCursor();
 $slq = null;
 }
 #nuevos mensajes.
 function messages($user_id){
 return $this->connect()->query("SELECT 
id FROM messages WHERE token_user = '{$user_id}' AND view = 0");
 }
 #solicitudes de amistad.
 function solicitudes($user_id){
 return
 $this->connect()->query("SELECT id_users,id_user FROM friends WHERE id_users = '{$user_id}' AND value < '1'");
 } 
 public function friends($id){
 //verificar la lista de amigos.
 return $this->connect()->query("SELECT id_user,id_users,value FROM friends WHERE id_users = '{$id}' AND value = '1' || id_user = '{$id}' AND value = '1'");
 }
 public function valid_friend($user,$users){
 //verificar si está en la lista de amigos. y si existe un chat con la persona.

 #id_usuario:
 $id_user = $user;
 #id_users:
 $id_users = $users;
 
 $friend_valid = $this->connect()->query("SELECT id AS 'yes_friend' FROM friends WHERE id_user = '{$id_user}' AND id_users = '{$id_users}' AND value > '0' OR id_users = '{$id_user}' AND id_user = '{$id_users}' AND value > '0'");

 //verificar si existe en la lista de amigos.
 if($friend_valid->rowcount('queryString') > 0 || $id_users == $id_user){
  //el usuario está en la lista de amigos.
 return true;
  }else{
  //el usuario no existe en la lista de amigos.
 return false;
  }
 }

}# => ENDCLASS
?>