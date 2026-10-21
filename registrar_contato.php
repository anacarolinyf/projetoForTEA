<?php

session_start();

require_once "conexao.php";

// Visitante sem login: não registra nada (a ligação continua funcionando)
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(204);
    exit;
}

$usuarioId     = (int) $_SESSION['usuario_id'];
$profissionalId = (int) ($_POST['id'] ?? 0);

if ($profissionalId <= 0) {
    http_response_code(400);
    exit;
}

// O profissional existe?
$stmt = $conexao->prepare("SELECT id FROM profissionais WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $profissionalId);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    http_response_code(404);
    exit;
}

// Já existe contato anterior?
$stmt = $conexao->prepare(
    "SELECT 1 FROM contatos_profissionais
     WHERE usuario_id = ? AND profissional_id = ? LIMIT 1"
);
$stmt->bind_param("ii", $usuarioId, $profissionalId);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    $up = $conexao->prepare(
        "UPDATE contatos_profissionais
         SET ultimo_contato = NOW()
         WHERE usuario_id = ? AND profissional_id = ?"
    );
    $up->bind_param("ii", $usuarioId, $profissionalId);
    $up->execute();

} else {

    $ins = $conexao->prepare(
        "INSERT INTO contatos_profissionais (usuario_id, profissional_id, ultimo_contato)
         VALUES (?, ?, NOW())"
    );
    $ins->bind_param("ii", $usuarioId, $profissionalId);
    $ins->execute();
}

http_response_code(204);