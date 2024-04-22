import {extractChartData} from './charts.js';
import jsonData from './results.json' with { type: "json" };

async function drawCharts() {
    console.log(jsonData);
    const simpleUsersData = extractChartData(jsonData, "selectSimpleUsers");
    const complexStudentsData = extractChartData(jsonData, "selectComplexUsersTasks");

    // Draw charts
    const simpleUsersCtx = document.getElementById("simpleUsersChart").getContext("2d");
    new Chart(simpleUsersCtx, {
        type: "line",
        data: simpleUsersData,
        options: {
            radius: 5,
            hoverRadius: 7,
            hitRadius: 10
        }
    });

    const complexStudentsCtx = document.getElementById("selectComplexUsersTasks").getContext("2d");
    new Chart(complexStudentsCtx, {
        type: "line",
        data: complexStudentsData,
        options: {
            // Chart options
        }
    });
}

// Call drawCharts function when the DOM content is loaded
document.addEventListener("DOMContentLoaded", drawCharts);