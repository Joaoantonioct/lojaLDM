@if($publications->isNotEmpty())
    <section class="publication-section" aria-labelledby="publication-section-title">
        <div class="catalog-section-heading">
            <h2 id="publication-section-title">Combinações</h2>
            <span>{{ $publications->count() }} disponíveis</span>
        </div>

        <div class="publication-grid">
            @foreach($publications as $publication)
                <article class="publication-card">
                    <div class="publication-meta">
                        <span class="publication-avatar" aria-hidden="true">L</span>
                        <div class="publication-author">
                            <strong>LDM</strong>
                            <time datetime="{{ $publication->created_at->toIso8601String() }}">{{ $publication->created_at->format('d/m/Y') }}</time>
                        </div>
                    </div>
                    <img
                        class="publication-image"
                        src="{{ asset('storage/'.$publication->image_path) }}"
                        alt="{{ $publication->title }}"
                        loading="lazy"
                    >
                    <div class="publication-content">
                        <h3>{{ $publication->title }}</h3>
                        @if($publication->description)
                            <p class="publication-description">{{ $publication->description }}</p>
                        @endif
                        <div class="publication-tags" aria-label="Categorias do look">
                            @foreach($publication->items->pluck('product.category.name')->filter()->unique() as $categoryName)
                                <span class="publication-tag">{{ $categoryName }}</span>
                            @endforeach
                        </div>
                        <ul class="publication-items">
                            @foreach($publication->items as $item)
                                <li>
                                    <span>{{ $item->item_name }}</span>
                                    <strong>R$ {{ number_format($item->currentPriceCents() / 100, 2, ',', '.') }}</strong>
                                </li>
                            @endforeach
                        </ul>
                        <p class="publication-total">
                            <span>Total da combinação</span>
                            <strong>R$ {{ number_format($publication->totalCents() / 100, 2, ',', '.') }}</strong>
                        </p>
                        @auth
                            <button
                                class="publication-buy-button"
                                type="button"
                                data-publication-buy
                                data-publication-id="{{ $publication->id }}"
                                data-checkout-url="{{ route('cart.test.store') }}"
                                data-csrf-token="{{ csrf_token() }}"
                            >Comprar combinação</button>
                        @else
                            <a class="publication-buy-button" href="{{ route('login', ['redirect' => route('cart.index')]) }}">
                                Entre para comprar
                            </a>
                        @endauth
                        <p class="publication-buy-feedback" data-publication-feedback role="status" aria-live="polite" hidden></p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif