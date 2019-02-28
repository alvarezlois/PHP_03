<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "
http://www.w3.org/TR/html4/loose.dtd">
<!-- Desarrollo Web en Entorno Servidor -->
<!-- Tarea 3: Trabajo con bases de datos MySQL y motor PDO -->
<!-- 
    Mostrara un cuadro desplegable que permita seleccionar un registro de la tabla 
    'familias', junto a un botón "Mostrar". Al pulsar el botón, se mostrará un 
    listado de los productos de la familia seleccionada.
    Para cada producto se mostrará su nombre corto y su PVP, junto a un botón con 
    el texto "Editar". Cuando se pulse ese botón, se enviará el formulario a la 
    página "editar.php".
 -->
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Tarea 3 - DWES - Jos&eacute; Luis Comesa&ntilde;a Cabeza</title>
        <!-- usamos la hoja de estilos que se indica en la tarea -->
        <link href="dwes.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
			// Si se recibe 'cod' se añade en la variable $codigo
            if (isset($_POST['cod'])) $codigo = $_POST['cod'];
			// Se abre la base de datos y se capturan los posibles errores
            try {
                $dwes = new PDO("mysql:host=localhost;dbname=dwes", "dwes", "abc123.");
                $dwes->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch (PDOException $e) {
                $error = $e->getCode();
                $mensaje = $e->getMessage();
            }
		?>
		<div id="encabezado">
			<h1>Tarea 3: Listado de productos de una familia</h1>
            <!-- Abrimos un formulario que envíe los datos via post a la propia página -->
			<form id="form_listado" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
				<span>Familia: </span>
                <!-- Comenzamos a configurar el combo que contendrá los datos de las familias -->
				<select name="cod">
					<?php
						// Si no han ocurrido errores en la apertura de la base de datos
						if (!isset($error)) {
							// Seleccionamos cod y nombre de la tabla familia
							$sql = "SELECT cod, nombre FROM familia";
							// Ejecutamos la consulta anterior sobre la BD abierta
							$resultado = $dwes->query($sql);
							// Si se encuentran datos
							if($resultado) {
								// Se lee el primer registro encontrado
								$row = $resultado->fetch();
								// Hacer mientras el registro leido sea distinto de null (mientras existan registros)
								while ($row != null) {
									// Se usa la orden de html 'option' con el valor del dato de familia leido
									echo "<option value='${row['cod']}'";
									// Si se recibe un código de producto lo seleccionamos
									// en el desplegable usando selected='true'
									if (isset($codigo) && $codigo == $row['cod'])
										echo " selected='true'";
									// Se cierra el option poniendo el dato del nombre de la familia
									// Utilizo htmlentities para poner los datos sin caracteres 'raros'
									echo ">".htmlentities($row['nombre'])."</option>";
									// Se lee un nuevo registro (si no se encuentra ninguno más dará null)
									$row = $resultado->fetch();
								}
							}
						}
					?>
				</select>
                <!-- Mostramos el botón para autoenviarnos la selección realizada -->
				<input type="submit" value="Mostrar" name="enviar"/>
			</form>
		</div>
		<div id="contenido">
			<h2>Productos de la familia:</h2>
			<?php
				// Si se recibió un código de familia y no se produjo ningún error
				//  mostramos los productos de esa familia
				if (!isset($error) && isset($codigo)) {
					/* 	
						Seleccionamos todos los campos de la tabla productos unida a la
					 	de familia para aquellos que coincidan el campo en común del código de familias
					 	De todos los registros nos quedamos únicamente con aquellos en que el código de 
					 	la familia coincida con el código que nos envía el formulario anterior
					*/
					$sql = <<<SQL
						SELECT producto.*
						FROM producto INNER JOIN familia ON producto.familia=familia.cod
						WHERE familia.cod='$codigo'
SQL;
					// ejecutamos la consulta anterior
					$resultado = $dwes->query($sql);
					// si existen datos...
					if($resultado) {
						// se lee el primer registro encontrado
						$row = $resultado->fetch();
						// hacer mientras existan registros encontrados
						while ($row != null) {
							// Creamos un formulario por cada registro encontrado
							// con los valores obtenidos y los enviamos via post al fichero editar.php
							echo '<form id="form" action="editar.php" method="post">';
							// cargamos los valores del registro en sus correspondientes variables
						  	$codPro=$row['cod'];
							$nombre=$row['nombre'];
							$nombre_corto=$row['nombre_corto'];
							$descripcion=$row['descripcion'];
							$pvp=$row['PVP'];
							// Pasamos oculto el código del producto y mostramos el
							// nombre corto y su precio.
							echo "<input type='hidden' name='cod' value='$codPro'/>";
							echo "<p>Producto <b>$nombre_corto</b> PVP: <b>$pvp &euro;</b>";
							// ponemos el botón de 'editar' para cada registro encontrado
							echo "<input type='submit' value='Editar' name='edit'/></p>";
							echo "</form>";
							// se lee un nuevo registro (si no existe tendrá el valor null)
							$row = $resultado->fetch();
						}
					}
				}
			?>
		</div>
	</body>
</html>