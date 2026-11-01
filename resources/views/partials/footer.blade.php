<footer class="footer">

    <div class="footer-container">

        <!-- SOBRE A LOJA -->
        <div class="footer-column">

            <a class="footer-brand-logos" href="{{ route('home') }}" aria-label="LDM, página inicial">
                <img class="footer-main-logo" src="{{ asset('img/logo1.png') }}" alt="Logo LDM">
                <img class="footer-partner-logo" src="{{ asset('img/michele1.png') }}" alt="Logo Michele Aguilar">
            </a>

            <p class="footer-description">
                LDM: produtos para facilitar o seu dia a dia.
            </p>

            <div class="social-links">

                <a href="https://www.instagram.com/" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                    <svg class="social-icon social-icon-instagram" data-social-icon="instagram" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                        <circle cx="12" cy="12" r="4"></circle>
                        <circle class="social-icon-dot" cx="18" cy="6" r="1"></circle>
                    </svg>
                </a>

                <a href="https://www.facebook.com/" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                    <svg class="social-icon social-icon-facebook" data-social-icon="facebook" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M13.5 21v-8h2.8l.4-3h-3.2V8.1c0-.9.3-1.5 1.6-1.5h1.7V3.9c-.8-.1-1.6-.2-2.5-.2-2.5 0-4.2 1.5-4.2 4.3V10H7.3v3h2.8v8h3.4Z"></path>
                    </svg>
                </a>

                <a href="https://www.whatsapp.com/" aria-label="WhatsApp" target="_blank" rel="noopener noreferrer">
                    <svg class="social-icon social-icon-whatsapp" data-social-icon="whatsapp" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.5 3.5A11.8 11.8 0 0 0 2 17.7L.5 23.5l6-1.6A11.8 11.8 0 0 0 20.5 3.5ZM12 21a9.8 9.8 0 0 1-5-1.4l-.4-.2-3.5.9.9-3.4-.2-.4A9.8 9.8 0 1 1 12 21Zm5.4-7.3c-.3-.2-1.7-.9-2-.9s-.5-.2-.7.2-.8.9-1 1.1-.4.2-.7.1a8.1 8.1 0 0 1-2.4-1.5 9.2 9.2 0 0 1-1.7-2.1c-.2-.4 0-.5.2-.7l.5-.6.2-.5a.6.6 0 0 0 0-.5c-.1-.2-.7-1.6-.9-2.1-.2-.5-.5-.4-.7-.4H7.6c-.2 0-.5.1-.8.4s-1 1-1 2.4 1 2.8 1.2 3 .3.5 2.2 3.4a10.7 10.7 0 0 0 4.2 2.6c.6.2 1.1.4 1.5.4.6.1 1.2.1 1.7 0 .5-.1 1.7-.7 1.9-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.5Z"></path>
                    </svg>
                </a>

            </div>

        </div>


        <!-- LINKS DA LOJA -->
        <div class="footer-column">

            <h3>Loja</h3>

            <ul>
                <li>
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li>
                    <a href="{{ route('pesquisa', ['search' => '']) }}">Todos os produtos</a>
                </li>

                <li>
                    <a href="{{ route('pesquisa', ['search' => '']) }}">Categorias</a>
                </li>

                <li>
                    <a href="{{ route('pesquisa', ['search' => '']) }}">Ofertas</a>
                </li>
            </ul>

        </div>


        <!-- ATENDIMENTO -->
        <div class="footer-column">

            <h3>Atendimento</h3>

            <ul>
                <li>
                    <a href="{{ url('/contato') }}">Fale Conosco</a>
                </li>

                <li>
                    <a href="{{ url('/contato') }}">Perguntas Frequentes</a>
                </li>

                <li>
                    <a href="{{ url('/contato') }}">Trocas e Devoluções</a>
                </li>

                <li>
                    <a href="{{ url('/contato') }}">Política de Privacidade</a>
                </li>
            </ul>

        </div>


        <!-- CONTATO -->
        <div class="footer-column">

            <h3>Contato</h3>

            <div class="contact-item">
                <i class="fa-solid fa-location-dot"></i>
                <span>Campo Grande - MS</span>
            </div>

            <div class="contact-item">
                <i class="fa-solid fa-phone"></i>
                <span>(67) 99999-9999</span>
            </div>

            <div class="contact-item">
                <i class="fa-solid fa-envelope"></i>
                <span>contato@sualoja.com</span>
            </div>

        </div>

    </div>


    <!-- PARTE INFERIOR -->

    <div class="footer-bottom">

        <div class="footer-bottom-container">

            <p>
                © {{ date('Y') }} LDM. Todos os direitos reservados.
            </p>

            <div class="payment-methods">

                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
                <i class="fa-brands fa-pix"></i>

            </div>

        </div>

    </div>

</footer>