<?php require 'rotina_config.php';
$id = (int)($_GET['id'] ?? 0);
$r = minha_rotina($pdo, $uid, $id);
if (!$r) { http_response_code(404); exit('Rotina não encontrada.'); }
$s = $pdo->prepare('SELECT * FROM rotina_itens WHERE rotina_id=? ORDER BY ordem, id'); $s->execute([$id]); $itens = $s->fetchAll();
$padrao = $pdo->query('SELECT * FROM atividades_padrao ORDER BY categoria, nome')->fetchAll(); ?>
<?php
// Carrega a conexão do site (usada pelo cabeçalho para tema e preferências) e o cabeçalho
if (file_exists(__DIR__ . '/conexao.php')) { include_once __DIR__ . '/conexao.php'; }
include __DIR__ . '/header.php';
?>
<link rel="stylesheet" href="css/rotina.css">
<div class="rotina-pagina">

<div class="rv-wrap">
  <p><a href="rotina.php">← Minhas rotinas</a></p>
  <h1><?= h($r['nome']) ?></h1>

  <h2>Atividades do dia</h2>
  <?php if (!$itens): ?><p class="rv-vazio">Nenhuma atividade ainda. Adicione abaixo.</p><?php endif; ?>
  <ol class="rv-ordem">
    <?php foreach ($itens as $i => $it): ?>
    <li>
      <div class="rv-cartao rv-mini"><?= cartao($it) ?></div>
      <div class="rv-acoes">
        <?php foreach ([['subir','Subir','▲',$i>0],['descer','Descer','▼',$i<count($itens)-1],['remover','Remover','✕',true]] as [$a,$t,$ic,$on]): if(!$on) continue; ?>
        <form method="post" action="rotina_acao.php">
          <?= csrf() ?><input type="hidden" name="acao" value="<?= $a ?>"><input type="hidden" name="item_id" value="<?= $it['id'] ?>">
          <button class="rv-btn rv-sec <?= $a==='remover'?'rv-perigo':'' ?>" aria-label="<?= $t ?> <?= h($it['titulo']) ?>"><?= $ic ?></button>
        </form>
        <?php endforeach; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ol>
  <?php if ($itens): ?><p><a class="rv-btn" href="rotina_ver.php?id=<?= $id ?>">Ver rotina pronta</a></p><?php endif; ?>

  <h2>Adicionar atividade pronta</h2>
  <form method="post" action="rotina_acao.php" class="rv-linha">
    <?= csrf() ?><input type="hidden" name="acao" value="add_pronta"><input type="hidden" name="rotina_id" value="<?= $id ?>">
    <label for="pid">Atividade</label>
    <select id="pid" name="padrao_id" required>
      <?php $cat = ''; foreach ($padrao as $p): if ($p['categoria'] !== $cat): if ($cat) echo '</optgroup>'; $cat = $p['categoria']; echo '<optgroup label="' . h($cat) . '">'; endif; ?>
        <option value="<?= $p['id'] ?>"><?= h($p['emoji'] . ' ' . $p['nome']) ?></option>
      <?php endforeach; if ($cat) echo '</optgroup>'; ?>
    </select>
    <label for="h1">Horário (opcional)</label><input id="h1" type="time" name="horario">
    <button class="rv-btn">Adicionar</button>
  </form>

  <h2>Criar atividade própria (com foto)</h2>
  <form method="post" action="rotina_acao.php" enctype="multipart/form-data" class="rv-coluna">
    <?= csrf() ?><input type="hidden" name="acao" value="add_propria"><input type="hidden" name="rotina_id" value="<?= $id ?>">
    <label for="tit">Nome da atividade</label><input id="tit" name="titulo" maxlength="60" required placeholder="Ex.: Guardar a mochila">
    <label for="foto">Foto (JPG, PNG ou WebP, até 3 MB)</label>
    <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
    <img id="previa" class="rv-previa" alt="" hidden>
    <label for="emo">Ou um emoji (usado se não houver foto)</label><input id="emo" name="emoji" maxlength="4" placeholder="🎒">
    <label for="h2">Horário (opcional)</label><input id="h2" type="time" name="horario">
    <button class="rv-btn">Adicionar atividade</button>
    <small>As fotos ficam visíveis só para a sua conta.</small>
  </form>
</div>
<script>
document.getElementById('foto').addEventListener('change', e => {
  const p = document.getElementById('previa'), f = e.target.files[0];
  if (f) { p.src = URL.createObjectURL(f); p.hidden = false; } else p.hidden = true;
});
</script></div>
</body></html>