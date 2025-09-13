<?php

class Footer
{

    public static function getFooterSiteEscola(): void
    {
        ?>
        <footer class="footer mt-auto py-4">
            <div class="container">
                <div class="row justify-content-between align-items-center gy-4">
                    <!-- Logo e Informações -->
                    <div class="col-lg-8 col-md-12 text-center text-md-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <a href="home" class="me-3 flex-shrink-0">
                                <img src="imagens/logo.png" alt="Maffei" class="logo img-fluid">
                            </a>
                            <div>
                                <p class="mb-1"><strong>Colégio Estadual João Maffei Rosa</strong></p>
                                <p class="mb-0 footer-text">
                                    <i class="fa-solid fa-location-dot footer-icon me-2"></i>
                                    R. Tamôios, 2454 - Centro, Juranda - PR, 87355-000
                                </p>
                                <p class="mb-0 footer-text">
                                    <i class="fa-solid fa-phone-alt footer-icon me-2"></i>(44) 3569-1318
                                    <span class="mx-2">|</span>
                                    <i class="fa-solid fa-envelope footer-icon me-2"></i>jrnjoaorosa@escola.pr.gov.br
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Redes Sociais -->
                    <div class="col-lg-4 col-md-12 text-center text-lg-end">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <a href="https://www.instagram.com/colegio_maffei/" target="_blank"
                                    class="social-icon instagram-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Instagram">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://www.facebook.com/profile.php?id=212544995436310" target="_blank"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook"
                                    class="social-icon facebook-icon">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://x.com/maffeirosa" target="_blank" class="social-icon twitter-icon"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter">
                                    <i class="fa-brands fa-x-twitter"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="credito-container py-2 mt-3">
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <p class="credito mb-0">
                                <a href="https://www.instagram.com/luiz_zamzicki/" target="_blank" class="credito-link-wrapper">
                                    <span>🛠 Desenvolvido por </span>
                                    <span>Luiz H. G. Zamzicki</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <?php
    }

    public static function getFooterAreaInterna(): void
    {
        ?>
        <footer class="border-t border-slate-200 px-3 py-1 text-center text-[11px] text-slate-500 shrink-0">
            <p class="mb-0">
                &copy; <?= date('Y') ?> Colégio Estadual João Maffei Rosa
                <span class="mx-1 text-slate-300">|</span>
                <a href="https://www.instagram.com/luiz_zamzicki/" target="_blank"
                    class="hover:text-purple-600 transition-colors duration-200">
                    Desenvolvido por <span class="font-semibold">Luiz H. G. Zamzicki</span>
                </a>
            </p>
        </footer>

        <?php
    }
}

?>