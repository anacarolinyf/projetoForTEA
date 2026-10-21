<?php
/*
 * Aplica as preferências salvas do usuário em TODAS as páginas:
 * modo escuro, tamanho da fonte e redução de animações.
 *
 * Como usar: no includes/header.php, antes do </head>, coloque:
 *     <?php include __DIR__ . '/preferencias.php'; ?>
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$__prefs = [
    'modo_escuro'       => 0,
    'reduzir_animacoes' => 0,
    'tamanho_fonte'     => 'normal',
];

if (!empty($_SESSION['usuario_id'])) {

    if (!isset($conexao)) {
        require_once __DIR__ . '/../conexao.php';
    }

    if (isset($conexao)) {

        $__id = (int) $_SESSION['usuario_id'];

        $__stmt = $conexao->prepare(
            "SELECT modo_escuro, reduzir_animacoes, tamanho_fonte
             FROM preferencias_usuario
             WHERE usuario_id = ?
             LIMIT 1"
        );

        if ($__stmt) {

            $__stmt->bind_param("i", $__id);
            $__stmt->execute();

            $__res = $__stmt->get_result();

            if ($__res && $__res->num_rows > 0) {
                $__prefs = array_merge($__prefs, $__res->fetch_assoc());
            }

            $__stmt->close();

        }

    }

}

$__tamanhos = [
    'pequena'      => '14px',
    'normal'       => '16px',
    'grande'       => '18px',
    'muito_grande' => '20px',
];

$__fonte  = $__tamanhos[$__prefs['tamanho_fonte']] ?? '16px';
$__escuro = (int) $__prefs['modo_escuro'] === 1;
$__semAni = (int) $__prefs['reduzir_animacoes'] === 1;
?>
<style id="fortea-preferencias">

<?php if ($__fonte !== '16px'): ?>
html { font-size: <?= $__fonte ?>; }
<?php endif; ?>

/* ---------- Modo escuro (vale para o site todo) ---------- */

html.modo-escuro {
    background: #ffffff;
    filter: invert(1) hue-rotate(180deg);
}

/* Imagens, vídeos e mapas voltam às cores originais */
html.modo-escuro img,
html.modo-escuro video,
html.modo-escuro iframe,
html.modo-escuro picture,
html.modo-escuro canvas {
    filter: invert(1) hue-rotate(180deg);
}

/* Cabeçalho e rodapé mantêm as cores da marca */
html.modo-escuro header,
html.modo-escuro footer {
    filter: invert(1) hue-rotate(180deg);
}

html.modo-escuro header img,
html.modo-escuro header video,
html.modo-escuro footer img,
html.modo-escuro footer video {
    filter: none;
}

/* ---------- Reduzir animações ---------- */

html.sem-animacoes *,
html.sem-animacoes *::before,
html.sem-animacoes *::after {
    transition: none !important;
    animation: none !important;
    scroll-behavior: auto !important;
}

</style>

<script>
(function () {
    var el = document.documentElement;
    <?php if ($__escuro): ?>el.classList.add('modo-escuro');<?php endif; ?>

    <?php if ($__semAni): ?>el.classList.add('sem-animacoes');<?php endif; ?>

})();
</script>