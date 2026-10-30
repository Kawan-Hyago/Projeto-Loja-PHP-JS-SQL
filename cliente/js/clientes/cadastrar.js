const formulario = document.getElementById('formulario');
const titulo = document.getElementById('titulo');
const mensagem = document.getElementById('mensagem');
const campoNome = document.getElementById('nome');
const campoEndereco = document.getElementById('endereco');

const idCliente = new URLSearchParams(location.search).get('id');

function mostrarMensagem(texto) {
    mensagem.textContent = texto;
    mensagem.hidden = false;
}

function limparMensagem() {
    mensagem.textContent = '';
    mensagem.hidden = true;
}

async function preencherFormulario() {
    titulo.textContent = 'Editar cliente';
    try {
        const clientes = await chamarApi('cliente', 'listar');
        const cliente = clientes.find(item => String(item.id) === String(idCliente));

        if (!cliente) {
            mostrarMensagem('Cliente não encontrado');
            return;
        }

        campoNome.value = cliente.nome;
        campoEndereco.value = cliente.endereco ?? '';
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    limparMensagem();

    const dados = {
        nome: campoNome.value.trim(),
        endereco: campoEndereco.value.trim()
    };

    try {
        if (idCliente) {
            dados.id = Number(idCliente);
            await chamarApi('cliente', 'editar', dados);
        } else {
            await chamarApi('cliente', 'salvar', dados);
        }
        window.location.href = 'listar.html';
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    if (idCliente) {
        preencherFormulario();
    }
});
