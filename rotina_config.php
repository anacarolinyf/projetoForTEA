<?php
session_start();
// AJUSTE 1: usuário/senha do seu banco (XAMPP padrão: root, sem senha)
$pdo = new PDO('mysql:host=localhost;dbname=fortea;charset=utf8mb4', 'root', '', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
// AJUSTE 2: nome da variável de sessão que guarda o id do usuário logado
$uid = $_SESSION['usuario_id'] ?? null;
// AJUSTE 3: página de login do seu site
if (!$uid) { header('Location: login.php'); exit; }

$_SESSION['csrf'] = $_SESSION['csrf'] ?? bin2hex(random_bytes(16));
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf() { return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">'; }
function minha_rotina($pdo, $uid, $id) {
  $s = $pdo->prepare('SELECT * FROM rotinas WHERE id=? AND usuario_id=?');
  $s->execute([$id, $uid]);
  return $s->fetch();
}
function cartao($it) {
  $m = $it['imagem'] ? '<img src="ver_imagem.php?f=' . h($it['imagem']) . '" alt="">'
                     : '<span class="emo">' . h($it['emoji'] ?: '⭐') . '</span>';
  $hora = $it['horario'] ? '<small>' . h(substr($it['horario'], 0, 5)) . '</small>' : '';
  return $m . '<strong>' . h($it['titulo']) . '</strong>' . $hora;
}
