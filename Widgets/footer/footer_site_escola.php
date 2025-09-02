<?php

function getFooterSiteEscola(): void
{
    ?>
    <footer class="footer mt-auto py-3">
        <div class="container">
            <div class="row align-items-center">
                <div
                    class="col-lg-3 col-md-3 text-center text-lg-start mb-3 mb-lg-0 d-flex justify-content-center align-items-center">
                    <a href="home"><img src="imagens/logo.png" alt="Maffei" class="logo img-fluid"></a>
                </div>
                <div
                    class="col-lg-3 col-md-3 text-center text-md-start mb-3 mb-md-0 d-flex flex-column align-items-center align-items-md-center">

                    <div class="enderecoInfos">
                        <i class="fa-solid fa-location-dot footer-icon me-2">
                        </i>
                        <address class="endereco mb-0 footer-text">
                            <p
                                class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                                R. Tamôios, 2454 - Centro</p>
                            <p
                                class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                                Juranda - PR, 87355-000</p>
                        </address>
                    </div>
                </div>


                <div
                    class="col-lg-3 col-md-3 text-center text-md-start mb-3 mb-md-0 d-flex flex-column align-items-center align-items-md-center">
                    <div class="contatoInfos">
                        <p
                            class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                            <i class="fa-solid fa-phone-alt footer-icon me-2"></i>
                            (44) 3569-1318
                        </p>
                        <p
                            class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                            <i class="fa-solid fa-envelope footer-icon me-2"></i>
                            jrnjoaorosa@escola.pr.gov.br
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-2 d-flex justify-content-center justify-content-md-end">
                    <div class="redes-card mb-0">
                        <div class="card-body">
                            <h5 class="card-title">Nossas Redes</h5>
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
} ?>