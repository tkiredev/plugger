<?php
 if (isset($_GET['submit'])) {
 
            /** ///////////////// **/
            $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
            $final = 2;
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

foreach($row as $rows){	
?>
<div class="profile">
 <div class="var_1">
 <img alt="avatar" src=""/>
 </div>
 <div class="var_2">
 <abbr><a class="btn-1" href=""><?=$rows->name_user;?></a></abbr>
 <img class="img-mini" alt="logo" src=""/>
 <a class="btn btn-2 size-m" href="" alt="j">ser amigos</a>
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
                    echo "<a href='search.php?buscador=" . $buscador . "&submit=Buscar&pagina=1'>|Inico|</a>";
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
