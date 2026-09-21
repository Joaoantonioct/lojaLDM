document.addEventListener('DOMContentLoaded', function () {
    const ordenacao = document.getElementById('ordenacao');
    const gradeProdutos = document.querySelector('.search-product-grid');

    if (ordenacao && gradeProdutos) {
        const produtos = Array.from(gradeProdutos.querySelectorAll('.search-product-card'));

        ordenacao.addEventListener('change', function () {
            const produtosOrdenados = [...produtos];

            if (ordenacao.value === 'menor-preco') {
                produtosOrdenados.sort((primeiro, segundo) => {
                    return Number(primeiro.dataset.price) - Number(segundo.dataset.price);
                });
            }

            if (ordenacao.value === 'maior-preco') {
                produtosOrdenados.sort((primeiro, segundo) => {
                    return Number(segundo.dataset.price) - Number(primeiro.dataset.price);
                });
            }

            produtosOrdenados.forEach((produto) => gradeProdutos.appendChild(produto));
        });
    }

    const produtos = document.getElementById('produtos');

    if (produtos && (window.location.hash === '#produtos' || produtos.dataset.search === 'true')) {
        produtos.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});