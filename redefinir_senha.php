<?php

session_start();

require_once "conexao.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "PHPMailer/src/Exception.php";
require_once "PHPMailer/src/PHPMailer.php";
require_once "PHPMailer/src/SMTP.php";




$erro = '';
$sucesso = '';

// Enquanto estiver testando no XAMPP, deixe true para ver o erro real do e-mail.
// Quando o site for publicado, troque para false.
$modoDesenvolvimento = true;


// "Solicitar outro código": limpa a recuperação e volta para a etapa do e-mail
if (isset($_GET['reiniciar'])) {

    unset(
        $_SESSION['recuperacao_usuario_id'],
        $_SESSION['recuperacao_email'],
        $_SESSION['recuperacao_verificada'],
        $_SESSION['recuperacao_id'],
        $_SESSION['etapa_recuperacao']
    );

    header("Location: redefinir_senha.php");
    exit;
}


// Define a etapa atual
$etapa = $_SESSION['etapa_recuperacao'] ?? 'email';


// Se acabou de entrar na página, mantém a etapa
if (!isset($_SESSION['etapa_recuperacao'])) {
    $_SESSION['etapa_recuperacao'] = 'email';
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_codigo'])) {

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {

        $erro = "Digite seu e-mail.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = "Digite um e-mail válido.";

    } else {

        // Procura o usuário pelo e-mail
        $stmt = $conexao->prepare(
            "SELECT id, nome, email FROM usuarios WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            $erro = "Não encontramos uma conta com esse e-mail.";

        } else {

            $usuario = $resultado->fetch_assoc();

            $usuarioId = $usuario['id'];
            $nomeUsuario = $usuario['nome'];
            $emailUsuario = $usuario['email'];


            

            $codigo = random_int(100000, 999999);

            // Criptografa o código antes de salvar no banco
            $codigoHash = password_hash($codigo, PASSWORD_DEFAULT);

            // Código válido por 10 minutos
            $expiraEm = date('Y-m-d H:i:s', time() + 600);


            

            $stmtInvalida = $conexao->prepare(
                "UPDATE recuperacao_senha
                 SET usado = 1
                 WHERE usuario_id = ? AND usado = 0"
            );

            $stmtInvalida->bind_param("i", $usuarioId);
            $stmtInvalida->execute();


            

            $stmtCodigo = $conexao->prepare(
                "INSERT INTO recuperacao_senha
                 (usuario_id, codigo_hash, expira_em, usado)
                 VALUES (?, ?, ?, 0)"
            );

            $stmtCodigo->bind_param(
                "iss",
                $usuarioId,
                $codigoHash,
                $expiraEm
            );

            if (!$stmtCodigo->execute()) {

                $erro = "Não foi possível gerar o código. Tente novamente.";

            } else {

                

                $configEmail = require "config_email.php";

                $mail = new PHPMailer(true);

                try {

                    $mail->isSMTP();

                    $mail->Host = $configEmail['host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $configEmail['username'];
                    $mail->Password = $configEmail['password'];

                    $mail->SMTPSecure = $configEmail['encryption'];
                    $mail->Port = $configEmail['port'];

                    $mail->CharSet = 'UTF-8';

                    
                    $mail->setFrom(
                        $configEmail['from_email'],
                        $configEmail['from_name']
                    );

                    

                    $mail->addAddress(
                        $emailUsuario,
                        $nomeUsuario
                    );


                    

                    $mail->isHTML(true);

                    $mail->Subject = 'Código de recuperação de senha - ForTEA';

                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto;'>

                            <h2 style='color: #333;'>ForTEA</h2>

                            <p>Olá, <strong>" . htmlspecialchars($nomeUsuario) . "</strong>!</p>

                            <p>
                                Recebemos uma solicitação para redefinir a senha
                                da sua conta.
                            </p>

                            <p>Seu código de recuperação é:</p>

                            <div style='
                                font-size: 32px;
                                font-weight: bold;
                                letter-spacing: 8px;
                                text-align: center;
                                padding: 20px;
                                background: #f4f4f4;
                                margin: 20px 0;
                            '>
                                $codigo
                            </div>

                            <p>
                                Este código é válido por <strong>10 minutos</strong>.
                            </p>

                            <p>
                                Se você não solicitou a recuperação da senha,
                                ignore este e-mail.
                            </p>

                            <p>
                                Atenciosamente,<br>
                                <strong>Equipe ForTEA</strong>
                            </p>

                        </div>
                    ";

                    $mail->AltBody =
                        "Olá, $nomeUsuario!\n\n" .
                        "Seu código de recuperação de senha do ForTEA é: $codigo\n\n" .
                        "Este código é válido por 10 minutos.";


                    

                    $mail->send();


                   

                    $_SESSION['recuperacao_usuario_id'] = $usuarioId;
                    $_SESSION['recuperacao_email'] = $emailUsuario;
                    $_SESSION['etapa_recuperacao'] = 'codigo';

                    header("Location: redefinir_senha.php");
                    exit;


                } catch (Exception $e) {

                    error_log("Erro PHPMailer (recuperação de senha): " . $mail->ErrorInfo);

                    if ($modoDesenvolvimento) {
                        $erro = "Erro ao enviar e-mail: " . $mail->ErrorInfo;
                    } else {
                        $erro = "Não foi possível enviar o e-mail agora. Tente novamente em alguns minutos.";
                    }
                }
            }
        }
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_codigo'])) {

    $codigoDigitado = trim($_POST['codigo'] ?? '');

    $usuarioId = $_SESSION['recuperacao_usuario_id'] ?? null;

    if (!$usuarioId) {

        $erro = "A sessão de recuperação expirou. Solicite um novo código.";

        $_SESSION['etapa_recuperacao'] = 'email';

        $etapa = 'email';

    } elseif (!preg_match('/^[0-9]{6}$/', $codigoDigitado)) {

        $erro = "Digite o código de 6 números.";

        $etapa = 'codigo';

    } else {

        

        $stmt = $conexao->prepare(
            "SELECT id, codigo_hash, expira_em
             FROM recuperacao_senha
             WHERE usuario_id = ?
             AND usado = 0
             ORDER BY id DESC
             LIMIT 1"
        );

        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            $erro = "Código inválido ou já utilizado.";

            $etapa = 'codigo';

        } else {

            $recuperacao = $resultado->fetch_assoc();

           

            if (strtotime($recuperacao['expira_em']) < time()) {

                $erro = "O código expirou. Solicite um novo código.";

                $etapa = 'codigo';

            } elseif (!password_verify(
                $codigoDigitado,
                $recuperacao['codigo_hash']
            )) {

                $erro = "Código incorreto.";

                $etapa = 'codigo';

            } else {

                

                $_SESSION['recuperacao_verificada'] = true;
                $_SESSION['recuperacao_id'] = $recuperacao['id'];
                $_SESSION['etapa_recuperacao'] = 'nova_senha';

                header("Location: redefinir_senha.php");
                exit;
            }
        }
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['redefinir_senha'])) {

    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    $usuarioId = $_SESSION['recuperacao_usuario_id'] ?? null;
    $recuperacaoId = $_SESSION['recuperacao_id'] ?? null;
    $verificada = $_SESSION['recuperacao_verificada'] ?? false;


    if (!$usuarioId || !$recuperacaoId || !$verificada) {

        $erro = "A recuperação não foi validada. Solicite um novo código.";

        $_SESSION['etapa_recuperacao'] = 'email';

        $etapa = 'email';

    } elseif (strlen($novaSenha) < 6) {

        $erro = "A nova senha deve ter pelo menos 6 caracteres.";

        $etapa = 'nova_senha';

    } elseif ($novaSenha !== $confirmarSenha) {

        $erro = "As senhas não são iguais.";

        $etapa = 'nova_senha';

    } else {

        

        $senhaHash = password_hash(
            $novaSenha,
            PASSWORD_DEFAULT
        );


        

        $stmt = $conexao->prepare(
            "UPDATE usuarios
             SET senha = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $senhaHash,
            $usuarioId
        );


        if ($stmt->execute()) {

            

            $stmtUsado = $conexao->prepare(
                "UPDATE recuperacao_senha
                 SET usado = 1
                 WHERE id = ?"
            );

            $stmtUsado->bind_param(
                "i",
                $recuperacaoId
            );

            $stmtUsado->execute();


            

            unset($_SESSION['recuperacao_usuario_id']);
            unset($_SESSION['recuperacao_email']);
            unset($_SESSION['recuperacao_verificada']);
            unset($_SESSION['recuperacao_id']);
            unset($_SESSION['etapa_recuperacao']);



            header("Location: login.php?senha=alterada");
            exit;

        } else {

            $erro = "Não foi possível alterar a senha. Tente novamente.";

            $etapa = 'nova_senha';
        }
    }
}




$etapa = $_SESSION['etapa_recuperacao'] ?? $etapa ?? 'email';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recuperar senha - ForTEA</title>

    <link rel="stylesheet" href="css/redefinir_senha.css">

    <link rel="icon" type="image/png" href="img/logoo.png">

</head>

<body>

<div class="pagina-recuperacao">

    <div class="caixa-recuperacao">

        <div class="logo-recuperacao">
            ForTEA
        </div>


        <?php if (!empty($erro)): ?>

            <div class="mensagem erro">
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>


        <?php if (!empty($sucesso)): ?>

            <div class="mensagem sucesso">
                <?= htmlspecialchars($sucesso) ?>
            </div>

        <?php endif; ?>


       

        <?php if ($etapa === 'email'): ?>

            <h1>Esqueci minha senha</h1>

            <p class="subtitulo">
                Digite o e-mail cadastrado na sua conta.
            </p>

            <form method="POST">

                <div class="campo">

                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        required
                    >

                </div>

                <button
                    type="submit"
                    name="enviar_codigo"
                    class="botao"
                >
                    Enviar código
                </button>

            </form>


            <a href="login.php" class="voltar">
                ← Voltar para o login
            </a>


        

        <?php elseif ($etapa === 'codigo'): ?>

            <h1>Digite o código</h1>

            <p class="subtitulo">

                Enviamos um código de 6 números para:

                <strong>
                    <?= htmlspecialchars($_SESSION['recuperacao_email'] ?? '') ?>
                </strong>

            </p>


            <form method="POST">

                <div class="campo">

                    <label for="codigo">
                        Código de recuperação
                    </label>

                    <input
                        type="text"
                        id="codigo"
                        name="codigo"
                        placeholder="000000"
                        maxlength="6"
                        inputmode="numeric"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="confirmar_codigo"
                    class="botao"
                >
                    Confirmar código
                </button>

            </form>


            <a href="redefinir_senha.php?reiniciar=1" class="voltar">
                Solicitar outro código
            </a>


        

        <?php elseif ($etapa === 'nova_senha'): ?>

            <h1>Nova senha</h1>

            <p class="subtitulo">
                Crie uma nova senha para sua conta.
            </p>


            <form method="POST">

                <div class="campo">

                    <label for="nova_senha">
                        Nova senha
                    </label>

                    <input
                        type="password"
                        id="nova_senha"
                        name="nova_senha"
                        placeholder="Digite sua nova senha"
                        minlength="6"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="confirmar_senha">
                        Confirmar nova senha
                    </label>

                    <input
                        type="password"
                        id="confirmar_senha"
                        name="confirmar_senha"
                        placeholder="Repita sua nova senha"
                        minlength="6"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="redefinir_senha"
                    class="botao"
                >
                    Alterar senha
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>

</body>

</html>