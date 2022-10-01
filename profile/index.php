<?PHP
#CONEXION 
include('../pdo/class.users.php');
#iniciar session
session_start();

if(isset($_GET["up"])){
var_dump($_COOKIE["username"]);
setcookie('username',$_COOKIE['username'],time() -1);
} 
#incluir clases 
$classU = new\users();
$db = new\db();
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
<title><?=$user->name_user;?></title>
<!--//
EL CODIGO FUE ECHO APARTIR DE 20 DE OBTUBRE DEL 2019 ALAS 4:04pm.
//-->
<link href="../css/main.css" rel="stylesheet" type="text/css">
<style>
.border_premium{border-left: 4px solid #009186;background:aliceblue;}
.verify{
background: blue;color:red;
font-size:10px;
width:5px;
padding:5px;
height:auto;
border-radius:5px;
}
</style>
</head>
<body>

<script type="text/javascript">
   var uid = '281738';
   var wid = '578613';
   var pop_fback = 'up';
   var pop_tag = document.createElement('script');pop_tag.src='//cdn.popcash.net/show.js';document.body.appendChild(pop_tag);
   pop_tag.onerror = function() {pop_tag = document.createElement('script');pop_tag.src='//cdn2.popcash.net/show.js';document.body.appendChild(pop_tag)};
</script>
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
 <li class="var_1"><a href="">PERFIL <?=$status_message;?></a></li>
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
<!--//Notifi//-->
<div class="notif">
<p>
<?=$classU->notif();?>
</p>
</div>
<div class="cont-profile">
<div class="profile">
<?PHP
//limitar carácteres.
function limit_caract($str){
 $return =  '';
 $length = strlen($str);
 $return .= ($length >= 84)? 
 str_ireplace(substr($str,84,4),'...',substr($str,0,88)): 
 $str;
 return $return;
}
?>
<div class="var_1">
<?php
$return = "";
$return .= (strlen($user->avatar) >= 1) ?
"<img width='500' height='50' src='../pictures/{$user->avatar}' alt='avatar/{$user->name_user}'>":
 "<img src='../avatar.png' alt='{$user->name_user}'>";
echo $return;
?>
</div>
<div class="var_2">
<span><?=$user->name_user.'&nbsp'.$user->filset;?></span>
<abbr><?=limit_caract($user->state)?></abbr>
<abbr><a class="btn-1" href="state.php">Actualiza tu estado</a></abbr>
</div>
</div>
<div class="str">
<input class="btn btn-2" type="button" value="+ Comparte tus fotos"/>
</div>
<ul class="mini-menu">
  <li><a class="btn-1" href="activities.php">actividades</a></li>
  <li><strong>conversiones <?= ($classU->messages($user->id)->rowcount() > 0 )? "&nbsp;({$classU->messages($user->id)->rowcount()})":null;?></strong></li>
  <li><a class="btn-1" href="#">fotos y videos</a></li>
  <li><a class="btn-1" href="info-profile.php">información de perfil</a></li>
 </ul>

<div class="num_message">
<?PHP
 $num_ms = "";
 $new_messages = $classU->messages($user->id)->rowcount();

 if($new_messages > 0){
  $num_ms .= ($new_messages > 1)? "{$new_messages}&nbsp;nuevos mensajes": "{$new_messages}&nbsp;nuevo mensaje";
 }

//mostrar el número de mensajes nuevos.
echo "<span>{$num_ms}</span>";
?>
</div>
<?PHP
//incluir paginación
 $query = $db->connect()->query("SELECT * FROM grou_message WHERE token = '{$user->id}' OR id_user = '{$user->id}'");
 
	$row = $query->rowcount();
	//número de páginas
	$page_rows = 5;
  //nombre de dominio.
  $domain = "";

	$last = ceil($row/$page_rows);
 
	if($last < 1){
		$last = 1;
	}

	$pagenum = null;

	if(isset($_GET['pn'])){
		$pagenum = preg_replace('#[^0-9]#', '', $_GET['pn']);
	}

	if ($pagenum < 1) { 
		$pagenum = 1; 
	} 

	else if ($pagenum > $last) { 
		$pagenum = $last; 
	}

	$limit = 'LIMIT ' .($pagenum - 1) * $page_rows .',' .$page_rows;
	
	/*$nquery = $db->connect()->query("SELECT * FROM coments WHERE id_token = '{$rows->id_token}' ORDER BY Id DESC {$limit}");*/

	$paginationCtrls = '';

	if($last != 1){
		
	if ($pagenum > 1) {
        $previous = $pagenum - 1;
		$paginationCtrls .= '<a href="'.$domain.'?'.$http.'&pn='.$previous.'" class="btn-1 pg-back">← Anterior</a>';
		
	    }
    }

	for($e = $pagenum; $e <= $last; $e++){
		$n = $e;
	}
if($last > 1){
	$paginationCtrls .= "<abbr>  {$pagenum}&nbsp;/&nbsp;{$n}</abb>";
}else{
$paginationCtrls = "";
}

    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= '<a href="'.$domain.'?'.$http.'&pn='.$next.'" class="btn-1 pg-next">Siguiente &rarr;</a> ';
    }

$sql = $db->connect()->query("SELECT * FROM grou_message WHERE token = '{$user->id}' OR id_user = '{$user->id}' ORDER BY timestamp DESC {$limit}");


foreach($sql->FETCHALL(PDO:: FETCH_OBJ) AS $row){
$num_message =
$db->connect()->query("
SELECT id FROM messages WHERE 
 token_user = '{$user->id}' AND id_user = '{$row->id_user}' AND view = 0
 || token_user = '{$user->id}' AND id_user = '{$row->token}' AND view = 0 ")->rowcount("queryString");
//cambiar estado.
$state = ($row->token == $user->id) ? $row->id_user:$row->token;

$get_user = $classU->get_user($state);
//echo "número de mensajes: {}";
//se activa el color naranja si se inicializo una conversación

$active = ($row->token_id == $user->id AND $row->status == 0 || $num_message > 0)? "active_border":null;

$premium = ($row->token_id == 67629 AND $num_message > 0)? "border_premium":false;

?>
<div class="cont-messages">
<div class="messages <?=($premium == false)? $active:$premium;?>">
<div class="mini-profile">
 <div class="var_1">
 <?=
  valid_avatar($get_user);
  ?>
 </div>
 <div class="var_2">
 <span>
 <a class="btn-1" href="menssage.php?token=<?=$get_user->id;?>"><?=$get_user->name_user."&nbsp;".$get_user->filset;?></a></span><strong><?= ($num_message > 0 )? "&nbsp;({$num_message})":null;?>
 </strong>
 </div>
</div>
<abbr>
 <?PHP
  if(!empty($row->message)){
  echo $row->message;
  }
 ?>
 </abbr>
<div class="v">
<abbr>
<?=timestamp($row->timestamp);?>
</abbr>
</div>

</div>
</div>
<?PHP }?>
<div class="pg">
<?=$paginationCtrls;?>
</div>
<!--//nueva conversación//-->
<div class="str">
<a href="../friends"><input class="btn btn-2" type="button" value="Nueva conversación"/></a>
</div>
</body>
</html>