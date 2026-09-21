document.addEventListener('DOMContentLoaded', function () {
    var mainImage = document.querySelector('#product-main-image');
    var shareTrigger = document.querySelector('.product-share-trigger');
    var shareModal = document.querySelector('#share-product-modal');
    var shareCloseButtons = document.querySelectorAll('[data-share-close]');
    var copyLinkButton = document.querySelector('[data-share-copy]');
    var copyFeedback = document.querySelector('.share-copy-feedback');

    document.querySelectorAll('.product-thumbnail').forEach(function (thumbnail) {
        thumbnail.addEventListener('click', function () {
            if (!mainImage) {
                return;
            }

            mainImage.src = thumbnail.dataset.image;
            document.querySelectorAll('.product-thumbnail').forEach(function (item) {
                item.classList.remove('active');
            });
            thumbnail.classList.add('active');
        });
    });

    if (shareTrigger && shareModal) {
        shareTrigger.addEventListener('click', function () {
            shareModal.hidden = false;
            document.body.classList.add('share-modal-open');
        });

        shareCloseButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                shareModal.hidden = true;
                document.body.classList.remove('share-modal-open');
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !shareModal.hidden) {
                shareModal.hidden = true;
                document.body.classList.remove('share-modal-open');
            }
        });
    }

    if (copyLinkButton && copyFeedback) {
        copyLinkButton.addEventListener('click', function () {
            navigator.clipboard.writeText(window.location.href).then(function () {
                copyFeedback.textContent = 'Link copiado.';
            });
        });
    }
});