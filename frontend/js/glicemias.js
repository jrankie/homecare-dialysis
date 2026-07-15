(function () {
    let myGlicemiaChart = null;

    document.getElementById('btnGraficarGlicemia').addEventListener('click', function() {
        let inicio = document.getElementById('fecha_inicio').value;
        let fin = document.getElementById('fecha_fin').value;

        window.fetch('../../backend/php/datosGlicemia.php?inicio=' + inicio + '&fin=' + fin, {
            method: 'get'
        })
        .then((response) => response.json())
        .then((json) => {
            if (json.length === 0) {
                alert('No se encontraron registros de glicemia en el rango seleccionado.');
                return;
            }

            let ctx = document.getElementById('glicemiaChart').getContext('2d');

            if (myGlicemiaChart) {
                myGlicemiaChart.destroy();
            }

            let labels = json.map((el) => {
                let fecha = new Date(el.created_at);
                return fecha.toLocaleDateString() + ' (' + el.momento + ')';
            });
            let valores = json.map((el) => parseFloat(el.valor_glucosa));

            myGlicemiaChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Glucosa (mg/dL)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        data: valores,
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                autoSkip: false
                            }
                        }]
                    }
                }
            });
        })
        .catch((err) => console.log(err));
    });
}());
