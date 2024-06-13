const chartOptions = {
    yScaleTitle: 'Average time executing [ms]',
    xScaleTitle: 'Number of records'
}

export function drawChart(data, chartTitle, element) {
    Chart.register(ChartDataLabels);

    new Chart(element, {
        type: "bar",
        data: data,
        options: {
            interaction: {
                mode: 'index',
                intersect: false,
            },
            radius: 5,
            hoverRadius: 7,
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: chartOptions.xScaleTitle,
                        color: 'black',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: chartOptions.yScaleTitle,
                        color: 'black',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: chartTitle,
                    color: 'black',
                    font: {
                        size: 24,
                        weight: 'bold'
                    }
                },
                datalabels: {
                    anchor: 'end', // remove this line to get label in middle of the bar
                    align: 'end',
                    formatter: (val) => Number(val.toFixed(2)).toLocaleString('pl-PL'),
                    labels: {
                        value: {
                            color: 'black'
                        }
                    }

                }
            }
        }
    });
}