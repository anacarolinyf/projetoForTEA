<?php

session_start();

require_once "conexao.php";


/* =========================
   VERIFICA LOGIN
   ========================= */

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];


/* =========================
   BUSCA USUÁRIO
   ========================= */

$sql = "SELECT * FROM usuarios WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar consulta: " . $conexao->error);
}

$stmt->bind_param("i", $usuarioId);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$stmt->close();


if (!$usuario) {

    session_destroy();

    header("Location: login.php");
    exit;
}


$mensagem = "";
$erro = "";


/* =========================
   ATUALIZAR PERFIL
   ========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");

    $senhaAtual = $_POST["senha_atual"] ?? "";
    $novaSenha = $_POST["nova_senha"] ?? "";
    $confirmarSenha = $_POST["confirmar_senha"] ?? "";


    /* =========================
       VALIDA NOME E EMAIL
       ========================= */

    if ($nome === "" || $email === "") {

        $erro = "Nome e e-mail são obrigatórios.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = "Digite um e-mail válido.";

    } else {


        /* =========================
           VERIFICA EMAIL DUPLICADO
           ========================= */

        $sqlEmail = "
            SELECT id
            FROM usuarios
            WHERE email = ?
            AND id != ?
        ";

        $stmtEmail = $conexao->prepare($sqlEmail);

        if (!$stmtEmail) {

            $erro = "Erro ao verificar o e-mail.";

        } else {

            $stmtEmail->bind_param(
                "si",
                $email,
                $usuarioId
            );

            $stmtEmail->execute();

            $resultadoEmail = $stmtEmail->get_result();

            if ($resultadoEmail->num_rows > 0) {

                $erro = "Esse e-mail já está sendo usado por outra conta.";

            }

            $stmtEmail->close();
        }


        /* =========================
   FOTO
   ========================= */

$foto = $usuario["foto"] ?? null;

if (
    isset($_FILES["foto"]) &&
    $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {

        $erro = "Erro ao enviar a foto. Código: " .
                $_FILES["foto"]["error"];

    } elseif ($_FILES["foto"]["size"] > 5 * 1024 * 1024) {

        $erro = "A foto deve ter no máximo 5 MB.";

    } else {

        $tiposPermitidos = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        $tipo = mime_content_type(
            $_FILES["foto"]["tmp_name"]
        );

        if (!isset($tiposPermitidos[$tipo])) {

            $erro = "A foto precisa ser JPG, PNG ou WEBP.";

        } else {

            /*
             * Pasta física onde o arquivo será salvo
             */
            $pastaFisica = __DIR__ . "/uploads/perfis/";

            /*
             * Cria a pasta caso ela não exista
             */
            if (!is_dir($pastaFisica)) {

                if (!mkdir($pastaFisica, 0755, true)) {

                    $erro = "Não foi possível criar a pasta de fotos.";

                }
            }


            if ($erro === "") {

                $extensao = $tiposPermitidos[$tipo];

                $nomeArquivo =
                    "perfil_" .
                    $usuarioId .
                    "_" .
                    time() .
                    "." .
                    $extensao;


                /*
                 * Caminho físico
                 */
                $caminhoFisico =
                    $pastaFisica .
                    $nomeArquivo;


                /*
                 * Caminho que será salvo no banco
                 */
                $caminhoBanco =
                    "uploads/perfis/" .
                    $nomeArquivo;


                if (move_uploaded_file(
    $_FILES["foto"]["tmp_name"],
    $caminhoFisico
)) {

    /*
     * Caminho que será salvo no banco
     */
    $foto = $caminhoBanco;

    /*
     * Atualiza a foto imediatamente no banco
     */
    $sqlFoto = "
        UPDATE usuarios
        SET foto = ?
        WHERE id = ?
    ";

    $stmtFoto = $conexao->prepare($sqlFoto);

    if (!$stmtFoto) {

        $erro = "Erro ao preparar atualização da foto: " . $conexao->error;

    } else {

        $stmtFoto->bind_param(
            "si",
            $foto,
            $usuarioId
        );

        if (!$stmtFoto->execute()) {

            $erro = "Erro ao salvar a foto no banco: " . $stmtFoto->error;

        }

        $stmtFoto->close();
    }

} else {

    $erro = "Não foi possível salvar a foto. Verifique a pasta uploads/perfis.";
}

                    /*
                     * Apaga a foto anterior
                     */
                    if (
                        !empty($usuario["foto"])
                    ) {

                        $fotoAnterior =
                            __DIR__ . "/" .
                            $usuario["foto"];

                        if (
                            file_exists($fotoAnterior)
                        ) {

                            unlink($fotoAnterior);

                        }
                    }


                    /*
                     * Guarda o caminho no banco
                     */
                    $foto = $caminhoBanco;


                } else {

                    $erro =
                        "Não foi possível salvar a foto. " .
                        "Verifique as permissões da pasta.";

                }
            }
        }
    }
}


        /* =========================
           ALTERAÇÃO DE SENHA
           ========================= */

        if (
            $erro === "" &&
            $novaSenha !== ""
        ) {

            if ($senhaAtual === "") {

                $erro = "Digite sua senha atual.";

            } elseif (
                !password_verify(
                    $senhaAtual,
                    $usuario["senha"]
                )
            ) {

                $erro = "A senha atual está incorreta.";

            } elseif (
                $novaSenha !== $confirmarSenha
            ) {

                $erro = "As novas senhas não são iguais.";

            } elseif (
                strlen($novaSenha) < 6
            ) {

                $erro = "A nova senha deve ter pelo menos 6 caracteres.";

            } else {

                $senhaHash = password_hash(
                    $novaSenha,
                    PASSWORD_DEFAULT
                );


                $sqlUpdate = "
                    UPDATE usuarios
                    SET nome = ?,
                        email = ?,
                        senha = ?,
                        foto = ?
                    WHERE id = ?
                ";

                $stmtUpdate = $conexao->prepare($sqlUpdate);


                if (!$stmtUpdate) {

                    $erro = "Erro ao atualizar o perfil.";

                } else {

                    $stmtUpdate->bind_param(
                        "ssssi",
                        $nome,
                        $email,
                        $senhaHash,
                        $foto,
                        $usuarioId
                    );

                    if (!$stmtUpdate->execute()) {

                        $erro = "Não foi possível atualizar o perfil.";

                    }

                    $stmtUpdate->close();
                }
            }

        } elseif ($erro === "") {


            /* =========================
               ATUALIZA SEM ALTERAR SENHA
               ========================= */

            $sqlUpdate = "
                UPDATE usuarios
                SET nome = ?,
                    email = ?,
                    foto = ?
                WHERE id = ?
            ";

            $stmtUpdate = $conexao->prepare($sqlUpdate);


            if (!$stmtUpdate) {

                $erro = "Erro ao atualizar o perfil.";

            } else {

                $stmtUpdate->bind_param(
                    "sssi",
                    $nome,
                    $email,
                    $foto,
                    $usuarioId
                );


                if (!$stmtUpdate->execute()) {

                    $erro = "Não foi possível atualizar o perfil.";

                }

                $stmtUpdate->close();
            }
        }


        /* =========================
           ATUALIZA SESSÃO
           ========================= */

        if ($erro === "") {

            $_SESSION["usuario_id"] = $usuarioId;
            $_SESSION["nome"] = $nome;
            $_SESSION["usuario_nome"] = $nome;
            $_SESSION["email"] = $email;


            $usuario["nome"] = $nome;
            $usuario["email"] = $email;
            $usuario["foto"] = $foto;


            $mensagem = "Perfil atualizado com sucesso!";
        }
    }

    require_once "includes/header.php";

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Editar perfil - ForTEA</title>

    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/perfil.css">

    <link rel="icon"
          type="image"
          href="img/logoo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<script>
function mostrarSenha(id, botao) {
    const campo = document.getElementById(id);
    const mostrar = campo.type === "password";

    campo.type = mostrar ? "text" : "password";

    botao.setAttribute(
        "aria-label",
        mostrar ? "Esconder senha" : "Mostrar senha"
    );

    botao.innerHTML = mostrar
        ? `<svg xmlns="http://www.w3.org/2000/svg"
               width="20" height="20" viewBox="0 0 24 24"
               fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round"
               stroke-linejoin="round">
             <path d="M3 3l18 18"/>
             <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
             <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5
                      c6.5 0 10 7 10 7a16 16 0 0 1-4 4.8"/>
             <path d="M6.2 6.2C3.5 8 2 12 2 12
                      s3.5 7 10 7a10 10 0 0 0 4-.8"/>
           </svg>`
        : `<svg xmlns="http://www.w3.org/2000/svg"
               width="20" height="20" viewBox="0 0 24 24"
               fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round"
               stroke-linejoin="round">
             <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
             <circle cx="12" cy="12" r="3"/>
           </svg>`;
}
</script>

<body>


<main class="editar-pagina">

    <section class="editar-card">

        <div class="editar-cabecalho">

            <a href="perfil.php"
               class="voltar-perfil">

                <i class="fa-solid fa-arrow-left"></i>

                Voltar ao perfil

            </a>

            <h1>Editar perfil</h1>

            <p>
                Atualize suas informações da conta.
            </p>

        </div>


        <?php if ($mensagem): ?>

            <div class="mensagem-sucesso">
                <?= htmlspecialchars($mensagem) ?>
            </div>

        <?php endif; ?>


        <?php if ($erro): ?>

            <div class="mensagem-erro">
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>


        <form method="POST"
              enctype="multipart/form-data">


            <!-- FOTO -->

            <div class="foto-edicao">

                <div class="foto-preview">

                    <?php if (!empty($usuario["foto"])): ?>

                        <img src="<?= htmlspecialchars($usuario["foto"]) ?>"
                             alt="Foto de perfil">

                    <?php else: ?>

                        <span>
                            <?= strtoupper(
                                substr($usuario["nome"], 0, 1)
                            ) ?>
                        </span>

                    <?php endif; ?>

                </div>


                <div class="foto-texto">

                    <h3>Foto de perfil</h3>

                    <p>
                        Escolha uma foto para aparecer no seu perfil.
                    </p>

                    <label class="botao-foto">

                        <i class="fa-solid fa-camera"></i>

                        Escolher foto

                        <input type="file"
                               name="foto"
                               accept="image/jpeg,image/png,image/webp"
                               hidden>

                    </label>

                    <small>
                        JPG, PNG ou WEBP. Máximo de 5 MB.
                    </small>

                </div>

            </div>


            <!-- NOME -->

            <div class="campo-editar">

                <label for="nome">
                    Nome completo
                </label>

                <input type="text"
                       id="nome"
                       name="nome"
                       value="<?= htmlspecialchars($usuario["nome"]) ?>"
                       required>

            </div>


            <!-- EMAIL -->

            <div class="campo-editar">

                <label for="email">
                    E-mail
                </label>

                <input type="email"
                       id="email"
                       name="email"
                       value="<?= htmlspecialchars($usuario["email"]) ?>"
                       required>

            </div>


            <div class="linha-senha">

                <!-- SENHA ATUAL -->

                    <div class="campo-editar">
                        <label for="senha_atual">
                        Senha atual
                    </label>
    <input type="password"
           id="senha_atual"
           name="senha_atual"
           placeholder="Digite sua senha atual">

    <button type="button"
            class="olho-senha"
            onclick="mostrarSenha('senha_atual', this)"
            aria-label="Mostrar senha">
        <svg xmlns="http://www.w3.org/2000/svg"
             width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
    </button>
</div>


                <!-- NOVA SENHA -->

                <div class="campo-editar">

                    <label for="nova_senha">
                        Nova senha
                    </label>

                    <input type="password"
                           id="nova_senha"
                           name="nova_senha"
                           placeholder="Nova senha">

                    <button type="button"
            class="olho-senha"
            onclick="mostrarSenha('senha_atual', this)"
            aria-label="Mostrar senha">
        <svg xmlns="http://www.w3.org/2000/svg"
             width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
    </button>

                </div>

            </div>


            <!-- CONFIRMAR SENHA -->

            <div class="campo-editar">

                <label for="confirmar_senha">
                    Confirmar nova senha
                </label>

                <input type="password"
                       id="confirmar_senha"
                       name="confirmar_senha"
                       placeholder="Repita a nova senha">

                       <button type="button"
            class="olho-senha"
            onclick="mostrarSenha('senha_atual', this)"
            aria-label="Mostrar senha">
        <svg xmlns="http://www.w3.org/2000/svg"
             width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
    </button>

            </div>


            <div class="editar-acoes">

                <a href="perfil.php"
                   class="botao-cancelar">

                    Cancelar

                </a>

                <button type="submit"
                        class="botao-salvar">

                    <i class="fa-solid fa-check"></i>

                    Salvar alterações

                </button>

            </div>

        </form>

    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/accessibility-widgets@latest/widget.js"></script>

</body>
</html>