<?php

session_start();

require_once "conexao.php";

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'login']);
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$titulo    = trim($_POST['titulo'] ?? '');
$lei       = trim($_POST['lei'] ?? '');
$url       = trim($_POST['url'] ?? '');
$tipo      = 'lei';

if ($titulo === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'dados']);
    exit;
}

// Já está favoritada?
$stmt = $conexao->prepare(
    "SELECT id FROM favoritos WHERE usuario_id = ? AND tipo = ? AND titulo = ? LIMIT 1"
);
$stmt->bind_param("iss", $usuarioId, $tipo, $titulo);
$stmt->execute();

$existe = $stmt->get_result()->fetch_assoc();

if ($existe) {

    $del = $conexao->prepare(
        "DELETE FROM favoritos WHERE id = ? AND usuario_id = ?"
    );
    $del->bind_param("ii", $existe['id'], $usuarioId);
    $del->execute();

    echo json_encode(['favoritado' => false]);

} else {

    $ins = $conexao->prepare(
        "INSERT INTO favoritos (usuario_id, tipo, titulo, especialidade, url)
         VALUES (?, ?, ?, ?, ?)"
    );
    $ins->bind_param("issss", $usuarioId, $tipo, $titulo, $lei, $url);
    $ins->execute();

    echo json_encode(['favoritado' => true]);
}