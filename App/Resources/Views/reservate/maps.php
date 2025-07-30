<?php require_once __DIR__ . '/../layout/top.php'; ?>
<!-- Row start -->
<div class="row gx-3">
    <style>
      table.fc-scrollgrid-sync-table {
        width: 95% !important;
        height: 70vh !important;
      }

      table.fc-col-header {
          width: 95% !important;
      }
      div#calendar {
          height: 81vh;
          position: relative;
          z-index: 0;
      }
      .fc-daygrid-body.fc-daygrid-body-unbalanced {
        width: 100% !important;
      }
      @media screen and (max-width: 790px) {
        .fc .fc-toolbar-title {
            font-size: 17px;
            margin: 0px;
        }
      }
      .fc-h-event .fc-event-title-container {
            text-align: center !important;
        }
    </style>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                eventSources: [], // Inicializa como um array vazio
                datesSet: function(info) {
                    var start = info.start;
                    var end = info.end;

                    // Converter as datas para o formato esperado pela API
                    var startDate = start.toISOString().split('T')[0];
                    var endDate = end.toISOString().split('T')[0];

                    // Fazer a requisição à API e receber os eventos
                    fazerRequisicaoEventos(startDate, endDate, function(eventos) {
                        console.log(eventos); // Para depuração, exibe os eventos recebidos
                        // Limpar os eventos existentes
                        calendar.getEventSources().forEach(function(eventSource) {
                            eventSource.remove(); // Remove eventos existentes
                        });

                        // Adicionar os novos eventos ao calendário
                        if (eventos.length > 0) {
                            calendar.addEventSource(eventos); // Adiciona eventos ao calendário
                        } 
                    });
                },
                dateClick: function(info) {
                    // Redirecionar para uma nova página quando um dia é clicado
                    var selectedDate = info.dateStr; // A data clicada
                    window.location.href = `/reserva/criar?dt_reserva=${selectedDate}`; // Ajuste a URL conforme necessário
                }
            });

            calendar.render();
        });

        function fazerRequisicaoEventos(startDate, endDate, callback) {
            $.ajax({
                url: '/maps/', // URL da sua rota
                type: 'POST', // Método HTTP
                dataType: 'json', // Tipo de resposta esperada
                data: { start: startDate, end: endDate }, // Envia como JSON
                success: function(data) {
                    if (Array.isArray(data)) { // Verifica se os dados retornados são um array
                        callback(data); // Chama o callback com os dados
                    } 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Erro na requisição:', textStatus, errorThrown);
                    alert('Erro ao carregar eventos. Tente novamente mais tarde.');
                }
            });
        }

    </script>
    <div id='calendar'></div>
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
