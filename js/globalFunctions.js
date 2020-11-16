const createChart = (id, XLabels, dataset, title, legendLabel,xtitle, ytitle) => {

    let myChart = new Chart(id, {
        type: 'bar',
        data: {
            labels: XLabels,
            datasets: [{
                label: legendLabel,
                data: dataset,
                backgroundColor:'rgba(131, 195, 169, 0.3)',
                borderColor:'rgba(131, 195, 169, 1)',
                borderWidth: 2
            }]
        },
        options: {
            title: {
                display: true,
                fontSize: 24,
                fontColor: ' #f1471d',
                fontFamily: 'Barlow, sans-serif',
                text: title
            },
            legend: {
                display:true,
                onClick: (e) => e.stopPropagation,
                labels: {
                    fontSize: 18,
                    fontFamily: 'Barlow, sans-serif'
                }

            },
            scales: {
                display:true,
                labelString: "Cantidad",
                xAxes: [{
                    display:true,
                    scaleLabel: {
                        display:true,
                        labelString: xtitle,
                        fontFamily: 'Barlow, sans-serif',
                        fontStyle:'bold',
                        fontSize: 16
                    }
                }],
                yAxes: [{
                    display:true,
                    scaleLabel: {
                        display:true,
                        labelString: ytitle,
                        fontFamily: 'Barlow, sans-serif',
                        fontStyle:'bold',
                        fontSize: 16
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    return myChart;
}


const updateChart = (chart, XLabels, dataset) => {
    chart.data.labels = XLabels;
    chart.data.datasets[0].data = dataset;
    chart.update();
}