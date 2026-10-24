<?php require 'rotina_config.php';
$s = $pdo->prepare('SELECT r.*, (SELECT COUNT(*) FROM rotina_itens WHERE rotina_id=r.id) AS total FROM rotinas r WHERE usuario_id=? ORDER BY id DESC');
$s->execute([$uid]); $rotinas = $s->fetchAll();
$prev = $pdo->prepare('SELECT emoji, imagem FROM rotina_itens WHERE rotina_id=? ORDER BY ordem, id LIMIT 4');
if (file_exists(__DIR__ . '/conexao.php')) { include_once __DIR__ . '/conexao.php'; }
include __DIR__ . '/header.php';
?>
<link rel="stylesheet" href="css/rotina.css">
<div class="rotina-pagina">
<div class="rv-wrap">

  <div class="rv-hero rv-com-emojis">
    <div class="rv-flutuantes" aria-hidden="true"><span>☀️</span><span>🎒</span><span>🪥</span><span>🍎</span><span>🌙</span></div>
    <h1>Rotina visual</h1>
    <p class="rv-lead">Monte o dia em cartões com imagem e texto. A criança vê o que vem agora e marca o que já fez.</p>
    <form method="post" action="rotina_acao.php" class="rv-pilula">
      <?= csrf() ?><input type="hidden" name="acao" value="nova">
      <label for="nome" class="rv-sr">Nome da nova rotina</label>
      <input id="nome" name="nome" maxlength="80" placeholder="Dê um nome: Dia de escola, Fim de semana…" required>
      <button class="rv-btn">Criar rotina</button>
    </form>
  </div>

  <div class="rv-passos">
    <div class="rv-passo"><b>1. Monte</b><span>Escolha atividades prontas ou use fotos suas.</span></div>
    <div class="rv-passo"><b>2. Use</b><span>A criança vê o que vem agora e toca em Feito.</span></div>
    <div class="rv-passo"><b>3. Imprima</b><span>Leve a rotina para a geladeira ou para a escola.</span></div>
  </div>

  <h2>Minhas rotinas</h2>
  <?php if (!$rotinas): ?>
    <p class="rv-vazio">Você ainda não tem rotinas. Dê um nome na caixa lá em cima e clique em <strong>Criar rotina</strong>.</p>
  <?php endif; ?>
  <div class="rv-rgrade">
    <?php foreach ($rotinas as $k => $r): $prev->execute([$r['id']]); $pv = $prev->fetchAll(); ?>
    <div class="rv-rcard rv-c<?= $k % 5 ?>">
      <div class="rv-pre" aria-hidden="true">
        <?php foreach ($pv as $p): echo $p['imagem'] ? '<img src="ver_imagem.php?f=' . h($p['imagem']) . '" alt="">' : '<span>' . h($p['emoji'] ?: '⭐') . '</span>'; endforeach; ?>
        <?php if (!$pv) echo '<span>🗓️</span>'; ?>
      </div>
      <div><h3><?= h($r['nome']) ?></h3><small><?= (int)$r['total'] ?> atividades</small></div>
      <div class="rv-acoes">
        <a class="rv-btn" href="rotina_ver.php?id=<?= $r['id'] ?>">Usar hoje</a>
        <a class="rv-btn rv-sec" href="rotina_editar.php?id=<?= $r['id'] ?>">Editar</a>
        <form method="post" action="rotina_acao.php" onsubmit="return confirm('Excluir esta rotina e as fotos dela?')">
          <?= csrf() ?><input type="hidden" name="acao" value="excluir_rotina">
          <input type="hidden" name="rotina_id" value="<?= $r['id'] ?>">
          <button class="rv-btn rv-sec rv-perigo">Excluir</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</div>
</body></html>