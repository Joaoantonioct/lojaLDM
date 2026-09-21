<div class="share-modal" id="share-product-modal" role="dialog" aria-modal="true" aria-labelledby="share-modal-title" hidden>
    <div class="share-modal-backdrop" data-share-close></div>

    <section class="share-modal-content">
        <button class="share-modal-close" type="button" aria-label="Fechar compartilhamento" data-share-close>&times;</button>
        <h2 id="share-modal-title">Compartilhe este produto</h2>

        <div class="share-options">
            <a class="share-option" href="https://wa.me/?text={{ urlencode($product->name.' '.url()->current()) }}" target="_blank" rel="noopener"><span class="share-option-icon">WA</span><span>WhatsApp</span></a>
            <a class="share-option" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"><span class="share-option-icon">f</span><span>Facebook</span></a>
            <a class="share-option" href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"><span class="share-option-icon">X</span><span>X</span></a>
            <a class="share-option" href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}" target="_blank" rel="noopener"><span class="share-option-icon">TG</span><span>Telegram</span></a>
            <button class="share-option" type="button" data-share-copy><span class="share-option-icon">&#9993;</span><span>Copiar link</span></button>
        </div>

        <p class="share-copy-feedback" role="status" aria-live="polite"></p>
    </section>
</div>
