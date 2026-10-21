

<div class="chatbot-container">

    <button
        class="chatbot-button"
        id="chatbotButton"
        aria-label="Abrir assistente ForTEA"
        type="button"
    >
        <img src="img/iconchat.png" alt="Ícone do chatbot">
    </button>

    <div class="chatbot-box" id="chatbotBox">

        <div class="chatbot-header">

            <div>
                <strong>Assistente ForTEA</strong>
                <span>Estamos aqui para ajudar 💜</span>
            </div>

            <button
                type="button"
                class="chatbot-close"
                id="chatbotClose"
                aria-label="Fechar chatbot"
            >
                ×
            </button>

        </div>

        <div class="chatbot-messages" id="chatbotMessages">

            <div class="bot-message">
                Olá! <br><br>
                Sou o assistente do ForTEA. Como posso ajudar?
            </div>

            <div class="chatbot-options">

                <button type="button" data-question="O que é o ForTEA?">
                    O que é o ForTEA?
                </button>

                <button type="button" data-question="Quero saber sobre educação inclusiva">
                    Educação inclusiva
                </button>

                <button type="button" data-question="Quero saber sobre direitos">
                    Direitos
                </button>

                <button type="button" data-question="Quero acessar a biblioteca">
                    Biblioteca
                </button>

                <button type="button" data-question="Quero acessar o guia para famílias">
                    Guia para Famílias
                </button>
                
                <button type="button" data-question="Quero fazer minha trajetória">
                    Minha Trajetória
                </button>

                <button type="button" data-question="Preciso de ajuda">
                    Preciso de ajuda
                </button>

            </div>

        </div>

        <form class="chatbot-input-area" id="chatbotForm">

            <input
                type="text"
                id="chatbotInput"
                placeholder="Digite sua dúvida..."
                autocomplete="off"
            >

            <button type="submit" aria-label="Enviar mensagem">
                ➤
            </button>

        </form>

    </div>

</div>