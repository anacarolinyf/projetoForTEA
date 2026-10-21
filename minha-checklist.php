
<?php
session_start();

require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuarioId = (int) $_SESSION["usuario_id"];



$etapas = [
    "buscando_informacoes" => [
        "titulo" => "Buscando informações",
        "descricao" => "Descubra por onde começar e encontre informações confiáveis.",
        "icone" => "📚",
        "tarefas" => [
            "Conhecer o que é o Transtorno do Espectro Autista",
            "Buscar informações em fontes confiáveis",
            "Conhecer os direitos das pessoas autistas",
            "Anotar as principais dúvidas",
            "Identificar serviços de apoio disponíveis"
        ]
    ],
    "investigando_sinais" => [
        "titulo" => "Investigando sinais e avaliação",
        "descricao" => "Organize suas observações e descubra onde buscar orientação.",
        "icone" => "🔎",
        "tarefas" => [
            "Registrar situações e comportamentos observados",
            "Anotar dúvidas sobre o desenvolvimento",
            "Conversar com profissionais de saúde ou educação",
            "Pesquisar como funciona a avaliação diagnóstica",
            "Organizar informações importantes para a consulta"
        ]
    ],
    "diagnostico_recente" => [
        "titulo" => "Diagnóstico recente",
        "descricao" => "Organize os próximos passos no seu próprio ritmo.",
        "icone" => "💙",
        "tarefas" => [
            "Organizar documentos e relatórios disponíveis",
            "Conversar com os profissionais responsáveis pelo acompanhamento",
            "Conhecer os direitos garantidos por lei",
            "Pesquisar serviços de apoio na minha região",
            "Anotar dúvidas para as próximas consultas"
        ]
    ],
    "acompanhamento" => [
        "titulo" => "Acompanhamento e cuidados",
        "descricao" => "Organize a rotina e os apoios necessários.",
        "icone" => "🌱",
        "tarefas" => [
            "Organizar os compromissos de acompanhamento",
            "Registrar necessidades e avanços observados",
            "Conversar com a equipe de acompanhamento",
            "Identificar adaptações que ajudam na rotina",
            "Reavaliar as necessidades de apoio periodicamente"
        ]
    ],
    "autonomia" => [
        "titulo" => "Autonomia e qualidade de vida",
        "descricao" => "Valorize as preferências, o bem-estar e a participação.",
        "icone" => "🌻",
        "tarefas" => [
            "Identificar interesses e preferências pessoais",
            "Estabelecer objetivos adequados às necessidades individuais",
            "Buscar estratégias para facilitar a rotina",
            "Incentivar a participação nas decisões do dia a dia",
            "Identificar formas de promover bem-estar e inclusão"
        ]
    ],
    "familia_cuidador" => [
        "titulo" => "Apoio à família e aos cuidadores",
        "descricao" => "O cuidado também inclui acolher quem acompanha essa jornada.",
        "icone" => "🤝",
        "tarefas" => [
            "Identificar pessoas e serviços que podem oferecer apoio",
            "Dividir responsabilidades sempre que possível",
            "Reservar momentos para descanso e autocuidado",
            "Buscar grupos de acolhimento e troca de experiências",
            "Anotar dúvidas e necessidades da família"
        ]
    ]
];

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

function voltarChecklist() {
    header("Location: minha-checklist.php");
    exit;
}



$stmt = $conexao->prepare(
    "SELECT etapa_atual
     FROM checklist_jornada
     WHERE usuario_id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$jornada = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$jornada) {
    $etapaAtual = "buscando_informacoes";

    $stmt = $conexao->prepare(
        "INSERT INTO checklist_jornada (usuario_id, etapa_atual)
         VALUES (?, ?)"
    );

    $stmt->bind_param("is", $usuarioId, $etapaAtual);
    $stmt->execute();
    $stmt->close();
} else {
    $etapaAtual = $jornada["etapa_atual"];

    if (!isset($etapas[$etapaAtual])) {
        $etapaAtual = "buscando_informacoes";
    }
}



if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["acao"] ?? "") === "alternar_tarefa"
) {
    header("Content-Type: application/json; charset=utf-8");

    $itemId = (int) ($_POST["item_id"] ?? 0);
    $concluida = $_POST["concluida"] ?? "";

    if ($itemId <= 0 || !in_array($concluida, ["0", "1"], true)) {
        http_response_code(400);
        echo json_encode(["sucesso" => false]);
        exit;
    }

    $novoStatus = $concluida === "1" ? "concluido" : "pendente";

    $stmt = $conexao->prepare(
        "UPDATE checklist_tarefas
         SET status = ?
         WHERE id = ? AND usuario_id = ?"
    );

    $stmt->bind_param("sii", $novoStatus, $itemId, $usuarioId);
    $sucesso = $stmt->execute();

    if ($sucesso && $stmt->affected_rows === 0) {
        $stmt->close();

        $stmt = $conexao->prepare(
            "SELECT id
             FROM checklist_tarefas
             WHERE id = ? AND usuario_id = ?
             LIMIT 1"
        );

        $stmt->bind_param("ii", $itemId, $usuarioId);
        $stmt->execute();
        $sucesso = $stmt->get_result()->num_rows > 0;
    }

    $stmt->close();

    echo json_encode(["sucesso" => $sucesso]);
    exit;
}



$stmtExiste = $conexao->prepare(
    "SELECT id
     FROM checklist_tarefas
     WHERE usuario_id = ? AND etapa = ? AND titulo = ?
     LIMIT 1"
);

$stmtInserir = $conexao->prepare(
    "INSERT INTO checklist_tarefas
     (usuario_id, etapa, titulo, status, personalizada)
     VALUES (?, ?, ?, 'pendente', 0)"
);

foreach ($etapas as $chaveEtapa => $dadosEtapa) {
    foreach ($dadosEtapa["tarefas"] as $tituloTarefa) {
        $stmtExiste->bind_param("iss", $usuarioId, $chaveEtapa, $tituloTarefa);
        $stmtExiste->execute();

        if ($stmtExiste->get_result()->num_rows === 0) {
            $stmtInserir->bind_param("iss", $usuarioId, $chaveEtapa, $tituloTarefa);
            $stmtInserir->execute();
        }
    }
}

$stmtExiste->close();
$stmtInserir->close();



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = $_POST["acao"] ?? "";

    if ($acao === "mudar_etapa") {
        $novaEtapa = $_POST["etapa"] ?? "";

        if (isset($etapas[$novaEtapa])) {
            $stmt = $conexao->prepare(
                "INSERT INTO checklist_jornada (usuario_id, etapa_atual)
                 VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE etapa_atual = VALUES(etapa_atual)"
            );

            $stmt->bind_param("is", $usuarioId, $novaEtapa);
            $stmt->execute();
            $stmt->close();
        }

        voltarChecklist();
    }

    if ($acao === "adicionar_tarefa") {
        $titulo = trim($_POST["titulo"] ?? "");

        if (
            $titulo !== "" &&
            function_exists("mb_strlen") &&
            mb_strlen($titulo, "UTF-8") <= 255
        ) {
            $stmt = $conexao->prepare(
                "INSERT INTO checklist_tarefas
                 (usuario_id, etapa, titulo, status, personalizada)
                 VALUES (?, ?, ?, 'pendente', 1)"
            );

            $stmt->bind_param("iss", $usuarioId, $etapaAtual, $titulo);
            $stmt->execute();
            $stmt->close();
        } elseif ($titulo !== "" && strlen($titulo) <= 255) {
            $stmt = $conexao->prepare(
                "INSERT INTO checklist_tarefas
                 (usuario_id, etapa, titulo, status, personalizada)
                 VALUES (?, ?, ?, 'pendente', 1)"
            );

            $stmt->bind_param("iss", $usuarioId, $etapaAtual, $titulo);
            $stmt->execute();
            $stmt->close();
        }

        voltarChecklist();
    }

    if ($acao === "editar_tarefa") {
        $itemId = (int) ($_POST["item_id"] ?? 0);
        $titulo = trim($_POST["titulo"] ?? "");
        $tamanhoValido = function_exists("mb_strlen")
            ? mb_strlen($titulo, "UTF-8") <= 255
            : strlen($titulo) <= 255;

        if ($itemId > 0 && $titulo !== "" && $tamanhoValido) {
            $stmt = $conexao->prepare(
                "UPDATE checklist_tarefas
                 SET titulo = ?
                 WHERE id = ? AND usuario_id = ? AND personalizada = 1"
            );

            $stmt->bind_param("sii", $titulo, $itemId, $usuarioId);
            $stmt->execute();
            $stmt->close();
        }

        voltarChecklist();
    }

    if ($acao === "excluir_tarefa") {
        $itemId = (int) ($_POST["item_id"] ?? 0);

        if ($itemId > 0) {
            $stmt = $conexao->prepare(
                "DELETE FROM checklist_tarefas
                 WHERE id = ? AND usuario_id = ? AND personalizada = 1"
            );

            $stmt->bind_param("ii", $itemId, $usuarioId);
            $stmt->execute();
            $stmt->close();
        }

        voltarChecklist();
    }
}



$stmt = $conexao->prepare(
    "SELECT id, titulo, status, personalizada
     FROM checklist_tarefas
     WHERE usuario_id = ? AND etapa = ?
     ORDER BY personalizada ASC, id ASC"
);

$stmt->bind_param("is", $usuarioId, $etapaAtual);
$stmt->execute();

$resultado = $stmt->get_result();
$tarefas = [];

while ($linha = $resultado->fetch_assoc()) {
    $tarefas[] = $linha;
}

$stmt->close();



$totalTarefas = count($tarefas);
$totalConcluidas = 0;

foreach ($tarefas as $tarefa) {
    if ($tarefa["status"] === "concluido") {
        $totalConcluidas++;
    }
}

$progresso = $totalTarefas > 0
    ? (int) round(($totalConcluidas / $totalTarefas) * 100)
    : 0;

include __DIR__ . "/includes/header.php";
?>

<style>
.checklist-pagina {
    --azul: #21479B;
    --azul-claro: #EAF2FF;
    --texto: #24334A;
    --cinza: #64748B;

    max-width: 1100px;
    width: calc(100% - 36px);
    margin: 55px auto 70px;
    color: var(--texto);
}

/* Botão de retorno ao guia */
.checklist-voltar {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 28px;
    padding: 11px 17px;
    border: 1px solid #DBEAFE;
    border-radius: 10px;
    background: #fff;
    color: var(--azul);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background .2s ease, transform .2s ease;
}

.checklist-voltar:hover {
    background: var(--azul-claro);
    transform: translateX(-3px);
}

.checklist-voltar span {
    font-size: 19px;
    line-height: 1;
}

.checklist-intro {
    margin-bottom: 25px;
}

.checklist-etiqueta {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: var(--azul-claro);
    color: var(--azul);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .5px;
}

.checklist-intro h1 {
    margin: 14px 0 10px;
    color: var(--azul);
    font-size: clamp(28px, 4vw, 38px);
}

.checklist-intro p,
.checklist-subtitulo {
    max-width: 700px;
    color: var(--cinza);
    font-size: 14px;
    line-height: 1.7;
}

.checklist-aviso {
    margin: 20px 0;
    padding: 15px 18px;
    border-left: 4px solid #60A5FA;
    border-radius: 8px;
    background: #F0F6FF;
    color: #344967;
    font-size: 14px;
    line-height: 1.7;
}

.checklist-etapa-card,
.checklist-progresso-card,
.checklist-lista-card {
    padding: 24px;
    margin-bottom: 20px;
    border: 1px solid #E4EAF3;
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 5px 22px rgba(33, 71, 155, .04);
}

.checklist-etapa-card h2,
.checklist-lista-card h2 {
    margin: 0 0 8px;
    color: var(--azul);
    font-size: 20px;
}

.checklist-etapa-form {
    display: flex;
    gap: 12px;
}

.checklist-etapa-form select,
.checklist-campo {
    box-sizing: border-box;
    width: 100%;
    min-width: 0;
    padding: 12px 14px;
    border: 1px solid #D7E0EE;
    border-radius: 10px;
    background: #fff;
    color: var(--texto);
    font-size: 14px;
}

.checklist-botao {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 12px 17px;
    border: 0;
    border-radius: 10px;
    background: var(--azul);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
}

.checklist-botao:hover {
    background: #183777;
}

.checklist-progresso-topo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.checklist-progresso-titulo {
    color: var(--azul);
    font-size: 19px;
    font-weight: 700;
}

.checklist-porcentagem {
    color: var(--azul);
    font-size: 25px;
    font-weight: 800;
}

.checklist-barra {
    width: 100%;
    height: 10px;
    overflow: hidden;
    border-radius: 20px;
    background: #E8EEF8;
}

.checklist-barra-preenchida {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #3467EB, #60A5FA);
    transition: width .25s ease;
}

.checklist-progresso-legenda {
    margin: 11px 0 0;
    color: var(--cinza);
    font-size: 13px;
}

.checklist-lista-topo {
    margin-bottom: 20px;
}

.checklist-lista-topo p {
    margin: 6px 0 0;
    color: var(--cinza);
    font-size: 14px;
    line-height: 1.6;
}

/* Cartão da tarefa */
.checklist-tarefa {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 17px;
    margin-top: 12px;
    border: 1px solid #E5EAF2;
    border-radius: 13px;
    background: #fff;
    transition: background .2s, border-color .2s;
}

.checklist-tarefa.concluida {
    border-color: #D5E5FA;
    background: #F8FBFF;
}

.checklist-tarefa-numero {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: var(--azul-claro);
    color: var(--azul);
    font-size: 13px;
    font-weight: 700;
}

.checklist-tarefa-conteudo {
    flex: 1;
    min-width: 0;
}

/* Bolinha e texto na mesma linha */
.checklist-marcar {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 0;
    color: var(--texto);
    font-size: 14px;
    line-height: 1.6;
    cursor: pointer;
}

.checklist-checkbox {
    appearance: none;
    -webkit-appearance: none;
    position: relative;
    display: inline-block;
    flex-shrink: 0;
    width: 22px;
    height: 22px;
    margin: 1px 0 0;
    border: 2px solid var(--azul);
    border-radius: 50%;
    background: #fff;
    cursor: pointer;
    transition: background .2s, border-color .2s;
}

.checklist-checkbox:checked {
    border-color: var(--azul);
    background: var(--azul);
}

.checklist-checkbox:checked::after {
    content: "✓";
    position: absolute;
    top: 50%;
    left: 50%;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    line-height: 1;
    transform: translate(-50%, -50%);
}

.checklist-checkbox:focus-visible {
    outline: 3px solid #93C5FD;
    outline-offset: 3px;
}

.checklist-checkbox:disabled {
    cursor: wait;
    opacity: .6;
}

.checklist-tarefa-titulo {
    display: block;
    color: var(--texto);
    font-size: 14px;
    font-weight: 600;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.checklist-tarefa.concluida .checklist-tarefa-titulo {
    color: #58718D;
    text-decoration: line-through;
}

.checklist-tarefa-tipo {
    display: inline-block;
    margin: 7px 0 0 32px;
    color: var(--cinza);
    font-size: 11px;
}

.checklist-tarefa-status {
    min-height: 17px;
    margin: 5px 0 0 32px;
    color: #166534;
    font-size: 12px;
}

.checklist-edicao {
    margin: 10px 0 0 32px;
}

.checklist-edicao summary {
    display: inline-block;
    color: var(--azul);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

.checklist-edicao form {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.checklist-edicao .checklist-campo {
    flex: 1;
}

.checklist-excluir {
    background: #FFF1F2;
    color: #BE123C;
}

.checklist-adicionar {
    margin-top: 24px;
    padding-top: 22px;
    border-top: 1px solid #E8EDF5;
}

.checklist-adicionar h3 {
    margin: 0 0 7px;
    color: var(--azul);
    font-size: 17px;
}

.checklist-adicionar p {
    color: var(--cinza);
    font-size: 13px;
    line-height: 1.6;
}

.checklist-adicionar form {
    display: flex;
    gap: 10px;
}

.checklist-vazio {
    padding: 25px 15px;
    border-radius: 12px;
    background: #F7F9FD;
    color: var(--cinza);
    text-align: center;
    font-size: 14px;
}

@media (max-width: 600px) {
    .checklist-pagina {
        width: calc(100% - 26px);
        margin-top: 35px;
    }

    .checklist-voltar {
        margin-bottom: 22px;
    }

    .checklist-etapa-card,
    .checklist-progresso-card,
    .checklist-lista-card {
        padding: 17px;
        border-radius: 14px;
    }

    .checklist-etapa-form,
    .checklist-adicionar form {
        flex-direction: column;
    }

    .checklist-etapa-form .checklist-botao,
    .checklist-adicionar .checklist-botao {
        width: 100%;
    }

    .checklist-tarefa {
        padding: 13px;
        gap: 10px;
    }

    .checklist-tarefa-numero {
        width: 28px;
        height: 28px;
    }

    .checklist-tarefa-tipo,
    .checklist-tarefa-status,
    .checklist-edicao {
        margin-left: 32px;
    }
}




.voltar-checklist {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;

    padding: 12px 18px;
    margin-bottom: 28px;

    background: #ffffff;
    color: #21479b;

    border: 1px solid #dce5f5;
    border-radius: 10px;

    box-shadow: 0 3px 10px rgba(33, 71, 155, 0.07);

    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    text-decoration: none;

    transition:
        background-color 0.25s ease,
        color 0.25s ease,
        border-color 0.25s ease,
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.voltar-checklist i {
    font-size: 14px;
    transition: transform 0.25s ease;
}

.voltar-checklist:hover {
    background: #21479b;
    color: #ffffff;
    border-color: #21479b;

    transform: translateY(-2px);

    box-shadow: 0 5px 14px rgba(33, 71, 155, 0.16);
}

.voltar-checklist:hover i {
    transform: translateX(-4px);
}

.voltar-checklist:focus-visible {
    outline: 3px solid #a9c5ff;
    outline-offset: 4px;
}

.voltar-checklist:active {
    transform: translateY(0);
}

@media (max-width: 600px) {
    .voltar-checklist {
        gap: 8px;
        padding: 11px 15px;
        margin-bottom: 22px;
        font-size: 13px;
        border-radius: 9px;
    }

    .voltar-checklist i {
        font-size: 13px;
    }
}

</style>


<main class="checklist-pagina">

    <section class="checklist-intro">

        <div class="checklist-intro-topo">

        
<a href="guia.php#trajetoria" class="voltar-checklist">
    <i class="fas fa-arrow-left"></i>
    Voltar para o guia
</a>


            <span class="checklist-etiqueta">
                SUA JORNADA, NO SEU TEMPO
            </span>

        
        </div>

        <h1>Minha checklist</h1>

        <p>
            Organize os próximos passos da sua jornada no seu próprio ritmo.
            Escolha sua etapa, acompanhe suas tarefas e marque cada conquista
            quando se sentir preparado. Seu progresso fica salvo para continuar
            quando quiser.
        </p>

    </section>


        <form method="POST" class="checklist-etapa-form">
            <input type="hidden" name="acao" value="mudar_etapa">

            <select name="etapa" aria-label="Etapa da jornada">
                <?php foreach ($etapas as $chave => $dados): ?>
                    <option
                        value="<?= escapar($chave) ?>"
                        <?= $etapaAtual === $chave ? "selected" : "" ?>
                    >
                        <?= escapar($dados["titulo"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="checklist-botao">
                Ver minha etapa
            </button>
        </form>
    </section>

    <section class="checklist-progresso-card">
        <div class="checklist-progresso-topo">
            <div>
                <div class="checklist-progresso-titulo">
                    <?= escapar($etapas[$etapaAtual]["icone"]) ?>
                    Meu progresso
                </div>

                <p class="checklist-progresso-legenda">
                    <?= escapar($etapas[$etapaAtual]["titulo"]) ?>
                </p>
            </div>

            <div class="checklist-porcentagem" id="checklistPorcentagem">
                <?= $progresso ?>%
            </div>
        </div>

        <div
            class="checklist-barra"
            id="checklistBarra"
            role="progressbar"
            aria-label="Progresso da etapa atual"
            aria-valuemin="0"
            aria-valuemax="100"
            aria-valuenow="<?= $progresso ?>"
        >
            <div
                class="checklist-barra-preenchida"
                id="checklistBarraPreenchida"
                style="width: <?= $progresso ?>%"
            ></div>
        </div>

        <p class="checklist-progresso-legenda" id="checklistContagem">
            <?= $totalConcluidas ?> de <?= $totalTarefas ?> tarefas concluídas.
        </p>
    </section>

    <section class="checklist-lista-card">
        <div class="checklist-lista-topo">
            <h2><?= escapar($etapas[$etapaAtual]["titulo"]) ?></h2>
            <p><?= escapar($etapas[$etapaAtual]["descricao"]) ?></p>
        </div>

        <?php if (empty($tarefas)): ?>
            <div class="checklist-vazio">
                Nenhuma tarefa por aqui ainda. Você pode adicionar a sua própria.
            </div>
        <?php endif; ?>

        <?php foreach ($tarefas as $indice => $tarefa): ?>
            <?php $concluida = $tarefa["status"] === "concluido"; ?>

            <article
                class="checklist-tarefa <?= $concluida ? "concluida" : "" ?>"
                data-tarefa
            >
                <div class="checklist-tarefa-numero">
                    <?= $indice + 1 ?>
                </div>

                <div class="checklist-tarefa-conteudo">

                    <label class="checklist-marcar">
                        <input
                            type="checkbox"
                            class="checklist-checkbox"
                            data-id="<?= (int) $tarefa["id"] ?>"
                            <?= $concluida ? "checked" : "" ?>
                        >

                        <span class="checklist-tarefa-titulo">
                            <?= escapar($tarefa["titulo"]) ?>
                        </span>
                    </label>

                    <span class="checklist-tarefa-tipo">
                        <?= (int) $tarefa["personalizada"] === 1
                            ? "Minha tarefa"
                            : "Tarefa sugerida" ?>
                    </span>

                    <div class="checklist-tarefa-status" aria-live="polite"></div>

                    <?php if ((int) $tarefa["personalizada"] === 1): ?>
                        <details class="checklist-edicao">
                            <summary>Editar ou excluir</summary>

                            <form method="POST">
                                <input type="hidden" name="acao" value="editar_tarefa">
                                <input
                                    type="hidden"
                                    name="item_id"
                                    value="<?= (int) $tarefa["id"] ?>"
                                >

                                <input
                                    class="checklist-campo"
                                    type="text"
                                    name="titulo"
                                    maxlength="255"
                                    required
                                    value="<?= escapar($tarefa["titulo"]) ?>"
                                    aria-label="Novo título da tarefa"
                                >

                                <button class="checklist-botao" type="submit">
                                    Salvar
                                </button>
                            </form>

                            
<form method="POST">
    <input type="hidden" name="acao" value="excluir_tarefa">
    <input
        type="hidden"
        name="item_id"
        value="<?= (int) $tarefa["id"] ?>"
    >

    <button
        class="checklist-botao checklist-excluir"
        type="submit"
    >
        Excluir tarefa
    </button>
</form>


                        </details>
                    <?php endif; ?>

                </div>
            </article>
        <?php endforeach; ?>

        <div class="checklist-adicionar">
            <h3>Adicionar minha própria tarefa</h3>

            <p>
                Escreva outra tarefa que seja importante para você.
                Ela ficará salva nesta etapa.
            </p>

            <form method="POST">
                <input type="hidden" name="acao" value="adicionar_tarefa">

                <input
                    class="checklist-campo"
                    type="text"
                    name="titulo"
                    maxlength="255"
                    placeholder="Ex.: pesquisar um serviço de apoio..."
                    aria-label="Nova tarefa"
                    required
                >

                <button class="checklist-botao" type="submit">
                    + Adicionar tarefa
                </button>
            </form>
        </div>
    </section>

</main>

<script>
document.querySelectorAll(".checklist-checkbox").forEach((checkbox) => {
    checkbox.addEventListener("change", async function () {
        const tarefa = this.closest("[data-tarefa]");
        const texto = tarefa.querySelector(".checklist-tarefa-titulo");
        const mensagem = tarefa.querySelector(".checklist-tarefa-status");

        const marcada = this.checked;
        const estadoAnterior = !marcada;

        this.disabled = true;
        mensagem.textContent = "Salvando...";

        const dados = new FormData();
        dados.append("acao", "alternar_tarefa");
        dados.append("item_id", this.dataset.id);
        dados.append("concluida", marcada ? "1" : "0");

        try {
            const resposta = await fetch("minha-checklist.php", {
                method: "POST",
                body: dados,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            if (!resposta.ok) {
                throw new Error("Falha ao salvar");
            }

            const resultado = await resposta.json();

            if (!resultado.sucesso) {
                throw new Error("Falha ao salvar");
            }

            tarefa.classList.toggle("concluida", marcada);

            mensagem.textContent = "Salvo!";

            const caixas = [
                ...document.querySelectorAll(".checklist-checkbox")
            ];

            const total = caixas.length;
            const feitas = caixas.filter(caixa => caixa.checked).length;

            const percentual = total > 0
                ? Math.round((feitas / total) * 100)
                : 0;

            document.getElementById("checklistPorcentagem").textContent =
                percentual + "%";

            document.getElementById("checklistBarraPreenchida")
                .style.width = percentual + "%";

            document.getElementById("checklistBarra")
                .setAttribute("aria-valuenow", percentual);

            document.getElementById("checklistContagem").textContent =
                `${feitas} de ${total} tarefas concluídas.`;

        } catch (erro) {
            this.checked = estadoAnterior;
            tarefa.classList.toggle("concluida", estadoAnterior);

            mensagem.textContent =
                "Não foi possível salvar. Tente novamente.";
        } finally {
            this.disabled = false;
        }
    });
});
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>
