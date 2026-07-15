(function () {

    window.fetch('../../backend/php/datos.php', {
        method: 'get'
    })
        .then((response) => response.json())
        .then((json) => renderChart(json))
        .catch((err) => console.log(err))

    function renderChart (dataset) {
        let ctx = document.getElementById('RecambiosChart')
        let barChart = new Chart(ctx, {
            type: 'bar',
            data: {

                labels: dataset.map((el) => 'Recambio ' + el.recambio_num),

                datasets: [
                    {
                        label: 'infusion (ml)',
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        data: dataset.map((el) => el.infusion)
                    },
                    {
                        label: 'drenaje (ml)',
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        data: dataset.map((el) => el.drenaje)
                    },
                    {
                        label: 'balance (ml)',
                        backgroundColor: 'rgba(153, 102, 255, 0.8)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        data: dataset.map((el) => el.balance)
                    }
                ]
            },
            options: {
                responsive: true,
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
        })
    }
}())