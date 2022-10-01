<?PHP
//include bots
include('robot.php');

#CLEANER CHAN
function cleaner($e){
//$st = filter_var($e,FILTER_SANITIZE_STRING);
//mostrar URL.
//$st = preg_replace('/(https{0,1}:\/\/[\w\-\.\/#?&=]*)/','<a href="$1" target="_blank">$1</a>',$st);

//cometír HASTACH.
return $e;
 //preg_replace("/#([^\s]+)/","<b>#$1</b>",$st);
}

#TOKEN GENERATE
function token($x){
//generador de token seguros.
return bin2hex(random_bytes($x - $x % 2 / 2));
}
#TIME STAMP
function timestamp($time){
 $dateTime = new\DateTime();
 $dateTime->setTimestamp($time);

 $new = new\dateTime('now');
 $diff = $new->diff($dateTime);
 $return = 'hace ';

  #AÑO
  if($diff->y >0)
  $return .= $diff->y > 1?
  "{$diff->y} años":
  "{$diff->y} año";

  #MES
  elseIF($diff->m >0)
  $return .= $diff->m > 1?
  "{$diff->m} meses":
  "{$diff->m} mes";

  #DIA
  elseIF($diff->d >0)
  $return .= $diff->d > 1?
  "{$diff->d} días":
  "{$diff->d} dia";

  #HORA
  elseIF($diff->h >0)
  $return .= $diff->h > 1?
  "{$diff->h} horas":
  "{$diff->h} hora";

  #MINUTO
  elseIF($diff->i > 0)
  $return .= $diff->i > 1?
  "{$diff->i} minutos":
  "{$diff->i} minuto";

  #SEGUNDO
  elseIF($diff->s >0)
  $return .= $diff->s > 1?
  "{$diff->s} segundos":
  "{$diff->s} segundo";
  else $return = 'ahora';
  return $return;
        
}
//comprobar a si existe un avatar
function valid_avatar($avatar){
$return = "";
$return .= (strlen($avatar->avatar) >= 1) ?
"<img src='../pictures/{$avatar->avatar}' alt='avatar/{$avatar->name_user}'>":
 "<img src='../avatar.png' alt='{$avatar->name_user}'>";
return $return;
}


#HASTAH
function hastah($st){
return $st;

 //preg_replace("/#([^\s]+)/","<b>#$1</b>",$st);
}
#Url detect
function url_detect($st){
return
preg_replace('/(https{0,1}:\/\/[\w\-\.\/#?&=]*)/','<a href="$1" target="_blank">$1</a>',$st);
}

?>