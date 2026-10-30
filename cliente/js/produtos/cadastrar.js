const formulario = document.getElementById('formulario');
const titulo = document.getElementById('titulo');
const mensagem = document.getElementById('mensagem');
const campoNome = document.getElementById('nome');
const campoPreco = document.getElementById('preco');
const campoFabricante = document.getElementById('fabricante');

const idProduto = new URLSearchParams(location.search).get('id');

function mostrarMensagem(texto) {
    mensagem.textContent = texto;
    mensagem.hidden = false;
}

function limparMensagem() {
    mensagem.textContent = '';
    mensagem.hidden = true;
}

async function preencherFormulario() {
    titulo.textContent = 'Editar produto';
    try {
        const produtos = await chamarApi('produto', 'listar');
        const produto = produtos.find(item => String(item.id) === String(idProduto));

        if (!produto) {
            mostrarMensagem('Produto não encontrado');
            return;
        }

        campoNome.value = produto.nome;
        campoPreco.value = produto.preco;
        campoFabricante.value = produto.fabricante ?? '';
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    limparMensagem();

    const dados = {
        nome: campoNome.value.trim(),
        preco: Number(campoPreco.value),
        fabricante: campoFabricante.value.trim()
    };

    try {
        if (idProduto) {
            dados.id = Number(idProduto);
            await chamarApi('produto', 'editar', dados);
        } else {
            await chamarApi('produto', 'salvar', dados);
        }
        window.location.href = 'listar.html';
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    if (idProduto) {
        preencherFormulario();
    }
});
