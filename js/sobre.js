

const progressBar = document.getElementById("progress-bar");

window.addEventListener("scroll", () => {

    const altura =
        document.documentElement.scrollHeight - window.innerHeight;

    const progresso = (window.scrollY / altura) * 100;

    progressBar.style.width = progresso + "%";

});




const reveals = document.querySelectorAll(".reveal");

function revelar() {

    reveals.forEach((item) => {

        const posicao = item.getBoundingClientRect().top;

        const tela = window.innerHeight - 120;

        if (posicao < tela) {

            item.classList.add("active");

        }

    });

}

window.addEventListener("scroll", revelar);

revelar();




const topBtn = document.getElementById("topBtn");

window.addEventListener("scroll", () => {

    if (window.scrollY > 350) {

        topBtn.classList.add("show");

    } else {

        topBtn.classList.remove("show");

    }

});

topBtn.addEventListener("click", () => {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });

});




const secoes = document.querySelectorAll("section[id]");

const links = document.querySelectorAll(".menu-lateral a");

window.addEventListener("scroll", () => {

    let atual = "";

    secoes.forEach(secao => {

        const topo = secao.offsetTop - 180;

        const altura = secao.offsetHeight;

        if (window.scrollY >= topo &&
            window.scrollY < topo + altura) {

            atual = secao.id;

        }

    });

    links.forEach(link => {

        link.classList.remove("active");

        if (link.getAttribute("href") === "#" + atual) {

            link.classList.add("active");

        }

    });

});




const numeros = document.querySelectorAll(".numero");

let iniciou = false;

window.addEventListener("scroll", () => {

    const area = document.querySelector(".contadores");

    if (!area) return;

    if (iniciou) return;

    if (window.scrollY + window.innerHeight > area.offsetTop + 100) {

        iniciou = true;

        numeros.forEach(numero => {

            const alvo = +numero.dataset.numero;

            let atual = 0;

            const incremento = alvo / 60;

            const timer = setInterval(() => {

                atual += incremento;

                if (atual >= alvo) {

                    numero.innerText = alvo + "+";

                    clearInterval(timer);

                } else {

                    numero.innerText = Math.floor(atual);

                }

            }, 25);

        });

    }

});



const imagem = document.querySelector(".sobre-imagem img");

window.addEventListener("scroll", () => {

    if (!imagem) return;

    imagem.style.transform =
        "translateY(" + window.scrollY * 0.10 + "px)";

});

