<?php
header('Content-Type: application/json');

// Ruta de la carpeta donde guardar las fotos
$uploadDir = __DIR__ . '/../assets/'; // Ajusta la ruta según tu estructura

if (isset($_FILES['foto'])) {
    $foto = $_FILES['foto'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($foto['type'], $allowedTypes)) {
        echo json_encode(['error' => 'Formato de archivo no permitido.']);
        exit;
    }

    $fileName = uniqid() . '-' . basename($foto['name']);
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($foto['tmp_name'], $uploadFile)) {
        echo json_encode(['url' => 'assets/' . $fileName, 'message' => 'Foto subida correctamente.']);
    } else {
        echo json_encode(['error' => 'Error al guardar el archivo.']);
    }
} else {
    echo json_encode(['error' => 'No se recibió ninguna foto.']);
}
?>
