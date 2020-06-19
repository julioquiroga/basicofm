<?php
/*
Fecha: Jueves, 18 de Junio del 2020
Autor: Julio Quiroga <julio@avii.mx>
Versión: 1.0
Descripción: Consume API de Wordpress del sitio de fernanda familiar y despliega los posts directamente del API
             esta versión NO guarda en base de datos dichos posts

*/

date_default_timezone_set('America/Mexico_City');

//1) Consultamos los posts del API
$wp_api_rest = 'https://fernandafamiliar.soy/wp-json/wp/v2/posts';
$wp_json_posts = file_get_contents($wp_api_rest);
$wp_posts= json_decode($wp_json_posts);
 
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
    //2) Desplegamos los posts consultados
    foreach ($wp_posts as $post) { 
        $fecha_publicacion = date("M d, Y h:m:s", strtotime($post->date));
        $fecha_modificacion = date("M d, Y h:m:s", strtotime($post->modified));
        $src_imagen = $post->jetpack_featured_media_url;
        echo "<tr>
                <td><img src='".$src_imagen."' width='200' height='200'></td>
                <td>
                <a href='" . $post->link . "' target='_blank'>" . $post->title->rendered . "</a> <br>
                fecha de publicación: " . $fecha_publicacion . " <br> 
                última modificación: ". $fecha_modificacion . "<br>
                 <br>
                ".$post->excerpt->rendered." <br>
                </td>
              </tr>";
    }    
?>
</table>
</body>
</html>
    