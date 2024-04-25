const chartOptions = {
    yScaleTitle: 'Liczba rekordów',
    xScaleTitle: 'Średni czas wykonywania [ms]'
}

export function drawChart(data, chartTitle, element) {
    new Chart(element, {
        type: "line",
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
                }
            }
        }
    });
}