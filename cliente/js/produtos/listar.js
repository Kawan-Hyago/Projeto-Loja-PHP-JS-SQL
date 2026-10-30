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

function montarLinha(produto) {
    const linha = document.createElement('tr');
    linha.appendChild(criarCelula(produto.id));
    linha.appendChild(criarCelula(produto.nome));
    linha.appendChild(criarCelula(Number(produto.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })));
    linha.appendChild(criarCelula(produto.fabricante ?? ''));

    const acoes = document.createElement('td');

    const editar = document.createElement('a');
    editar.className = 'botao';
    editar.href = `cadastrar.html?id=${produto.id}`;
    editar.textContent = 'Editar';

    const remover = document.createElement('button');
    remover.type = 'button';
    remover.textContent = 'Remover';
    remover.addEventListener('click', () => removerProduto(produto));

    acoes.appendChild(editar);
    acoes.appendChild(remover);
    linha.appendChild(acoes);

    return linha;
}

async function carregarProdutos() {
    try {
        const produtos = await chamarApi('produto', 'listar');
        tabela.innerHTML = '';

        if (produtos.length === 0) {
            const linha = document.createElement('tr');
            const celula = criarCelula('Nenhum registro cadastrado');
            celula.colSpan = 5;
            linha.appendChild(celula);
            tabela.appendChild(linha);
            return;
        }

        produtos.forEach(produto => tabela.appendChild(montarLinha(produto)));
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

async function removerProduto(produto) {
    if (!confirm(`Deseja remover o produto "${produto.nome}"?`)) {
        return;
    }

    try {
        limparMensagem();
        await chamarApi('produto', 'deletar', { id: produto.id });
        await carregarProdutos();
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

document.addEventListener('DOMContentLoaded', carregarProdutos);
