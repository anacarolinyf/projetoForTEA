<?php
include 'includes/header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$enviado = false;
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $assunto  = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (!$nome || !$email || !$assunto || !$mensagem) {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } else {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'projetofortea@gmail.com';
            $mail->Password   = 'eohp qpen zplk nrqz'; // senha de app de 16 letras
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // Quem envia (precisa ser o próprio Gmail)
            $mail->setFrom('projetofortea@gmail.com', 'Contato ForTEA');

            // Quem recebe
            $mail->addAddress('projetofortea@gmail.com');

            // Ao clicar em "Responder", responde direto pra pessoa
            $mail->addReplyTo($email, $nome);

            $mail->isHTML(true);
            $mail->Subject = 'Contato ForTEA: ' . $assunto;
            $mail->Body    = '
                <h3>Nova mensagem pelo site ForTEA</h3>
                <p><strong>Nome:</strong> ' . htmlspecialchars($nome) . '</p>
                <p><strong>E-mail:</strong> ' . htmlspecialchars($email) . '</p>
                <p><strong>Assunto:</strong> ' . htmlspecialchars($assunto) . '</p>
                <p><strong>Mensagem:</strong><br>' . nl2br(htmlspecialchars($mensagem)) . '</p>
            ';
            $mail->AltBody = "Nome: $nome\nE-mail: $email\nAssunto: $assunto\n\n$mensagem";

            $mail->send();
            $enviado = true;

        } catch (Exception $e) {
    $erro = 'Não foi possível enviar sua mensagem agora. Tente novamente mais tarde.';
    $erro .= ' [DEBUG: ' . $mail->ErrorInfo . ']';
}
    }
}
?>

<main class="pagina-contato">

    <section class="contato-hero">

        <div class="contato-hero-inner">

            <span class="contato-label">
                ENTRE EM CONTATO
            </span>

            <h1>
                Estamos aqui para ouvir você
            </h1>

            <p>
                Tem alguma dúvida, sugestão ou precisa de mais informações?
                Entre em contato com a equipe ForTEA.
            </p>

        </div>

    </section>


    <section class="contato-area">

        <div class="contato-container">

            <div class="contato-grid">


                <!-- INFORMAÇÕES -->

                <div class="contato-info">

                    <h2>
                        Fale conosco
                    </h2>

                    <p>
                        O ForTEA foi criado para oferecer informação,
                        orientação e acolhimento. Sua mensagem é importante
                        para continuarmos construindo um espaço cada vez mais
                        acessível e útil para as famílias.
                    </p>


                    <div class="contato-item">

                        <div class="contato-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>

                        <div class="contato-item-text">

                            <strong>E-mail</strong>

                            <span>
                                projetofortea@gmail.com
                            </span>

                        </div>

                    </div>


                    


                    <div class="contato-item">

                        <div class="contato-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>

                        <div class="contato-item-text">

                            <strong>Localização</strong>

                            <span>
                                Ribeirão Pires - SP <br>
                                Etec Professora Maria Cristina Medeiros
                            </span>

                        </div>

                    </div>


                    <div class="contato-aviso">

                        <i class="fa-solid fa-circle-info"></i>

                        <p>
                            As informações enviadas pelo formulário serão
                            utilizadas apenas para responder à sua solicitação.
                        </p>

                    </div>

                </div>


                <!-- FORMULÁRIO -->

                <div class="contato-form">

                    <h2>
                        Envie uma mensagem
                    </h2>

                    <?php if ($enviado): ?>
    <div class="contato-sucesso">
        <i class="fa-solid fa-circle-check"></i>
        Mensagem enviada com sucesso! Responderemos em breve.
    </div>
<?php elseif ($erro): ?>
    <div class="contato-erro">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>

                    <form method="POST" action="contato.php">


                        <div class="contato-field">

                            <label for="nome">
                                Nome
                            </label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                placeholder="Digite seu nome"
                                required
                            >

                        </div>


                        <div class="contato-field">

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


                        <div class="contato-field">

                            <label for="assunto">
                                Assunto
                            </label>

                            <input
                                type="text"
                                id="assunto"
                                name="assunto"
                                placeholder="Qual é o assunto?"
                                required
                            >

                        </div>


                        <div class="contato-field">

                            <label for="mensagem">
                                Mensagem
                            </label>

                            <textarea
                                id="mensagem"
                                name="mensagem"
                                placeholder="Digite sua mensagem..."
                                required
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="contato-button"
                        >
                            <i class="fa-solid fa-paper-plane"></i>
                            Enviar mensagem
                        </button>


                    </form>

                </div>


            </div>

        </div>

    </section>

    <style>
        .contato-sucesso,
.contato-erro {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
}
.contato-sucesso { background: #e6f7ec; color: #1e7a3d; }
.contato-erro    { background: #fdeaea; color: #b3261e; }
    </style>

</main>

<?php include 'includes/footer.php'; ?>