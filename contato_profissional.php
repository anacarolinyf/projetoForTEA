<?php
// Registra que o usuário clicou para conversar com o profissional
// e depois redireciona para o WhatsApp.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conexao.php';

$profissionalId = (int) ($_GET['id'] ?? 0);

if ($profissionalId <= 0) {
    header('Location: Profissionais.php');
    exit;
}

// Busca o WhatsApp no banco (não confia em dados vindos da URL)
$stmt = $conexao->prepare("SELECT whatsapp FROM profissionais WHERE id = ? AND ativo = 1");
$stmt->bind_param('i', $profissionalId);
$stmt->execute();
$profissional = $stmt->get_result()->fetch_assoc();
$stmt->close();

$whatsapp = preg_replace('/[^0-9]/', '', $profissional['whatsapp'] ?? '');

if ($whatsapp === '') {
    header('Location: Profissionais.php');
    exit;
}

// Só registra se o usuário estiver logado
if (isset($_SESSION['usuario_id'])) {
    $usuarioId = (int) $_SESSION['usuario_id'];

    $stmt = $conexao->prepare("
        INSERT INTO contatos_profissionais (usuario_id, profissional_id)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE ultimo_contato = NOW()
    ");
    $stmt->bind_param('ii', $usuarioId, $profissionalId);
    $stmt->execute();
    $stmt->close();
}

header('Location: https://wa.me/' . $whatsapp);
exit;