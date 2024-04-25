import {drawChart} from "./chartUtils.js";
import {
    calculateAverageTimesForBenchmarks,
    getQueriesForBenchmarkAndTargets,
    getTimesForBenchmarksAndTargets
} from "./chartUtils.data.js";

export function showAllTargets(jsonData, noOrms, element) {
    let ul = document.createElement('ul');
    let li;

    element.appendChild(ul);

    const benchmarkNames = getTargetsNamesAndLanguages(jsonData, noOrms);
    benchmarkNames.forEach(function (item) {
        li = document.createElement('li');
        li.appendChild(document.createTextNode(item));
        ul.appendChild(li);
    });
}

export function showTestEnvSpecs(specs, element) {
    const text = document.createTextNode(`${specs.os}, ${specs.cpu}, ${specs.ram}, ${specs.hdd}`);
    element.appendChild(text);
}

export function showLastBenchmarkDate(jsonData, element) {
    const text = document.createTextNode(jsonData.at(-1).benchmark_date);
    element.appendChild(text);
}

export function showNumberOfRecords(jsonData, element) {
    const numberOfRecords = getNumberOfRecords(jsonData);
    const text = document.createTextNode(numberOfRecords.join(', '));
    element.appendChild(text);
}

export function drawBanner(name) {
    let banner = document.createElement("div");
    banner.className = 'benchmark-banner';

    let bannerHeader = document.createElement("div");
    bannerHeader.className = 'benchmark-banner__header';
    let benchmarkName = document.createTextNode(name);
    bannerHeader.appendChild(benchmarkName)

    banner.appendChild(bannerHeader);

    return banner;
}

export function showResultsFromAllBenchmarks(jsonData, resultsDiv) {
    const benchmarkNamesAndDescriptions = getBenchmarkNames(jsonData);

    benchmarkNamesAndDescriptions.forEach(el => {
        let benchmarkDiv = document.createElement("div")

        // banner
        let drawBenchmarkBanner = drawBanner(el);
        benchmarkDiv.appendChild(drawBenchmarkBanner);

        // charts
        let ormChartContainer = document.createElement("div");
        ormChartContainer.className = 'chart';

        let ormChart = document.createElement("canvas");
        let ormNames = getTargetsNames(jsonData, false);
        let resultsForBenchmarkName = getTimesForBenchmarksAndTargets(jsonData, ormNames, el);
        drawChart(resultsForBenchmarkName, el, ormChart);


        let nonOrmChart = document.createElement("canvas");
        let nonOrmNames = getTargetsNames(jsonData, true);
        let resultsForBenchmarkNameNonOrm = getTimesForBenchmarksAndTargets(jsonData, nonOrmNames, el);
        drawChart(resultsForBenchmarkNameNonOrm, el, nonOrmChart);

        ormChartContainer.appendChild(ormChart)
        ormChartContainer.appendChild(nonOrmChart);
        benchmarkDiv.appendChild(ormChartContainer);

        let queriesDiv = document.createElement("div");
        let queries = getQueriesForBenchmarkAndTargets(jsonData, el, ormNames);
        const formatter = new JSONFormatter(queries);

        let queriesHeader = document.createElement("h3");
        let queriesHeaderText = document.createTextNode("Generated queries by ORMs (1000 records)");
        queriesHeader.appendChild(queriesHeaderText);

        queriesDiv.appendChild(queriesHeader)

        queriesDiv.appendChild(formatter.render());
        formatter.openAtDepth(2);

        benchmarkDiv.appendChild(queriesDiv);

        resultsDiv.appendChild(benchmarkDiv);
    });
}

export function showAverageTimesForCrudOperations(jsonData, element) {
    const crudOperations = [
        {
            operation: 'select',
            benchmarkNames: ['Select n first users', 'Select first n students and their courses, order by surname', 'Select tasks to do for n first students']
        },
        {
            operation: 'insert',
            benchmarkNames: ['Insert n users with additional information using transaction', 'Insert n courses']
        },
        {
            operation: 'update',
            benchmarkNames: ['Prolong available to date for n courses']
        },
        {
            operation: 'delete',
            benchmarkNames: ['Remove n first users from their courses', 'Delete n courses']
        },
    ];

    let banner= drawBanner('Average CRUD operations');
    element.appendChild(banner);

    const ormNames = getTargetsNames(jsonData, false);

    let ormChartContainer = document.createElement("div");
    ormChartContainer.className = 'chart';

    crudOperations.forEach(operation => {
        const averageTimes = calculateAverageTimesForBenchmarks(jsonData, ormNames, operation.benchmarkNames);
        let averageTimeChart = document.createElement("canvas");
        drawChart(averageTimes, `Average time of ${operation.operation} operations`, averageTimeChart);

        ormChartContainer.appendChild(averageTimeChart);
    })

    element.appendChild(ormChartContainer);
}

export function getTargetsNames(jsonData, nonOrms) {
    const benchmarksFiltered = filterTargets(jsonData, nonOrms);
    return new Set(benchmarksFiltered.map(item => item.orm_name));
}

export function getTargetsNamesAndLanguages(jsonData, nonOrms) {
    const benchmarksFiltered = filterTargets(jsonData, nonOrms);
    return new Set(benchmarksFiltered.map(item => `${item.orm_name} (${item.orm_language}) v.${item.orm_version}`));
}

export function filterTargets(jsonData, nonOrms) {
    return (nonOrms) ?
        jsonData.filter(item => item.orm_name.includes("NO-ORM"))
        : jsonData.filter(item => !item.orm_name.includes("NO-ORM"));
}

export function getBenchmarkNames(jsonData) {
    return new Set(jsonData.flatMap(item =>
        item.benchmarks.map(benchmark => benchmark.name))
    );
}

export function getNumberOfRecords(jsonData) {
    return Array.from(new Set(jsonData.flatMap(item =>
        item.benchmarks.flatMap(benchmark => Object.keys(benchmark.numberOfRecords))
    )));
}