<?PHP
//clase conexión.
class db{
 function connect(){
 //conexión ala base de datos.
 try{

$host = "";
$dbname = "";
$username = "";
$password = "";

$conect = new PDO('mysql:host=$host;dbname=$dbname',$username,$password);
$conect ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
return $conect;
 }catch(PDOException $e){
 echo "<div class='notif'>No se pudo conectar ala base de datos</div>";
 error_reporting(0);
 return false;
 $e->getMessage();
 return false;
 }
 
 }
}
?>
