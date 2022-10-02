<?PHP
//clase conexión.
class db{
 function connect(){
 //conexión ala base de datos.
 try{
$conect = new PDO('mysql:host=189.85.36.34;port=3306;dbname=pluggert_freefire',"pluggert","jaimesfer2001");
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
