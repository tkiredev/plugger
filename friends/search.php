<?PHP
#CONEXION
require('../pdo/class.users.php');
#iniciar session
session_start();
#incluir clases 
$classU = new users();
$user = $classU->get_user($_SESSION['username']);
#SESSION
$classU->session($_SESSION['username'],$_COOKIE['username'],$_SESSION['rol']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content=":Plugger te conecta con tus amigos">
<link rel="icon" href="../icon.png" type="image/x-icon" sizes="50x50">
<title>search Friends</title>
<!--//
EL CODIGO FUE ECHO APARTIR DE 20 DE OBTUBRE DEL 2019 ALAS 4:04pm.
//-->
<link href="../css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<header>
<div class="img-logo">
<img src="../loggo/loggo.png" alt="icon/plugger"/> 
</div>
<ul class="submenu">
 <li><a href="/">INICIO</a></li>
  <?PHP
 $message_new = $classU->messages($user->id)->rowcount();
 if($message_new > 0){
  $status_message = ($message_new >= 9)? "&nbsp;(9+)":
  "&nbsp;({$message_new})"
   ;
  }else{
  $status_message = null;
  }
 ?>
 <li><a href="../profile">PERFIL <?=$status_message;?></a></li>
<?PHP
//status solicitud.
$status = $classU->solicitudes($user->id)->rowcount();
 if($status > 0){
  $status_add = ($status >= 100)? "&nbsp;(99+)":
  "&nbsp;({$status})"
   ;
  }else{
  $status_add = null;
  }
?>
 <li><a href="../friends">AMIGOS<?=$status_add;?></a></li>
</ul>
</header>

<!--//Friends search//-->
<?PHP
$con = new\db(); 
$con = $con->connect();

 if (isset($_GET['submit'])) {
 
            /** ///////////////// **/
            $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
var_dump($pagina);
            $final = 3;
            $inicio = ($pagina - 1) * $final;
            /**\\\\\\\\\\\\\\\\\\\**/

	        $buscador = $_GET["buscador"];
	        $nquery = $con->query("SELECT * FROM usernames WHERE name_user LIKE '%$buscador%' ORDER BY id DESC LIMIT $inicio, $final");


	        if($num = $nquery->rowcount()){

                /**//////////////////// */
$sql = $con->query("SELECT * FROM usernames");
echo "<abbr>{$num} resultados de {$sql->rowcount()}";
                /* \\\\\\\\\\\\\\\\\\\\ */
$row = $nquery->FETCHALL(PDO::FETCH_OBJ);

foreach($row AS $rows){
?>
<div class="cont-profile">
<div class="profile">
 <div class="var_1">
 <?php
$return = "";
$return .= (strlen($rows->avatar) >= 1) ?
"<img class='img' src='../pictures/{$rows->avatar}' alt='avatar/{$rows->name_user}'>":
 "<img class='img' src='../avatar.png' alt='{$rows->name_user}'>";
echo $return;
?>
 </div>
 <div class="var_2">
 <abbr><a class="btn-1" href="../activities/profile.php?profile=<?=$rows->id;?>"><?=$rows->name_user.'&nbsp;'.$rows->filset;?></a></abbr>
 <img class="icon"src="../icon.png"/>
 <a class="btn btn-2 size-m" href="../activities/profile.php?profile=<?=$rows->id;?>" alt="add_friend">ser amigos</a>
 </div>
</div>
</div>
<?PHP
}

            }else {
                echo 'No existe resultados para :' . $buscador;
            }
        }
        ?>

        <div class="pagination">
            <?php
            /** ///////////////////// */
            if (isset($_GET['buscador'], $_GET['submit'])) {
	            $pquery = $con->query("SELECT * FROM usernames WHERE name_user LIKE '%$buscador%' ORDER BY id DESC");
                $total_records = $pquery->rowcount();
var_dump($total_records);
	            $total_pages = ceil($total_records / $final);
	            $start_loop = $pagina;
	            $diferencia = $total_pages - $pagina;

                if ($diferencia <= 5) {
                    $start_loop = $total_pages - 5;
                }

	            $end_loop = $start_loop + 5;

                /**
                 * //www/ reemplazalo con tu url de tu localhost 
                 */
                if ($pagina > 1) {
                    echo "<a href='localhost:8080/search.php?buscador=" . $buscador . "&submit=Buscar&pagina=1'>|Inico|</a>";
                    echo "<a href='search.php?buscador=" . $buscador . "&submit=Buscar&pagina=" . ($pagina - 1) . "'>|Anterior|</a>";
                }
                for ($i = $start_loop; $i <= $end_loop; $i++) {
                    if ($i > 0) {
                        $class = $i == $pagina ? "class='active'" : "";
                        echo "<a " . $class . " href='search.php?buscador=" . $buscador . "&submit=Buscar&pagina=" . $i . "'> |" . $i . "|</a>";
                    }
                }
                if ($pagina < $end_loop) {
                    echo "<a href='search.php?buscador=" . $buscador . "&submit=Buscar&pagina=" . ($pagina + 1) . "'>|Siguiente |</a>";
                    echo "<a href='search.php?buscador=" . $buscador . "&submit=Buscar&pagina=" . $total_pages . "'>|Ultima</a>";
                }
            }
        ?>
<div class="search">
<strong>Busca amigos</strong>
<label>por nombre, apellido o número de teléfono</label>
<form action="" method="get">
<input class="input-search" type="search" name="buscador" <?php echo isset($_GET['buscador']) ? 'value="' . $_GET['buscador'] . '"' : ''; ?>>
<input class="btn btn-2 size-m" type="submit"  name="submit" value="BUSCAR">
</form>
</div>
</body>
</html>