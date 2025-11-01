<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Reserva</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.4;
            background-color: #f8f9fa;
            padding: 15px;
            font-size: 14px;
        }

        .comprovante {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 8px;
            font-weight: 300;
        }

        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 25px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 3px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 8px;
            margin-bottom: 8px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 14px;
            color: #333;
            word-break: break-word;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status.reservada {
            background-color: #ffeaa7;
            color: #d63031;
        }

        .status.hospedada {
            background-color: #81ecec;
            color: #00b894;
        }

        .status.finalizada {
            background-color: #fd79a8;
            color: #e84393;
        }

        .status.cancelada {
            background-color: #fab1a0;
            color: #e17055;
        }

        .total-section {
            background: linear-gradient(135deg, #00b894, #00a085);
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin-top: 15px;
        }

        .total-label {
            font-size: 14px;
            margin-bottom: 3px;
        }

        .total-value {
            font-size: 24px;
            font-weight: 700;
        }

        .footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        .footer p {
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .print-button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 15px auto;
            display: block;
            transition: background-color 0.3s;
        }

        .print-button:hover {
            background: #0056b3;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .uuid-display {
            font-family: monospace;
            background: #e9ecef;
            padding: 6px 10px;
            border-radius: 3px;
            font-size: 12px;
            color: #495057;
            word-break: break-all;
        }

        /* Estilos para impressão */
        @media print {
            @page {
                size: A4;
                margin: 0.5cm;
            }

            body {
                background: white;
                padding: 0;
                font-size: 10px;
                line-height: 1.2;
                margin: 0;
            }

            .comprovante {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
                width: 100%;
                margin: 0;
                height: auto;
            }

            .print-button {
                display: none !important;
            }

            .header {
                background: #007bff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 8px;
                page-break-inside: avoid;
            }

            .header h1 {
                font-size: 16px;
                margin-bottom: 3px;
            }

            .header .subtitle {
                font-size: 10px;
            }

            .content {
                padding: 8px;
            }

            .section {
                margin-bottom: 8px;
                page-break-inside: avoid;
            }

            .section-title {
                font-size: 12px;
                margin-bottom: 4px;
                padding-bottom: 1px;
                border-bottom: 1px solid #007bff;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 3px;
                margin-bottom: 4px;
            }

            .info-item {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 4px;
                border-radius: 2px;
                border-left: 2px solid #007bff;
                break-inside: avoid;
            }

            .info-label {
                font-size: 8px;
                margin-bottom: 1px;
                font-weight: 600;
            }

            .info-value {
                font-size: 10px;
                line-height: 1.1;
            }

            .total-section {
                background: #00b894 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 6px;
                margin-top: 6px;
                page-break-inside: avoid;
            }

            .total-label {
                font-size: 10px;
                margin-bottom: 2px;
            }

            .total-value {
                font-size: 16px;
            }

            .footer {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 6px;
                page-break-inside: avoid;
            }

            .footer p {
                font-size: 8px;
                margin-bottom: 1px;
            }

            .uuid-display {
                font-size: 8px;
                padding: 2px 4px;
                line-height: 1.1;
            }

            .status {
                font-size: 8px;
                padding: 2px 4px;
            }

            /* Forçar tudo em uma página */
            .comprovante * {
                page-break-inside: avoid;
            }

            /* Reduzir ainda mais se necessário */
            .section:last-of-type {
                margin-bottom: 4px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 8px;
            }

            .content {
                padding: 15px;
            }

            .header {
                padding: 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .section {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="comprovante">
        <div class="header">
            <h1>Comprovante de Reserva</h1>
            <p class="subtitle"><?php echo htmlspecialchars($empresa['nome']); ?></p>
        </div>

        <div class="content">
            <button class="print-button" onclick="window.print()">🖨️ Imprimir Comprovante</button>

            <!-- Dados da Reserva e Datas -->
            <div class="section">
                <h2 class="section-title">📋 Dados da Reserva</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Código da Reserva</div>
                        <div class="info-value uuid-display"><?php echo htmlspecialchars($reserva['code']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status <?php echo strtolower($reserva['situation']); ?>">
                                <?php echo htmlspecialchars($reserva['situation']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tipo de Reserva</div>
                        <div class="info-value"><?php echo htmlspecialchars($reserva['type']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Número de Hóspedes</div>
                        <div class="info-value"><?php echo htmlspecialchars($reserva['guests'] ?? '1'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Check-in</div>
                        <div class="info-value">
                            <?php
                            $checkin = new DateTime($reserva['checkin']);
                            echo $checkin->format('d/m/Y H:i');
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Check-out</div>
                        <div class="info-value">
                            <?php
                            $checkout = new DateTime($reserva['checkout']);
                            echo $checkout->format('d/m/Y H:i');
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Duração</div>
                        <div class="info-value">
                            <?php
                            $diff = $checkin->diff($checkout);
                            echo $diff->days > 1 ? $diff->days . ' diárias' : '1 diária';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados do Hóspede e Apartamento -->
            <div class="section">
                <h2 class="section-title">👤 Dados do Hóspede e Apartamento</h2>
                <div class="info-grid">
                    <?php if (!empty($reserva['customer'])): ?>
                        <div class="info-item">
                            <div class="info-label">Nome do Hóspede</div>
                            <div class="info-value"><?php echo htmlspecialchars($reserva['customer']['name'] ?? 'Não informado'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">E-mail</div>
                            <div class="info-value"><?php echo htmlspecialchars($reserva['customer']['email'] ?? 'Não informado'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Telefone</div>
                            <div class="info-value"><?php echo htmlspecialchars($reserva['customer']['phone'] ?? 'Não informado'); ?></div>
                        </div>
                        <?php if (!empty($reserva['customer']['doc'])): ?>
                            <div class="info-item">
                                <div class="info-label">Documento</div>
                                <div class="info-value"><?php echo htmlspecialchars($reserva['customer']['doc']); ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($reserva['apartment'])): ?>
                        <div class="info-item">
                            <div class="info-label">Apartamento</div>
                            <div class="info-value"><?php echo htmlspecialchars($reserva['apartment']['name'] ?? 'Não informado'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Categoria</div>
                            <div class="info-value"><?php echo htmlspecialchars($reserva['apartment']['category'] ?? 'Não informado'); ?></div>
                        </div>
                        <?php if (!empty($reserva['apartment']['description'])): ?>
                            <div class="info-item">
                                <div class="info-label">Descrição</div>
                                <div class="info-value"><?php echo htmlspecialchars($reserva['apartment']['description']); ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Observações -->
            <?php if (!empty($reserva['obs'])): ?>
                <div class="section">
                    <h2 class="section-title">📝 Observações</h2>
                    <div class="info-item">
                        <div class="info-value"><?php echo nl2br(htmlspecialchars($reserva['obs'])); ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Valores e Pagamentos -->
            <?php if (!empty($reserva['paid_amount']) || !empty($reserva['consumption_value']) || !empty($reserva['estimated_value'])): ?>
                <div class="section">
                    <h2 class="section-title">💰 Valores e Pagamentos</h2>
                    <div class="info-grid">
                        <?php if (!empty($reserva['estimated_value'])): ?>
                            <div class="info-item">
                                <div class="info-label">Valor Estimado (Diárias)</div>
                                <div class="info-value">R$ <?php echo number_format($reserva['estimated_value'], 2, ',', '.'); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($reserva['consumption_value'])): ?>
                            <div class="info-item">
                                <div class="info-label">Valor de Consumo</div>
                                <div class="info-value">R$ <?php echo number_format($reserva['consumption_value'], 2, ',', '.'); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($reserva['paid_amount'])): ?>
                            <div class="info-item">
                                <div class="info-label">Valor Pago</div>
                                <div class="info-value">R$ <?php echo number_format($reserva['paid_amount'], 2, ',', '.'); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php
                        $total = ($reserva['total_reservation_value'] ?? $reserva['amount'] ?? 0);
                        $consumo = $reserva['consumption_value'] ?? 0;
                        $pago = $reserva['paid_amount'] ?? 0;
                        $saldo = ($total + $consumo) - $pago;
                        if ($saldo != 0):
                        ?>
                            <div class="info-item">
                                <div class="info-label"><?php echo $saldo > 0 ? 'Saldo Devedor' : 'Crédito Cliente'; ?></div>
                                <div class="info-value" style="<?php echo $saldo > 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #27ae60; font-weight: bold;'; ?>">
                                    R$ <?php echo number_format(abs($saldo), 2, ',', '.'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Total -->
            <div class="total-section">
                <div class="total-label">Valor Total da Reserva</div>
                <div class="total-value">R$ <?php echo number_format($reserva['total_reservation_value'] ?? $reserva['amount'] ?? 0, 2, ',', '.'); ?></div>
            </div>
        </div>

        <div class="footer">
            <?php if (!empty($empresa['endereco'])): ?>
                <p><strong>Endereço:</strong> <?php echo htmlspecialchars($empresa['endereco']); ?></p>
            <?php endif; ?>

            <?php if (!empty($empresa['telefone'])): ?>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($empresa['telefone']); ?></p>
            <?php endif; ?>

            <?php if (!empty($empresa['email'])): ?>
                <p><strong>E-mail:</strong> <?php echo htmlspecialchars($empresa['email']); ?></p>
            <?php endif; ?>

            <p><small>Comprovante gerado em: <?php echo date('d/m/Y H:i:s'); ?></small></p>
        </div>
    </div>

    <script>
        // Função para imprimir automaticamente se necessário
        function printIfRequested() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === 'true') {
                window.print();
            }
        }

        // Executar após o carregamento completo da página
        window.addEventListener('load', printIfRequested);

        // Melhorar a experiência de impressão
        window.addEventListener('beforeprint', function() {
            document.title = 'Comprovante de Reserva - <?php echo htmlspecialchars($reserva['id']); ?>';
        });

        window.addEventListener('afterprint', function() {
            document.title = 'Comprovante de Reserva';
        });
    </script>
</body>

</html>