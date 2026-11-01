document.addEventListener('DOMContentLoaded', function () {
    const filterDisclosure = document.querySelector('.search-filter-disclosure');

    if (filterDisclosure) {
        const portraitFilters = window.matchMedia('(max-width: 800px) and (orientation: portrait)');
        const syncFilterDisclosure = function () {
            filterDisclosure.open = !portraitFilters.matches;
        };

        syncFilterDisclosure();
        portraitFilters.addEventListener('change', syncFilterDisclosure);
        window.addEventListener('resize', syncFilterDisclosure);
    }

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
        const destino = produtos.dataset.search === 'true'
            ? produtos.closest('.search-layout')
            : produtos;

        requestAnimationFrame(() => {
            destino?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }
});