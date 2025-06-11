<section class="contact-section my-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Entre em Contato Conosco!</h2>
        <p class="text-center mb-5">
            Tem dúvidas sobre o Ensino Integral, matrículas ou quer saber mais sobre nossa escola?
            Preencha o formulário abaixo e retornaremos em breve!
        </p>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card-padrao p-4 p-md-5">
                    <div id="form-messages" class="mb-3" style="display: none;"></div>

                    <form action="paginas/processa_contato.php" method="POST" id="contactForm">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="floating-label-group">
                                    <input type="text" class="form-control" id="nome" name="nome" placeholder=" "
                                        required />
                                    <label for="nome">Seu Nome Completo</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="floating-label-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder=" "
                                        required />
                                    <label for="email">Seu Melhor E-mail</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="floating-label-group">
                                    <input type="tel" class="form-control" id="telefone" name="telefone" placeholder=" "
                                        maxlength="15" required />
                                    <label for="telefone">Telefone (com DDD)</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="floating-label-group">
                                    <input type="text" class="form-control" id="assunto" name="assunto" placeholder=" "
                                        required />
                                    <label for="assunto">Assunto</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="floating-label-group">
                                <select class="form-select" id="turmaInteresse" name="turmaInteresse" placeholder=" "
                                    required>
                                    <option value="" disabled selected></option>
                                    <optgroup label="Ensino Fundamental II">
                                        <option value="6_ano_fund">6º Ano</option>
                                        <option value="7_ano_fund">7º Ano</option>
                                        <option value="8_ano_fund">8º Ano</option>
                                        <option value="9_ano_fund">9º Ano</option>
                                    </optgroup>
                                    <optgroup label="Ensino Médio Diurno">
                                        <option value="1_ano_medio">1º Ano</option>
                                        <option value="2_ano_medio">2º Ano</option>
                                        <option value="3_ano_medio">3º Ano</option>
                                    </optgroup>
                                    <optgroup label="Ensino Médio Noturno">
                                        <option value="1_ano_medio_noturno">1º Ano (Noturno)</option>
                                        <option value="2_ano_medio_noturno">2º Ano (Noturno)</option>
                                        <option value="3_ano_medio_noturno">3º Ano (Noturno)</option>
                                    </optgroup>
                                    <option value="indiferente_turma">Não tenho certeza / Outro</option>
                                </select>
                                <label for="turmaInteresse">Turma de Interesse</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="floating-label-group">
                                <textarea class="form-control" id="mensagem" name="mensagem" rows="3" placeholder=" "
                                    required></textarea>
                                <label for="mensagem">Sua Mensagem</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-azul btn-lg">Enviar Mensagem</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>