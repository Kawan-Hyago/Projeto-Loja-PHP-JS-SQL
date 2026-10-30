const tabela = document.getElementById('tabela');
const mensagem = document.getElementById('mensagem');

function mostrarMensagem(texto) {
    mensagem.textContent = texto;
    mensagem.hidden = false;
}

function limparMensagem() {
    mensagem.textContent = '';
    mensagem.hidden = true;
}

function criarCelula(texto) {
    const celula = document.createElement('td');
    celula.textContent = texto;
    return celula;
}

function montarLinha(cliente) {
    const linha = document.createElement('tr');
    linha.appendChild(criarCelula(cliente.id));
    linha.appendChild(criarCelula(cliente.nome));
    linha.appendChild(criarCelula(cliente.endereco ?? ''));

    const acoes = document.createElement('td');

    const editar = document.createElement('a');
    editar.className = 'botao';
    editar.href = `cadastrar.html?id=${cliente.id}`;
    editar.textContent = 'Editar';

    const remover = document.createElement('button');
    remover.type = 'button';
    remover.textContent = 'Remover';
    remover.addEventListener('click', () => removerCliente(cliente));

    acoes.appendChild(editar);
    acoes.appendChild(remover);
    linha.appendChild(acoes);

    return linha;
}

async function carregarClientes() {
    try {
        const clientes = await chamarApi('cliente', 'listar');
        tabela.innerHTML = '';

        if (clientes.length === 0) {
            const linha = document.createElement('tr');
            const celula = criarCelula('Nenhum registro cadastrado');
            celula.colSpan = 4;
            linha.appendChild(celula);
            tabela.appendChild(linha);
            return;
        }

        clientes.forEach(cliente => tabela.appendChild(montarLinha(cliente)));
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

async function removerCliente(cliente) {
    if (!confirm(`Deseja remover o cliente "${cliente.nome}"?`)) {
        return;
    }

    try {
        limparMensagem();
        await chamarApi('cliente', 'deletar', { id: cliente.id });
        await carregarClientes();
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

document.addEventListener('DOMContentLoaded', carregarClientes);
