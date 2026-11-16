const btnAdicionar = document.getElementById('btnAdicionarProduto');
const listaProdutos = document.getElementById('listaProdutos');
const modeloLinha = document.getElementById('modeloLinhaProduto');

function adicionarLinhaProduto() {
    const linha = modeloLinha.content.cloneNode(true);
    listaProdutos.appendChild(linha);
    atualizarContador();
}

function atualizarContador() {
    const total = listaProdutos.querySelectorAll('.linha-produto').length;
    document.getElementById('totalItens').textContent = total;
}

if (btnAdicionar && listaProdutos && modeloLinha) {
    btnAdicionar.addEventListener('click', adicionarLinhaProduto);

    // delegacao porque as linhas sao criadas depois
    listaProdutos.addEventListener('click', function (event) {
        const botao = event.target.closest('.btn-remover');
        if (!botao) return;

        if (listaProdutos.querySelectorAll('.linha-produto').length === 1) {
            alert('O frete precisa ter pelo menos um produto.');
            return;
        }
        botao.closest('.linha-produto').remove();
        atualizarContador();
    });

    if (listaProdutos.querySelectorAll('.linha-produto').length === 0) {
        adicionarLinhaProduto();
    }
    atualizarContador();
}

// nao deixa escolher a mesma origem e destino
const formFrete = document.getElementById('formFrete');
if (formFrete) {
    formFrete.addEventListener('submit', function (event) {
        const origem = document.getElementById('origem_id').value;
        const destino = document.getElementById('destino_id').value;
        if (origem !== '' && origem === destino) {
            event.preventDefault();
            alert('A origem e o destino não podem ser o mesmo endereço.');
        }
    });
}

// busca CEP no ViaCEP
const campoCep = document.getElementById('cep');
if (campoCep) {
    campoCep.addEventListener('input', function () {
        let v = campoCep.value.replace(/\D/g, '').slice(0, 8);
        if (v.length > 5) {
            v = v.slice(0, 5) + '-' + v.slice(5);
        }
        campoCep.value = v;
    });

    campoCep.addEventListener('blur', function () {
        const cep = campoCep.value.replace(/\D/g, '');
        if (cep.length !== 8) return;

        const aviso = document.getElementById('avisoCep');
        aviso.textContent = 'Buscando CEP...';

        fetch('https://viacep.com.br/ws/' + cep + '/json/')
            .then(resposta => resposta.json())
            .then(dados => {
                if (dados.erro) {
                    aviso.textContent = 'CEP não encontrado, preencha manualmente.';
                    return;
                }
                document.getElementById('logradouro').value = dados.logradouro;
                document.getElementById('bairro').value = dados.bairro;
                document.getElementById('cidade').value = dados.localidade;
                document.getElementById('uf').value = dados.uf;
                aviso.textContent = '';
                document.getElementById('numero').focus();
            })
            .catch(() => {
                aviso.textContent = 'Não consegui consultar o CEP agora, preencha manualmente.';
            });
    });
}

document.querySelectorAll('.btn-excluir').forEach(function (link) {
    link.addEventListener('click', function (event) {
        if (!confirm('Tem certeza que deseja excluir este registro?')) {
            event.preventDefault();
        }
    });
});
