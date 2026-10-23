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

                <a href="#" aria-label="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>

                <a href="#" aria-label="Facebook">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>

                <a href="#" aria-label="WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
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