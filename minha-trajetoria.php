
<?php

session_start();
require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = (int) $_SESSION["usuario_id"];



function escaparTrajetoria($valor) {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        "UTF-8"
    );
}



function salvarFotoTrajetoria($arquivo, $usuarioId) {
    if (
        !$arquivo ||
        !isset($arquivo["error"]) ||
        $arquivo["error"] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if (
        $arquivo["error"] !== UPLOAD_ERR_OK ||
        $arquivo["size"] > 5 * 1024 * 1024
    ) {
        return false;
    }

    $tiposPermitidos = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp",
        "image/gif" => "gif"
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo["tmp_name"]);

    if (!isset($tiposPermitidos[$mime])) {
        return false;
    }

    $diretorio = __DIR__ . "/uploads/trajetoria";

    if (
        !is_dir($diretorio) &&
        !mkdir($diretorio, 0755, true)
    ) {
        return false;
    }

    $nomeArquivo = "registro_" . (int) $usuarioId . "_" .
        bin2hex(random_bytes(8)) . "." . $tiposPermitidos[$mime];

    if (
        !move_uploaded_file(
            $arquivo["tmp_name"],
            $diretorio . "/" . $nomeArquivo
        )
    ) {
        return false;
    }

    return "uploads/trajetoria/" . $nomeArquivo;
}



if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["editar_registro"])
) {
    $id = filter_input(
        INPUT_POST,
        "registro_id",
        FILTER_VALIDATE_INT
    );

    $titulo = trim($_POST["titulo"] ?? "");
    $tipo = trim($_POST["tipo"] ?? "");
    $data = trim($_POST["data_registro"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    $atual = null;

    if ($id) {
        $stmtAtual = $conexao->prepare(
            "SELECT foto
             FROM trajetoria_registros
             WHERE id = ? AND usuario_id = ?"
        );

        $stmtAtual->bind_param("ii", $id, $usuario_id);
        $stmtAtual->execute();

        $atual = $stmtAtual->get_result()->fetch_assoc();
        $stmtAtual->close();
    }

    if (
        $id &&
        $atual &&
        $titulo !== "" &&
        $tipo !== "" &&
        $data !== ""
    ) {
        $foto = salvarFotoTrajetoria(
            $_FILES["foto"] ?? null,
            $usuario_id
        );

        if ($foto === false) {
            header("Location: minha-trajetoria.php?erro=foto");
            exit;
        }

        if ($foto === null) {
            $foto = $atual["foto"] ?? null;
        }

        $sql = "UPDATE trajetoria_registros
                SET titulo = ?,
                    tipo = ?,
                    data_registro = ?,
                    descricao = ?,
                    foto = ?
                WHERE id = ? AND usuario_id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "sssssii",
            $titulo,
            $tipo,
            $data,
            $descricao,
            $foto,
            $id,
            $usuario_id
        );

        $stmt->execute();
        $stmt->close();
    }

    header("Location: minha-trajetoria.php");
    exit;
}



if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["adicionar_registro"])
) {
    $titulo = trim($_POST["titulo"] ?? "");
    $tipo = trim($_POST["tipo"] ?? "");
    $data = trim($_POST["data_registro"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    if (
        $titulo === "" ||
        $tipo === "" ||
        $data === ""
    ) {
        header("Location: minha-trajetoria.php");
        exit;
    }

    $dataValida = DateTime::createFromFormat("!Y-m-d", $data);

    if (
        !$dataValida ||
        $dataValida->format("Y-m-d") !== $data
    ) {
        header("Location: minha-trajetoria.php");
        exit;
    }

    $foto = salvarFotoTrajetoria(
        $_FILES["foto"] ?? null,
        $usuario_id
    );

    if ($foto === false) {
        header("Location: minha-trajetoria.php?erro=foto");
        exit;
    }

    $sql = "INSERT INTO trajetoria_registros
            (
                usuario_id,
                titulo,
                tipo,
                data_registro,
                profissional,
                local,
                descricao,
                foto
            )
            VALUES (?, ?, ?, ?, '', '', ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "isssss",
        $usuario_id,
        $titulo,
        $tipo,
        $data,
        $descricao,
        $foto
    );

    $stmt->execute();
    $stmt->close();

    header("Location: minha-trajetoria.php");
    exit;
}



if (isset($_GET["excluir"])) {
    $id = filter_input(
        INPUT_GET,
        "excluir",
        FILTER_VALIDATE_INT
    );

    if ($id) {
        $stmt = $conexao->prepare(
            "SELECT foto
             FROM trajetoria_registros
             WHERE id = ? AND usuario_id = ?"
        );

        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();

        $registroExcluir = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($registroExcluir) {
            $stmt = $conexao->prepare(
                "DELETE FROM trajetoria_registros
                 WHERE id = ? AND usuario_id = ?"
            );

            $stmt->bind_param("ii", $id, $usuario_id);
            $stmt->execute();
            $stmt->close();

            /*
             * A foto também é removida do servidor
             * se estiver dentro da pasta da trajetória.
             */
            if (!empty($registroExcluir["foto"])) {
                $caminhoFoto = __DIR__ . "/" .
                    $registroExcluir["foto"];

                $pastaPermitida = realpath(
                    __DIR__ . "/uploads/trajetoria"
                );

                $fotoReal = realpath($caminhoFoto);

                if (
                    $pastaPermitida &&
                    $fotoReal &&
                    strpos(
                        $fotoReal,
                        $pastaPermitida . DIRECTORY_SEPARATOR
                    ) === 0 &&
                    is_file($fotoReal)
                ) {
                    unlink($fotoReal);
                }
            }
        }
    }

    header("Location: minha-trajetoria.php");
    exit;
}



$registro_edicao = null;

if (isset($_GET["editar"])) {
    $id_edicao = filter_input(
        INPUT_GET,
        "editar",
        FILTER_VALIDATE_INT
    );

    if ($id_edicao) {
        $stmtEdicao = $conexao->prepare(
            "SELECT *
             FROM trajetoria_registros
             WHERE id = ? AND usuario_id = ?"
        );

        $stmtEdicao->bind_param(
            "ii",
            $id_edicao,
            $usuario_id
        );

        $stmtEdicao->execute();

        $registro_edicao = $stmtEdicao
            ->get_result()
            ->fetch_assoc();

        $stmtEdicao->close();
    }
}



$sql = "SELECT *
        FROM trajetoria_registros
        WHERE usuario_id = ?
        ORDER BY data_registro DESC, id DESC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$registros = $stmt->get_result();
$totalRegistros = $registros->num_rows;

include __DIR__ . "/includes/header.php";

?>

<link rel="stylesheet" href="css/trajetoria.css">
<link rel="stylesheet" href="css/trajetoria-ajustes.css">

<main>

<section class="trajetoria-pagina">

    <div class="container-trajetoria">

  <a href="guia.php#trajetoria" class="voltar-trajetoria">
    <i class="fas fa-arrow-left"></i>
    Voltar para o guia
</a>
        <!-- APRESENTAÇÃO -->

        <div class="trajetoria-intro">

            <span>MINHA TRAJETÓRIA</span>

            <h1>Organize cada etapa do caminho</h1>

            <p>
                Guarde conquistas, descobertas e experiências importantes.
                Compartilhe apenas o que fizer sentido para você.
            </p>

        </div>

        <!-- RESUMO DE REGISTROS -->

        <div class="resumo-trajetoria">

            <div class="resumo-card">

                <div class="resumo-icone">
                    <i class="fas fa-book-open"></i>
                </div>

                <div>
                    <strong><?= (int) $totalRegistros ?></strong>
                    <span>
                        <?= $totalRegistros === 1
                            ? "Registro realizado"
                            : "Registros realizados" ?>
                    </span>
                </div>

            </div>

        </div>

        <div class="trajetoria-layout">

            <!-- FORMULÁRIO DE REGISTRO -->

            <section
                class="registro-area"
                id="form-registro-trajetoria"
            >

                <div class="area-titulo">

                    <div>
                        <span>ACOMPANHAMENTO</span>

                        <h2>
                            <?= $registro_edicao
                                ? "Editar lembrança"
                                : "Guardar um momento" ?>
                        </h2>
                    </div>

                    <i class="fas fa-pen-to-square"></i>

                </div>

                <?php if (
                    isset($_GET["erro"]) &&
                    $_GET["erro"] === "foto"
                ): ?>

                    <p class="erro-foto-trajetoria">
                        Não foi possível enviar a foto.
                        Escolha uma imagem JPG, PNG, WEBP ou GIF
                        de até 5 MB.
                    </p>

                <?php endif; ?>

                <form
                    method="POST"
                    class="form-registro"
                    enctype="multipart/form-data"
                >

                    <?php if ($registro_edicao): ?>

                        <input
                            type="hidden"
                            name="registro_id"
                            value="<?= (int) $registro_edicao["id"] ?>"
                        >

                    <?php endif; ?>

                    <div class="campo-trajetoria campo-grande">

                        <label for="titulo-trajetoria">
                            Título
                        </label>

                        <input
                            id="titulo-trajetoria"
                            type="text"
                            name="titulo"
                            maxlength="255"
                            value="<?= escaparTrajetoria(
                                $registro_edicao["titulo"] ?? ""
                            ) ?>"
                            placeholder="Ex.: Uma conquista que quero lembrar"
                            required
                        >

                    </div>

                    <div class="linha-campos">

                        <div class="campo-trajetoria">

                            <label for="tipo-trajetoria">
                                Tipo
                            </label>

                            <select
                                id="tipo-trajetoria"
                                name="tipo"
                                required
                            >

                                <option value="">
                                    Escolha uma opção
                                </option>

                                <?php
                                $tiposRegistro = [
                                    "Conquista",
                                    "Descoberta",
                                    "Experiência",
                                    "Consulta",
                                    "Terapia",
                                    "Avaliação",
                                    "Escola",
                                    "Reunião",
                                    "Outro"
                                ];

                                foreach ($tiposRegistro as $tipoOpcao):
                                ?>

                                    <option
                                        value="<?= escaparTrajetoria($tipoOpcao) ?>"
                                        <?= (
                                            ($registro_edicao["tipo"] ?? "") === $tipoOpcao
                                        ) ? "selected" : "" ?>
                                    >
                                        <?= escaparTrajetoria($tipoOpcao) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="campo-trajetoria">

                            <label for="data-trajetoria">
                                Data
                            </label>

                            <input
                                id="data-trajetoria"
                                type="date"
                                name="data_registro"
                                value="<?= escaparTrajetoria(
                                    $registro_edicao["data_registro"]
                                    ?? date("Y-m-d")
                                ) ?>"
                                required
                            >

                        </div>

                    </div>

                    <div class="campo-trajetoria">

                        <label for="foto-trajetoria">
                            Foto (opcional)
                        </label>

                        <input
                            id="foto-trajetoria"
                            type="file"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                        >

                        <small>
                            Adicione uma imagem para guardar junto
                            desse momento. Até 5 MB.
                        </small>

                        <?php if (
                            !empty($registro_edicao["foto"])
                        ): ?>

                            <div class="foto-atual-edicao">

                                <img
                                    src="<?= escaparTrajetoria(
                                        $registro_edicao["foto"]
                                    ) ?>"
                                    alt="Foto atual do registro"
                                    class="foto-atual-preview"
                                >

                                <small>
                                    A foto atual será mantida se você
                                    não escolher outra.
                                </small>

                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="campo-trajetoria">

                        <label for="descricao-trajetoria">
                            O que você gostaria de guardar sobre
                            esse momento? (opcional)
                        </label>

                        <textarea
                            id="descricao-trajetoria"
                            name="descricao"
                            rows="4"
                            maxlength="10000"
                            placeholder="Conte do seu jeito, no seu tempo. Pode ser uma conquista, algo novo que descobriu ou uma lembrança especial..."
                        ><?= escaparTrajetoria(
                            $registro_edicao["descricao"] ?? ""
                        ) ?></textarea>

                    </div>

                    <button
                        type="submit"
                        name="<?= $registro_edicao
                            ? "editar_registro"
                            : "adicionar_registro" ?>"
                        class="botao-adicionar"
                    >

                        <i class="fas <?= $registro_edicao
                            ? "fa-floppy-disk"
                            : "fa-heart" ?>"></i>

                        <?= $registro_edicao
                            ? "Salvar alterações"
                            : "Guardar lembrança" ?>

                    </button>

                    <?php if ($registro_edicao): ?>

                        <a
                            href="minha-trajetoria.php"
                            class="botao-cancelar-edicao"
                        >
                            Cancelar edição
                        </a>

                    <?php endif; ?>

                </form>

            </section>

            <!-- HISTÓRICO -->

            <section class="registros-area">

                <div class="area-titulo">

                    <div>
                        <span>HISTÓRICO</span>
                        <h2>Seus registros</h2>
                    </div>

                    <i class="fas fa-clock-rotate-left"></i>

                </div>

                <?php if ($totalRegistros > 0): ?>

                    <div class="lista-registros">

                        <?php while (
                            $registro = $registros->fetch_assoc()
                        ): ?>

                            <article class="registro-card">

                                <div class="registro-data">

                                    <strong>
                                        <?= date(
                                            "d/m",
                                            strtotime($registro["data_registro"])
                                        ) ?>
                                    </strong>

                                    <span>
                                        <?= date(
                                            "Y",
                                            strtotime($registro["data_registro"])
                                        ) ?>
                                    </span>

                                </div>

                                <div class="registro-conteudo">

                                    <span class="registro-tipo">
                                        <?= escaparTrajetoria(
                                            $registro["tipo"]
                                        ) ?>
                                    </span>

                                    <h3>
                                        <?= escaparTrajetoria(
                                            $registro["titulo"]
                                        ) ?>
                                    </h3>

                                    <?php if (
                                        !empty($registro["descricao"])
                                    ): ?>

                                        <p>
                                            <?= nl2br(
                                                escaparTrajetoria(
                                                    $registro["descricao"]
                                                )
                                            ) ?>
                                        </p>

                                    <?php endif; ?>

                                    <?php if (
                                        !empty($registro["foto"])
                                    ): ?>

                                        <img
                                            class="foto-registro-trajetoria"
                                            src="<?= escaparTrajetoria(
                                                $registro["foto"]
                                            ) ?>"
                                            alt="Foto do registro <?= escaparTrajetoria(
                                                $registro["titulo"]
                                            ) ?>"
                                            loading="lazy"
                                        >

                                    <?php endif; ?>

                                </div>

                                <a
                                    href="minha-trajetoria.php?editar=<?= (int) $registro["id"] ?>#form-registro-trajetoria"
                                    class="editar-registro"
                                    title="Editar registro"
                                    aria-label="Editar registro"
                                >
                                    <i class="fas fa-pen"></i>
                                </a>

                                <a
                                    href="#"
                                    class="excluir-registro"
                                    onclick="abrirConfirmacao(<?= (int) $registro["id"] ?>); return false;"
                                    title="Excluir registro"
                                    aria-label="Excluir registro"
                                >
                                    <i class="fas fa-trash"></i>
                                </a>

                            </article>

                        <?php endwhile; ?>

                    </div>

                <?php else: ?>

                    <div class="sem-registros">

                        <i class="fas fa-folder-open"></i>

                        <h3>Este espaço é seu</h3>

                        <p>
                            Quando quiser, registre uma conquista,
                            uma descoberta ou uma lembrança importante.
                            Você decide o que compartilhar.
                        </p>

                    </div>

                <?php endif; ?>

            </section>

        </div>

        <!-- PRIVACIDADE -->

        <div class="privacidade-trajetoria">

            <i class="fas fa-lock"></i>

            <div>

                <strong>Seus registros são pessoais</strong>

                <p>
                    As informações adicionadas ficam vinculadas
                    à sua conta e podem ser acessadas novamente
                    quando você entrar no ForTEA.
                </p>

            </div>

        </div>

    </div>

</section>

</main>

<!-- MODAL DE CONFIRMAÇÃO -->

<div
    class="confirmacao-modal"
    id="confirmacaoModal"
    aria-hidden="true"
>

    <div
        class="confirmacao-caixa"
        role="dialog"
        aria-modal="true"
        aria-labelledby="tituloConfirmacao"
    >

        <button
            type="button"
            class="fechar-confirmacao"
            onclick="fecharConfirmacao()"
            aria-label="Fechar"
        >
            <i class="fas fa-xmark"></i>
        </button>

        <div class="confirmacao-icone">
            <i class="fas fa-trash"></i>
        </div>

        <h3 id="tituloConfirmacao">
            Excluir registro?
        </h3>

        <p>
            Tem certeza que deseja excluir este registro da sua trajetória?
            Essa ação não poderá ser desfeita.
        </p>

        <div class="confirmacao-botoes">

            <button
                type="button"
                class="botao-cancelar"
                onclick="fecharConfirmacao()"
            >
                Cancelar
            </button>

            <a
                href="#"
                id="confirmarExclusao"
                class="botao-confirmar"
            >
                Excluir registro
            </a>

        </div>

    </div>

</div>

<script>
function abrirConfirmacao(id) {
    const modal = document.getElementById("confirmacaoModal");
    const link = document.getElementById("confirmarExclusao");

    link.href = "minha-trajetoria.php?excluir=" + encodeURIComponent(id);

    modal.classList.add("ativo");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-aberto");
}

function fecharConfirmacao() {
    const modal = document.getElementById("confirmacaoModal");

    modal.classList.remove("ativo");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-aberto");
}

document.getElementById("confirmacaoModal").addEventListener(
    "click",
    function(event) {
        if (event.target === this) {
            fecharConfirmacao();
        }
    }
);

document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        fecharConfirmacao();
    }
});
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>
