﻿﻿<?php
/*


*/


//Parametrizar según idioma
$currentLocale = get_locale();
if(!empty($currentLocale)) {
$moFile = dirname(__FILE__) . "/languages/quote-archive-" . $currentLocale . ".mo";
if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('citas', $moFile);
}

global $wpdb;
$tabla_citas = $wpdb->prefix . "citas";



/*********************************************************
**
**  Gestión de las citas
**
***********************************************************/

/*
function quote_archive_manager() {
*/

/**********************
*
* Prefijo de la tabla
*
***********************/

               global $wpdb;
               $tabla_citas = $wpdb->prefix . "citas";
               install_quote_archive();

/*****************************
*
* Literales
*
******************************/
               $grabar_tabla = __("Modificar", 'citas');
               $nueva_fila = __("Añadir", 'citas');
               $ver_tabla = __("Ver datos", 'citas');
               $borrar_tabla = __("Eliminar", 'citas');
               $limpiar = __("Limpiar", 'citas');
               $confirmar_borrado = __("Confirmar", 'citas');
               $borrar_todo = __("Vaciar", 'citas');
               $restaurar = __("Valores iniciales", 'citas');
               $inicio = '<<';
               $anterior = '<';
               $siguiente = '>';
               $fin = '>>';
               $ed_html = 'html';
               $ed_visual = 'visual';
               $autores1 = array();

/***************************
*
* Inicializar mensajes
*
*****************************/
               $mensajes = 0;
               //9 mensajes
               $mensaje = array ('', __("Borrado", 'citas'), __("Ya estás al principio de las citas", 'citas'), __("Ya estás al final de las citas", 'citas'), __("No hay citas para mostrar", 'citas'), __("Debe confirmar el borrado", 'citas'), __("Ya existe esa cita con ese autor", 'citas'), __("La cita que está intentando modificar no existe", 'citas'), __("Se han borrado todas las citas", 'citas'), __("Cargadas citas iniciales", 'citas'));

/************************
*
* Valores por defecto
*
*************************/
               if ($_POST) {
                   $errores = 0;
		   if ($_POST["visualiza"] == "")
			$_POST["visualiza"] = "si";
                   if ($_POST["autor_n"] != '' && $_POST['autor_n'] != ' ') {
                        $_POST["autor"] = $_POST["autor_n"];
                        $_POST["autor_n"] = '';
                        $_POST['autor_ID'] = 0;
                   }
                   if ($_POST['tipo_ed'] == '')
                        $_POST['tipo_ed'] = $ed_html;


/*************************************
*
*  Borrar una fila
*
**************************************/
             if ($_POST['Gestionar'] == $confirmar_borrado) {
                   quote_archive_borrar($_POST['autor'], $_POST['lacita']);
                   $mensajes = 1;
             };

/*************************************
*
*  Borrar toda la tabla
*
**************************************/
             if ($_POST['Gestionar'] == $borrar_todo) {
                   quote_archive_borrar_todo();
                   $mensajes = 8;
             };

/*************************************
*
*  Carga de valores por defecto
*
**************************************/
             if ($_POST['Gestionar'] == $restaurar) {
                   quote_archive_restaurar();
                   $mensajes = 9;
                   $consulta = "SELECT ID, autor, cita, visualiza FROM " . $tabla_citas . " ORDER BY autor ASC, cita ASC";
                   $datos_previos = $wpdb->get_results($consulta);
                   if ($datos_previos) {
                           $_POST['autor_ID'] = $datos_previos[0]->ID;
                           $_POST['autor'] = $datos_previos[0]->autor;
                           $_POST['lacita'] = $datos_previos[0]->cita;
                           $_POST['visualiza'] = $datos_previos[0]->visualiza;
                   }
                   
             };



             
/************************************
*
* Actualiza la tabla
*
*************************************/

             if ($_POST['Gestionar'] == $grabar_tabla) {
             		$mensajes = quote_archive_actualizar($_POST['autor_ID'], $_POST['autor'], $_POST['lacita'], $_POST['visualiza']);
             }
             
/************************************
*
* Inserta fila
*
*************************************/

             if ($_POST['Gestionar'] == $nueva_fila) {
             		$mensajes = quote_archive_insertar($_POST['autor'], $_POST['lacita'], $_POST['visualiza']);
                                              
             } 

/************************************ 
*
* Anterior
*
*************************************/
                   if ($_POST['Gestionar'] == $anterior) {
                       $consulta = "SELECT ID, autor, cita, visualiza FROM " . $tabla_citas . " WHERE autor < '" . $_POST['autor'] . "' OR (autor = '" . $_POST['autor'] . "' AND cita < '" . $_POST['lacita'] . "') ORDER BY autor DESC, cita DESC";

                       $datos_previos = $wpdb->get_results($consulta);
                       if ($datos_previos) {
                           $_POST['autor_ID'] = $datos_previos[0]->ID;
                           $_POST['autor'] = $datos_previos[0]->autor;
                           $_POST['lacita'] = $datos_previos[0]->cita;
                           $_POST['visualiza'] = $datos_previos[0]->visualiza;
                       }
                       else {
                           $mensajes = 2;
                       }
                   }

/************************************ 
*
* Siguiente
*
*************************************/
                   if ($_POST['Gestionar'] == $siguiente) {
                       $consulta = "SELECT ID, autor, cita, visualiza FROM " . $tabla_citas . " WHERE autor > '" . $_POST['autor'] . "' OR (autor = '" . $_POST['autor'] . "' AND cita > '" . $_POST['lacita'] . "') ORDER BY autor ASC, cita ASC";
                       $datos_previos = $wpdb->get_results($consulta);
                       if ($datos_previos) {
                           $_POST['autor_ID'] = $datos_previos[0]->ID;
                           $_POST['autor'] = $datos_previos[0]->autor;
                           $_POST['lacita'] = $datos_previos[0]->cita;
                           $_POST['visualiza'] = $datos_previos[0]->visualiza;
                       }
                       else {
                           $mensajes = 3;
                       }
                   }

/************************************ 
*
* Inicio y final
*
*************************************/
                   if ($_POST['Gestionar'] == $inicio || $_POST['Gestionar'] == $fin) {
                   if ($_POST['Gestionar'] == $inicio) {
                       $consulta = "SELECT ID, autor, cita, visualiza FROM " . $tabla_citas . " ORDER BY autor ASC, ID ASC";
                   }
                   else {
                       $consulta = "SELECT ID, autor, cita, visualiza FROM " . $tabla_citas . " ORDER BY autor DESC, ID DESC";
                   }
                       $datos_previos = $wpdb->get_results($consulta);
                       if ($datos_previos) {
                           $_POST['autor_ID'] = $datos_previos[0]->ID;
                           $_POST['autor'] = $datos_previos[0]->autor;
                           $_POST['lacita'] = $datos_previos[0]->cita;
                           $_POST['visualiza'] = $datos_previos[0]->visualiza;
                       }
                       else {
                           $mensajes = 4;
                       }
                   }

/*************************
*
* Limpiar formulario
*
**************************/
                   if ($_POST['Gestionar'] == $limpiar) {
                       $_POST['lacita'] = '';
                       $_POST['visualiza'] = 'si';
                   }

/************************************ 
*
* Visualizar datos
*
*************************************/
                   if ($_POST['Gestionar'] == $ver_tabla || $_POST['gestionar'] == $nueva_fila || $_POST['Gestionar'] == $confirmar_borrado) {
                       if ($_POST['Gestionar'] == $confirmar_borrado) {
                           $consulta = "SELECT ID, autor, cita, visualiza FROM " . $tabla_citas . " WHERE autor > '" . $_POST['autor'] . "' OR (autor = '" . $_POST['autor'] . "' AND cita > '" . $_POST['lacita'] . "') ORDER BY autor ASC, cita ASC";
                       }
                       else {
                            $consulta = "SELECT ID, autor, cita, visualiza FROM " . $tabla_citas . " WHERE autor ='" . $_POST['autor'] . "' ORDER BY autor ASC, cita ASC";
                       }
                       $datos_previos = $wpdb->get_results($consulta);
                       if ($datos_previos) {
                           $_POST['autor_ID'] = $datos_previos[0]->ID;
                           $_POST['autor'] = $datos_previos[0]->autor;
                           $_POST['lacita'] = $datos_previos[0]->cita;
                           $_POST['visualiza'] = $datos_previos[0]->visualiza;
                       }
                       else {
                           $_POST['autor_ID'] = 0;
                       	   $_POST['autor'] = '';
                           $_POST['lacita'] = '';
                           $_POST['visualiza'] = 'si';
                       }
                   }

/******************************
*
* Fijar valor de los datos
*
*******************************/
       update_option('autor', $_POST['autor']);
       update_option('lacita', $_POST['lacita']);
       update_option('visualiza', $_POST['visualiza']);
       update_option('autor_n', $_POST['autor_n']);
       update_option('autor_ID', $_POST['autor_ID']);
       update_option('tipo_ed', $_POST['tipo_ed']);
                 

             };

        $autor = get_option('autor');
        $autor_n = get_option('autor_n');
        $lacita = get_option('lacita');
        $visualiza = get_option('visualiza');
        $autor_ID = get_option('autor_ID');
        $tipo_ed = get_option('tipo_ed');

/****************************
*
* Elegir tipo de editor
*
*****************************/
                  

                   if ($_POST['tipo_ed']) {
                       $tipo_ed = $_POST['tipo_ed'];
                    }

                   if ($tipo_ed == $ed_visual) {
?>	
<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
<script type="text/javascript">
<!--
tinyMCE.init({
theme : "advanced",
theme_advanced_toolbar_location : "top",
theme_advanced_layout_manager : "SimpleLayout",
theme_advanced_buttons1 : "undo,redo,separator,bold,italic,underline,strikethrough,separator,forecolor,fontsizeselect,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,sub,sup,separator,hr,charmap,",
theme_advanced_buttons2 : "cut,copy,paste,separator,bullist,numlist,separator,outdent,indent,separator,fontselect,separator,link,unlink,anchor,separator,image,cleanup,separator,code,removeformat,",
theme_advanced_buttons3 : "",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
verify_html : "false",
verify_css_classes : "false",
plugins : "zoom,advlink,emotions,iespell,style,advhr,contextmenu,advimage,",
mode : "exact",
elements : "lacita",
valid_elements : "*[*]",
extended_valid_elements : "*[*]",
auto_reset_designmode : "true",
trim_span_elements : "false",
width : "447",
height : "300"
});
-->
</script>
<?php
}
        
?>

<div class="wrap">
<h2><?php  _e('Archivo de citas', 'citas'); ?></h2>
<form target="_self" method="post" name="form_gestion">
<table width=90%>
<tr>
<?php 
   $consulta = "SELECT autor, ID FROM " . $tabla_citas . " ORDER BY autor ASC, cita ASC";
   //echo $consulta;
   $autores_ex = $wpdb->get_results($consulta);
   foreach ($autores_ex as $existen) {
      $autores1[] = $existen->autor;
      $autores_ID[] = $existen->ID;
 
   }

if ($autores1[0] != '') {
	?>
<td width=20% valign=middle></td>
<td valign=top colspan=2>
<input name="autor" type="hidden" style="width:100%;" value="<?php echo $autor; ?>"/>
<input name="autor_ID" type="hidden" style="width:100%;" value="<?php echo $autor_ID; ?>"/>
<select name="autor">
<?php
  $i = 0;
  foreach ($autores1 as $existen) {
   ?>
         <option <?php if ($autor==$autores1[$i]) {echo 'selected'; $autor_ID = $autores_ID[$i];} ?> value="<?php echo $autores1[$i] ?>"><?php echo $autores1[$i] ?></option>
<?php
   $i++;
  }
?>
</select>
</td>
<td valign=top><?php _e('Nuevo autor', 'citas');	?> 
	<?php
	}
	else {
	?>
	<td><?php _e('Nuevo autor', 'citas');	?></td>
	<td></td>
<td valign=top>
	<?php
	};
	?>
	<input name="autor_n" type="hidden" style="width:100%;" value="<?php echo $autor_n; ?>" />
	<input name="autor_n" type="text" size=30 value="<?php echo $autor_n; ?>" />
</td>
<td>
</td>
</tr>

<tr><td colspan=5>&nbsp;</td></tr>

<tr>
<td width=20% valign=top></td>
<td valign=top colspan=4>
<div align=left>
<STYLE type="text/css">
   input.inactivo {border: none; background: #fff; color: #505050}
   input.activo {border: none; background: #505050; color: #FFF;}
</STYLE>
<input name="tipo_ed" type="hidden" style="width:100%;" value="<?php echo $tipo_ed; ?>" />
<input type="submit" class="<?php if ($tipo_ed == $ed_visual) echo 'inactivo'; else echo 'activo'; ?>" name="tipo_ed" value="<?php echo $ed_html; ?>" />
<input type="submit" class="<?php if ($tipo_ed == $ed_html) echo 'inactivo'; else echo 'activo'; ?>"name="tipo_ed" value="<?php echo $ed_visual; ?>" />
<input name="visualiza" type="hidden" style="width:100%;" value="<?php echo $visualiza; ?>" />
&nbsp;|&nbsp;
<?php _e('Ver la cita', 'citas'); ?>&nbsp;
<input type="radio" <?php if ($visualiza == 'si') echo ' checked'; ?> name="visualiza" value="si"><?php _e('Sí', 'citas'); ?>&nbsp;&nbsp;&nbsp;
<input type="radio" <?php if ($visualiza == 'no') echo ' checked'; ?> name="visualiza" value="no"><?php _e('No', 'citas'); ?>
</div>
<input name="lacita" type="hidden" style="width:100%;" value="<?php echo $lacita; ?>" />
<style>
#lacita {'trebuchet ms', arial, sans-serif;;font-size: 12px;font-weight: normal;letter-spacing: 2px;width:400px;height:150px;border: 2px outset #999999;}
</style>
<textarea id ="lacita" name="lacita" cols="58" rows="10"><?php echo $lacita; ?></textarea>

<tr>
<td></td>
<td valign=top colspan=3><strong><p class="submit">
    <input name="submitted" type="hidden" value="yes" />
    <input type="submit" name="Gestionar" value="<?php echo $inicio; ?>" />
    <input type="submit" name="Gestionar" value="<?php echo $anterior; ?>" />
    <input type="submit" name="Gestionar" value="<?php echo $siguiente; ?>" />
    <input type="submit" name="Gestionar" value="<?php echo $fin; ?>" />
&nbsp;|&nbsp;
    <input type="submit" name="Gestionar" value="<?php echo $grabar_tabla; ?>" />
    <input type="submit" name="Gestionar" value="<?php echo $nueva_fila; ?>" />
<?php
             //Borrado / Confirmación
             if ($_POST['Gestionar'] != $borrar_tabla || $errores > 0) {
                echo '<input type="submit" name="Gestionar" value="' . $borrar_tabla . '" />';
             }
             else {
                echo '<input type="submit" name="Gestionar" value="' . $confirmar_borrado . '" />';
             }; 
?>
<br />
    <input type="submit" name="Gestionar" value="<?php echo $borrar_todo; ?>" />
    <input type="submit" name="Gestionar" value="<?php echo $restaurar; ?>" />
&nbsp;|&nbsp;
    <input type="submit" name="Gestionar" value="<?php echo $ver_tabla; ?>" />
    <input type="submit" name="Gestionar" value="<?php echo $limpiar; ?>" />


</p></td>
<td></td>
</tr>

</table>
<!-- Fin de tabla de opciones -->

</form>
<?php
             
/************************************
*
* Mensaje de confirmación de borrado
*
************************************/

             if ($_POST['Gestionar'] == $borrar_tabla && $errores == 0) {
                     $mensajes = 5;
             };

/**************************************
*
* Visualizar mensajes
*
***************************************/
             if ($_POST && $mensajes > 0) {
                      echo '<div id="message" class="updated fade"><p>';
                      echo $mensaje[$mensajes];
                      echo '.</p></div>';
             }

/*
}
*/


/*
function install_quote_archive(){
   global $wpdb;
   $tabla_citas = $wpdb->prefix . "citas";
   if($wpdb->get_var("show tables like '$tabla_citas'") != $tabla_citas) {

   $sql = "CREATE TABLE " . $tabla_citas . " (
	      ID int(5) unsigned NOT NULL auto_increment,
              autor varchar(60) NOT NULL default '',
              cita longtext NOT NULL default '',
              visualiza varchar(2) NOT NULL default 'si';
	      PRIMARY KEY  (ID, autor) 		); 		";
    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
    dbDelta($sql);
    quote_archive_restaurar();
   }
}
*/


function quote_archive_borrar($autor, $lacita) {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $borrar = $wpdb->query("DELETE FROM " . $tabla_citas . "  WHERE autor = '$autor' AND cita = '$lacita'");
}

function quote_archive_borrar_todo() {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $borrar = $wpdb->query("DELETE FROM " . $tabla_citas);
}

function quote_archive_restaurar() {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $restaurar = $wpdb->query("INSERT INTO $tabla_citas (`ID`, `autor`, `cita`, `visualiza`) VALUES (11, 'Mark Twain', 'Un clásico es algo que todo el mundo quisiera haber leído y que nadie quiere leer.', 'si'), (10, 'Samuel Johnson', 'Su libro es bueno y original, pero la parte que es buena no es original y la parte que es original no es buena.', 'si'), (9, 'Tácito', 'Mientras unos toman los rumores por hechos ciertos, otros convierten los hechos en falsedades. Y tanto unos como otros son exagerados por la posteridad.', 'si'), (8, 'Sin perdón', 'Matar a un hombre es algo muy duro. No sólo le quitas todo lo que tiene. Le quitas todo lo que podría llegar a tener.', 'si'), (7, 'Walter Simonson', 'Eres un poderoso luchador, pero al final no eres más que una criatura egoísta. Mientras que los héroes.. . ¡Los héroes tienen una capacidad infinita de estupidez! ¡Y así nacen las leyendas!', 'si'), (6, 'Casablanca', '¿Quizá se fugó con los fondos de una iglesia? ¿O se lió con la mujer de un senador? Aunque me gustaría creer que mató a un hombre. En el fondo, soy un romántico.', 'si'), (4, 'Robert E. Howard', 'Los hombres civilizados no son tan corteses como los salvajes, porque, en general, saben que pueden mostrarse groseros sin que les partan el cráneo.', 'si'), (5, 'Star Crash', 'Esto habría matado a la mayoría de la gente. Pero nosotros no somos la mayoría de la gente.', 'si'), (2, 'Robert A. Heinlein', 'El progreso se debe a hombres vagos en busca de formas más fáciles de hacer las cosas.', 'si'), (3, 'Raymond Chandler', '<p>-&iquest;C&oacute;mo puedes ser tan duro y tan tierno a la vez?</p>\r\n<p>-Si no fuera duro no podr&iacute;a estar vivo. Si no fuera tierno, no merecer&iacute;a estarlo.</p>', 'si'), (1, 'Woody Allen', 'Para usted, yo soy ateo. Para Dios, soy la leal oposición', 'si'), (12, 'George Bernard Shaw', 'El camino de la ignorancia está empedrado de buenas ediciones.', 'si'), (13, 'El club de la lucha', 'Me has conocido en un momento extraño de mi vida.', 'si'), (14, 'Salvor Hardin', 'Nunca permitas que tu sentido de la moral te impida hacer lo correcto.', 'si'), (15, 'La princesa prometida', 'Me llamo Íñigo Montoya. Tú mataste a mi padre. Prepárate para morir.', 'si'), (16, 'Isaac Asimov', '¿Qué haría si solo me quedaran seis meses de vida? Escribiría más rápido.', 'si'), (17, 'Oscar Wilde', 'La diferencia entre periodismo y literatura es que el periodismo es ilegible y la literatura no es leída.', 'si'), (18, 'Obi-wan Kenobi', 'Lo que te dije era verdad... desde cierto punto de vista.', 'si'), (19, 'Apocalipse Now', 'Juzgar a alguien por asesinato en esta guerra sería como poner multas de velocidad en la carrera de Indianapolis.', 'si'), (20, 'El día de la marmota', '¿Y si no hay un mañana? ¡Hoy no lo ha habido!', 'si'), (21, 'André Gide', 'No se hace buena literatura con buenas intenciones ni con buenos sentimientos.', 'si'), (22, 'Homer Simpson', 'No me coma. Tengo mujer e hijos. Cómalos a ellos.', 'si'), (23, 'Homer Simpson', 'Los hechos no tienen sentido. ¡Puedes usar los hechos para probar cualquier cosa que sea remotamente cierta!', 'si'), (24, 'Woody Allen', 'El león y la gacela yacerán juntos... pero la gacela no dormirá muy tranquila.', 'si'), (25, 'Homer Simpson', '¡No puedo creerlo! ¡Leer y escribir realmente dan dinero!', 'si'), (26, 'Michael Howard', 'Todo valor introducido por el usuario es tonto o malicioso a menos que se demuestre lo contrario.', 'si'), (27, 'Woody Allen', 'He hecho un curso de lectura rápida y he leído Guerra y paz  en veinte minutos. Habla de Rusia.', 'si'), (28, 'Edward V. Berard', 'Caminar sobre el agua y desarrollar software a partir de unas especificaciones es fácilísimo si ambas están congeladas.', 'si'), (29, 'Samuel Johnson', 'El patriotismo es el último refugio de los cobardes.', 'si'), (30, 'La princesa prometida', 'Sí, hijo, el amor verdadero es lo mejor que existe, salvo quizá los bocadillos de cordero.', 'si'), (31, 'Andy Finkel', 'Cualquier tecnología suficientemente avanzada es indistinguible de una demo trucada.', 'si'), (32, 'Homer Simpson', 'Es como algo de aquel programa crepuscular sobre esa zona.', 'si'), (33, 'Homer Simpson', 'Marge, mentir es cosa de dos. Uno que miente y uno que escucha.', 'si'), (34, 'Homer Simpson', 'Vamos, la gente puede usar las estádisticas para probar cualquier cosa, Kent. El 14% de la gente lo sabe.', 'si'), (35, 'El padrino', 'No es nada personal, es cuestión de negocios.', 'si'), (36, 'El padrino', 'Si hay algo seguro en esta vida, si la historia nos ha enseñado algo, es que se puede matar a cualquiera.', 'si'), (37, 'Seven', 'Heminway escribió una vez: el mundo es un buen lugar por el que merece la pena luchar...  estoy de acuerdo con la segunda parte.', 'si'), (38, 'Groucho Marx', 'Pero debe haber una guerra. He pagado un mes de alquiler por el campo de batalla.', 'si'), (39, 'Groucho Marx', 'Estamos luchando por el honor de esta mujer, que es mucho más de lo que ella hubiera hecho.', 'si'), (40, 'Groucho Marx', '¿Quiere casarse conmigo? ¿Cuánto dinero le dejó su marido? Responda primero a lo segundo.', 'si'), (41, 'John Steinbeck', 'Por el grosor del polvo en los libros de una biblioteca pública puede medirse la cultura de un pueblo.', 'si'), (42, 'Jorge Luis Borges', 'La Biblioteca es una esfera cuyo centro cabal es cualquier hexágono, cuya circunferencia es inaccesible.', 'si'), (43, 'Ezra Pound', 'Los buenos escritores son aquellos que conservan la eficiencia del lenguaje. Es decir, lo mantienen preciso, lo mantienen claro.', 'si'), (44, 'Gabriel García Márquez', 'El escritor escribe su libro para explicarse a sí mismo lo que no se puede explicar.', 'si'), (45, 'Alfonso V de Aragón', 'Los libros son, entre mis consejeros, los que más me agradan, porque ni el temor ni la esperanza les impiden decirme lo que debo hacer.', 'si'), (46, 'Groucho Marx', 'Nunca voy a ver películas donde el pecho del héroe es mayor que el de la heroína.', 'si'), (47, 'Oscar Wilde', 'Resulta de todo punto monstruosa la forma en que la gente va por ahí hoy en día criticándote a tus espaldas por cosas que son absolutamente y completamente ciertas.', 'si');") or die("Failed Query of " . $restaurar);	
}

function cita_obtener_ultimo_ID() {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $consulta = "SELECT ID FROM " . $tabla_citas . " ORDER BY ID DESC";
    $leer = $wpdb->get_results($consulta);
    if ($leer) { $ultimo_ID = $leer[0]->ID;}
    else {$ultimo_ID = 0; };
    return $ultimo_ID;
}

function cita_ver_si_existe_por_ID($autor, $ID) {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $consulta = "SELECT autor FROM " . $tabla_citas . " WHERE autor ='$autor' AND ID = '$ID'";
    //echo '<br /> Ver si existe en tabla: ' . $consulta;
    return $wpdb->get_var($consulta);
}

function cita_ver_si_existe_por_cita($autor, $cita) {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $consulta = "SELECT autor FROM " . $tabla_citas . " WHERE autor ='$autor' AND cita = '$cita'";
    //echo '<br /> Ver si existe en tabla: ' . $consulta;
    return $wpdb->get_var($consulta);
}


function cita_leer_para_visualizar($autor, $cita) {
     global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $consulta = "SELECT * FROM " . $tabla_citas . " WHERE autor ='$autor' AND cita = '$cita'";
    $devuelve = $wpdb->get_results($consulta);
    //echo '<br /> Fila actualizada: ' . $devuelve[0]->ID . ' ' . $devuelve[0]->user_login . ' ' . $devuelve[0]->fecha_nac . ' ' . $devuelve[0]->lugar_nac . ' ' . $devuelve[0]->sitio_web . ' ' . $devuelve[0]->ficha;
}



function quote_archive_actualizar($ID_actualizar, $autor_actualizar, $cita_actualizar, $visualiza_actualizar) {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $fila = cita_ver_si_existe_por_ID($autor_actualizar, $ID_actualizar);
    		if ($fila){
                        $actualizar = $wpdb->query("UPDATE " . $tabla_citas . " SET cita = '$cita_actualizar', visualiza = '$visualiza_actualizar'  WHERE autor = '$autor_actualizar' AND ID = $ID_actualizar");
                        return 0;
		}
                else {
                     return 7;
                }
                
}


function quote_archive_insertar($autor_actualizar, $cita_actualizar, $visualiza_actualizar) {
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $fila = cita_ver_si_existe_por_cita($autor_actualizar, $cita_actualizar);
    		if (!$fila){
                       //Obtener siguiente id
                       $ultimo_ID = cita_obtener_ultimo_ID();
                       //echo '<br />Ultimo ID en la tabla ' . $ultimo_ID;
                       $ID_actualizar = $ultimo_ID + 1;
			$insertar = $wpdb->query("INSERT INTO " . $tabla_citas . "  VALUES ($ID_actualizar, '$autor_actualizar', '$cita_actualizar', '$visualiza_actualizar')") or die("Failed Query of " . $insertar);
                        //echo '<br /> Insertar: ' . $insertar;
                        return 0;
		}	
                else {
                     return 6;
		}
                
}




?>