<?php
$db = new db(); 
$http = "";	
 $query = $db->connect()->query("SELECT * FROM public LIMIT 0,100");
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
  //$paginationCtrls = "GOODBYE :(";
      $paginationCtrls .= '<a href="'.$domain.'?'.$http.'&pn='.$next.'" class="btn-1 pg-next">Siguiente &rarr;</a> ';
    }
?>