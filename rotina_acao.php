<?php
require 'rotina_config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
  http_response_code(400); exit('Pedido inválido.');
}
$acao = $_POST['acao'] ?? '';
$rid  = (int)($_POST['rotina_id'] ?? 0);
$volta = ($_POST['volta'] ?? '') === 'ver' ? 'rotina_ver.php' : 'rotina_editar.php';
const UPLOAD_DIR = __DIR__ . '/uploads/rotinas/';
const MAX_FOTOS = 30;

function apagar_arquivos($arquivos) {
  foreach ($arquivos as $f) if (preg_match('/^[a-f0-9]{32}\.jpg$/', (string)$f)) @unlink(UPLOAD_DIR . $f);
}
// valida, redimensiona (lado maior 600px) e regrava como JPG (isso remove EXIF/localização)
function salvar_foto($arq) {
  if ($arq['error'] !== UPLOAD_ERR_OK || $arq['size'] > 3 * 1024 * 1024) return null;
  $info = @getimagesize($arq['tmp_name']);
  if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) return null;
  $src = @imagecreatefromstring(file_get_contents($arq['tmp_name']));
  if (!$src) return null;
  [$w, $hgt] = [imagesx($src), imagesy($src)];
  $esc = min(1, 600 / max($w, $hgt));
  $nw = max(1, (int)($w * $esc)); $nh = max(1, (int)($hgt * $esc));
  $dst = imagecreatetruecolor($nw, $nh);
  imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $hgt);
  $nome = bin2hex(random_bytes(16)) . '.jpg';
  imagejpeg($dst, UPLOAD_DIR . $nome, 85);
  return $nome;
}
function dono_do_item($pdo, $uid, $iid) {
  $s = $pdo->prepare('SELECT i.* FROM rotina_itens i JOIN rotinas r ON r.id=i.rotina_id WHERE i.id=? AND r.usuario_id=?');
  $s->execute([$iid, $uid]);
  return $s->fetch();
}

if ($acao === 'nova') {
  $nome = trim(mb_substr($_POST['nome'] ?? '', 0, 80));
  if ($nome !== '') {
    $pdo->prepare('INSERT INTO rotinas (usuario_id, nome) VALUES (?, ?)')->execute([$uid, $nome]);
    header('Location: rotina_editar.php?id=' . $pdo->lastInsertId()); exit;
  }
  header('Location: rotina.php'); exit;
}

if ($acao === 'excluir_rotina' && minha_rotina($pdo, $uid, $rid)) {
  $s = $pdo->prepare('SELECT imagem FROM rotina_itens WHERE rotina_id=? AND imagem IS NOT NULL');
  $s->execute([$rid]);
  apagar_arquivos($s->fetchAll(PDO::FETCH_COLUMN));
  $pdo->prepare('DELETE FROM rotinas WHERE id=?')->execute([$rid]);
  header('Location: rotina.php'); exit;
}

if (in_array($acao, ['add_pronta', 'add_propria'], true) && minha_rotina($pdo, $uid, $rid)) {
  $horario = preg_match('/^\d{2}:\d{2}$/', $_POST['horario'] ?? '') ? $_POST['horario'] : null;
  $ordem = (int)$pdo->query('SELECT COALESCE(MAX(ordem),0)+1 FROM rotina_itens WHERE rotina_id=' . $rid)->fetchColumn();
  if ($acao === 'add_pronta') {
    $s = $pdo->prepare('SELECT nome, emoji FROM atividades_padrao WHERE id=?');
    $s->execute([(int)($_POST['padrao_id'] ?? 0)]);
    if ($p = $s->fetch())
      $pdo->prepare('INSERT INTO rotina_itens (rotina_id,titulo,emoji,horario,ordem) VALUES (?,?,?,?,?)')
          ->execute([$rid, $p['nome'], $p['emoji'], $horario, $ordem]);
  } else {
    $titulo = trim(mb_substr($_POST['titulo'] ?? '', 0, 60));
    $emoji  = mb_substr(trim($_POST['emoji'] ?? ''), 0, 4);
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
      $q = $pdo->prepare('SELECT COUNT(*) FROM rotina_itens i JOIN rotinas r ON r.id=i.rotina_id WHERE r.usuario_id=? AND i.imagem IS NOT NULL');
      $q->execute([$uid]);
      if ($q->fetchColumn() < MAX_FOTOS) $foto = salvar_foto($_FILES['foto']);
    }
    if ($titulo !== '')
      $pdo->prepare('INSERT INTO rotina_itens (rotina_id,titulo,emoji,imagem,horario,ordem) VALUES (?,?,?,?,?,?)')
          ->execute([$rid, $titulo, $emoji ?: null, $foto, $horario, $ordem]);
  }
  header('Location: rotina_editar.php?id=' . $rid); exit;
}

if (in_array($acao, ['remover', 'subir', 'descer'], true) && ($it = dono_do_item($pdo, $uid, (int)($_POST['item_id'] ?? 0)))) {
  $rid = (int)$it['rotina_id'];
  if ($acao === 'remover') {
    apagar_arquivos([$it['imagem']]);
    $pdo->prepare('DELETE FROM rotina_itens WHERE id=?')->execute([$it['id']]);
  } else {
    $op = $acao === 'subir' ? ['<', 'DESC'] : ['>', 'ASC'];
    $s = $pdo->prepare("SELECT id, ordem FROM rotina_itens WHERE rotina_id=? AND (ordem {$op[0]} ? OR (ordem = ? AND id {$op[0]} ?)) ORDER BY ordem {$op[1]}, id {$op[1]} LIMIT 1");
    $s->execute([$rid, $it['ordem'], $it['ordem'], $it['id']]);
    if ($viz = $s->fetch()) {
      $t = $pdo->prepare('UPDATE rotina_itens SET ordem=? WHERE id=?');
      // se as ordens forem iguais, usa o id para desempatar
      $a = $it['ordem']; $b = $viz['ordem'];
      if ($a == $b) { $b = $acao === 'subir' ? $a - 1 : $a + 1; }
      $t->execute([$b, $it['id']]); $t->execute([$a, $viz['id']]);
    }
  }
  header('Location: rotina_editar.php?id=' . $rid); exit;
}

if ($acao === 'feito' && ($it = dono_do_item($pdo, $uid, (int)($_POST['item_id'] ?? 0)))) {
  $rid = (int)$it['rotina_id'];
  $del = $pdo->prepare('DELETE FROM rotina_progresso WHERE item_id=? AND dia=CURDATE()');
  $del->execute([$it['id']]);
  if (!$del->rowCount()) $pdo->prepare('INSERT INTO rotina_progresso (item_id, dia) VALUES (?, CURDATE())')->execute([$it['id']]);
  header('Location: rotina_ver.php?id=' . $rid . (($_POST['modo'] ?? '') === 'agora' ? '&modo=agora' : '')); exit;
}
header('Location: rotina.php');
