<?php
/*
Fecha: Jueves, 18 de Junio del 2020
Autor: Julio Quiroga <julio@avii.mx>
Versión: 1.0
Descripción: Consume API de Wordpress del sitio de fernanda familiar e importa los posts
             esta versión SI guarda en base de datos (BD) dichos posts y los despliega de la BD.

*/

date_default_timezone_set('America/Mexico_City');

//1) Consultamos los posts del API
$wp_api_rest = 'https://fernandafamiliar.soy/wp-json/wp/v2/posts';
$wp_json_posts = file_get_contents($wp_api_rest);
$wp_posts= json_decode($wp_json_posts);
 
//2) Importamos los posts consultados en la base de datos local de WP
$mysqli = new mysqli('servidor', 'usuario', 'clave', 'basedatos');

if ($mysqli->connect_error) {
	echo "Error: " . $mysqli->connect_error . "<br>";
	exit;
} else {
    if ($mysqli->query("DELETE FROM wp_posts") === TRUE) {
        $qry = "";
        foreach ($wp_posts as $post) { 
            $titulo = utf8_decode(str_replace("'", " ", $post->title->rendered));
            $extracto = utf8_decode(str_replace("'", " ", ltrim(rtrim($post->excerpt->rendered))));
            $contenido = utf8_decode(str_replace("'", " ", ltrim(rtrim($post->content->rendered))));
            $imagen = $post->jetpack_featured_media_url;

            //$qry = "INSERT INTO tbl_notas(id_nota, titulo, extracto, imagen, fecha_publicacion, fecha_modificacion) 
            //VALUES (".$post->id.",'".$titulo."', '".$extracto."', '".$imagen."', '".$post->date."', '".$post->modified."')";

            $qry = "INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status,
                    comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, 
                    post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
                    VALUES (".$post->author.",'".$post->date."', '".$post->date_gmt."', '".$contenido."', '".$titulo."', '".$extracto."', 
                    '".$post->status."', '".$post->comment_status."', '".$post->ping_status."', '', '".$titulo."', '".$imagen."', '', 
                    '".$post->modified."', '".$post->modified_gmt."', '', 0, '".$post->guid->rendered."', 0, '".$post->type."', '', 0) ";
            //echo $qry."<br>";
            
			if ($mysqli->query($qry) === TRUE) {
				//echo "se registro correctamente la nota ".$post->id.": ".utf8_encode($titulo)."  <br>";
			} else {
				echo "error al registrar nota:".$mysqli->error." /n qry:".$qry;
			}
        }
	} else {
		echo "error al borrar tabla de wp_posts";
	}
}
?>

<!DOCTYPE html>
<html lang="es" >
<head>
  <meta charset="UTF-8">
  <title>Fernanda Familiar</title>
</head>

<body>
<table border="1" width="50%" align="center" colspan="1" bordercolor="gray">
 <tr align="center">
     <td colspan="2">Notas publicadas en fernandafamiliar.soy</td>
 </tr>
 <tr>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
 </tr>

<?php
    //3) Desplegamos los posts importados de la base de datos local de wordpress
    if ($mysqli->connect_error) {
        echo "Error: " . $mysqli->connect_error . "<br>";
        exit;
    } else {
        $qry = "SELECT post_date, post_modified, to_ping, guid, post_title, post_excerpt FROM wp_posts";
        $result = $mysqli->query($qry);
        if ($result->num_rows > 0) {
            while($campo = $result->fetch_assoc()) {
                $i++;
                $fecha_publicacion = date("M d, Y h:m:s", strtotime($campo["post_date"]));
                $fecha_modificacion = date("M d, Y h:m:s", strtotime($campo["post_modified"]));
                $src_imagen = $campo["to_ping"];
                echo "<tr>
                        <td><img src='".$src_imagen."' width='200' height='200'></td>
                        <td>
                        <a href='" .  $campo["guid"] . "' target='_blank'>" .  utf8_encode($campo["post_title"]) . "</a> <br>
                        fecha de publicación: " . $fecha_publicacion . " <br> 
                        última modificación: ". $fecha_modificacion . "<br>
                         <br>
                        ".utf8_encode($campo["post_excerpt"])." <br>
                        </td>
                      </tr>";

            }
        }

    }
    $mysqli->close();
?>
</table>
</body>
</html>




    