<?php require 'rotina_config.php';
$id = (int)($_GET['id'] ?? 0);
$r = minha_rotina($pdo, $uid, $id);
if (!$r) { http_response_code(404); exit('Rotina não encontrada.'); }
$s = $pdo->prepare('SELECT i.*, (p.item_id IS NOT NULL) AS feito FROM rotina_itens i LEFT JOIN rotina_progresso p ON p.item_id=i.id AND p.dia=CURDATE() WHERE i.rotina_id=? ORDER BY i.ordem, i.id');
$s->execute([$id]); $itens = $s->fetchAll();
$total = count($itens); $feitos = count(array_filter($itens, fn($x) => $x['feito']));
$completo = $total > 0 && $feitos === $total;
$agora = isset($_GET['modo']) && $_GET['modo'] === 'agora';
$mostrar = $itens;
if ($agora) { $pend = array_values(array_filter($itens, fn($x) => !$x['feito'])); $mostrar = array_slice($pend, 0, 2); }

function botao($it, $id, $agora) { ?>
  <form method="post" action="rotina_acao.php" class="rv-semprint">
    <?= csrf() ?><input type="hidden" name="acao" value="feito"><input type="hidden" name="item_id" value="<?= $it['id'] ?>">
    <input type="hidden" name="volta" value="ver"><?= $agora ? '<input type="hidden" name="modo" value="agora">' : '' ?>
    <button class="rv-btn rv-feito-btn"><?= $it['feito'] ? 'Desfazer' : '✔ Feito' ?></button>
  </form>
<?php }
if (file_exists(__DIR__ . '/conexao.php')) { include_once __DIR__ . '/conexao.php'; }
include __DIR__ . '/header.php';
?>
<link rel="stylesheet" href="css/rotina.css">
<div class="rotina-pagina">
<div class="rv-wrap">
  <p class="rv-semprint"><a href="rotina.php">← Minhas rotinas</a></p>

  <div class="rv-hero">
    <h1><?= h($r['nome']) ?></h1>
    <div class="rv-barra" role="progressbar" aria-valuemin="0" aria-valuemax="<?= $total ?>" aria-valuenow="<?= $feitos ?>">
      <span style="width:<?= $total ? round($feitos / $total * 100) : 0 ?>%"></span>
    </div>
    <p class="rv-prog"><?= $feitos ?> de <?= $total ?> feitas hoje</p>
  </div>

  <?php if ($completo): ?>
  <div class="rv-parabens" role="status">
    <div class="rv-estrelas" aria-hidden="true">
      <?php for ($i = 0; $i < 10; $i++) echo '<span style="left:' . ($i * 10 + 3) . '%;animation-delay:' . ($i * 0.25) . 's">⭐</span>'; ?>
    </div>
    🎉 <strong>Dia concluído!</strong> Todas as atividades foram feitas.
  </div>
  <?php endif; ?>

  <div class="rv-acoes rv-semprint">
    <a class="rv-btn <?= $agora ? 'rv-sec' : '' ?>" href="rotina_ver.php?id=<?= $id ?>">Dia inteiro</a>
    <a class="rv-btn <?= $agora ? '' : 'rv-sec' ?>" href="rotina_ver.php?id=<?= $id ?>&modo=agora">Agora / Depois</a>
    <button class="rv-btn rv-sec" onclick="window.print()">Imprimir</button>
    <a class="rv-btn rv-sec" href="rotina_editar.php?id=<?= $id ?>">Editar</a>
  </div>

  <?php if (!$total): ?>
    <p class="rv-vazio">Esta rotina ainda não tem atividades. <a href="rotina_editar.php?id=<?= $id ?>">Adicionar</a></p>
  <?php endif; ?>

  <div class="rv-grade <?= $agora ? 'rv-duas' : '' ?>">
    <?php foreach ($mostrar as $k => $it): ?>
    <div class="rv-cartao <?= $it['feito'] ? 'rv-ok' : '' ?>">
      <?php if ($agora): ?><span class="rv-selo"><?= $k === 0 ? 'Agora' : 'Depois' ?></span><?php endif; ?>
      <?= cartao($it) ?>
      <?php botao($it, $id, $agora); ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<script>
// Ao marcar "Feito", o cartão dá uma pequena "pulada" antes de salvar
document.querySelectorAll('form.rv-semprint').forEach(f => f.addEventListener('submit', e => {
  const c = f.closest('.rv-cartao');
  const calmo = document.body.classList.contains('reduzir-animacoes') || matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!c || c.classList.contains('rv-ok') || calmo) return;
  e.preventDefault(); c.classList.add('rv-pop'); setTimeout(() => f.submit(), 380);
}));
</script>
</div>
</body></html>