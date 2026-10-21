<?php

session_start();

require_once "conexao.php";

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'login']);
    exit;
}

$usuarioId      = (int) $_SESSION['usuario_id'];
$profissionalId = (int) ($_POST['id'] ?? 0);
$url            = trim($_POST['url'] ?? '');
$tipo           = 'profissional';

if ($profissionalId <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'dados']);
    exit;
}

// Nome e especialidade vêm do banco (não do navegador)
$stmt = $conexao->prepare(
    "SELECT nome, especialidade FROM profissionais WHERE id = ? LIMIT 1"
);
$stmt->bind_param("i", $profissionalId);
$stmt->execute();

$profissional = $stmt->get_result()->fetch_assoc();

if (!$profissional) {
    http_response_code(404);
    echo json_encode(['erro' => 'nao_encontrado']);
    exit;
}

$titulo        = $profissional['nome'];
$especialidade = $profissional['especialidade'] ?? '';

// Só aceita link relativo no formato pagina.php#prof-ID
if (!preg_match('/^[A-Za-z0-9_\-\.]+\.php#prof-\d+$/', $url)) {
    $url = 'Profissionais.php#prof-' . $profissionalId;
}

// Já está favoritado?
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
    $ins->bind_param("issss", $usuarioId, $tipo, $titulo, $especialidade, $url);
    $ins->execute();

    echo json_encode(['favoritado' => true]);
}