<?php
$target_dir = "imgusers/";
$max_file_size = 200 * 1024; // 200 KB in bytes
$max_total_size = 300 * 1024; // 300 KB in bytes
$allowed_types = array('image/jpeg', 'image/png');

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true); // Crear el directorio y darle los permisos adecuados.
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_size = 0;
    foreach ($_FILES["files"]["tmp_name"] as $key => $tmp_name) {
        $file_name = $_FILES["files"]["name"][$key];
        $file_size = $_FILES["files"]["size"][$key];
        $file_tmp = $_FILES["files"]["tmp_name"][$key];
        $file_type = $_FILES["files"]["type"][$key];

        // Comprobación del tamaño de cada archivo y el tipo de archivo
        if ($file_size > $max_file_size) {
            die("El archivo $file_name es demasiado grande. El tamaño máximo permitido es 200 KB.");
        }

        if (!in_array($file_type, $allowed_types)) {
            die("El archivo $file_name no es del tipo JPG o PNG.");
        }

        $total_size += $file_size;

        // Comprobar si el archivo ya existe
        if (file_exists($target_dir . $file_name)) {
            die("El archivo $file_name ya existe.");
        }
    }

    // Comprobación del tamaño total de los archivos
    if ($total_size > $max_total_size) {
        die("El tamaño total de los archivos excede el límite permitido de 300 KB.");
    }

    // Mover los archivos al directorio de destino
    foreach ($_FILES["files"]["tmp_name"] as $key => $tmp_name) {
        $file_name = $_FILES["files"]["name"][$key];
        move_uploaded_file($tmp_name, $target_dir . $file_name);
    }

    echo "Archivos subidos con éxito.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subir archivos</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" multiple>
        <input type="submit" value="Subir archivos" name="submit">
    </form>
</body>
</html>