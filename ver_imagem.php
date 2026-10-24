<?php
// Entrega a foto só para o dono da rotina (a pasta uploads/ é bloqueada pelo .htaccess)
require 'rotina_config.php';
$f = $_GET['f'] ?? '';
if (!preg_match('/^[a-f0-9]{32}\.jpg$/', $f)) { http_response_code(404); exit; }
$s = $pdo->prepare('SELECT 1 FROM rotina_itens i JOIN rotinas r ON r.id=i.rotina_id WHERE i.imagem=? AND r.usuario_id=?');
$s->execute([$f, $uid]);
$caminho = __DIR__ . '/uploads/rotinas/' . $f;
if (!$s->fetch() || !is_file($caminho)) { http_response_code(404); exit; }
header('Content-Type: image/jpeg');
header('Cache-Control: private, max-age=86400');
readfile($caminho);
