export function getTimesForBenchmarksAndTargets(jsonData, targetNames, benchmarkName) {
    const data = {
        labels: [],
        datasets: []
    };

    // Iterate through targets
    targetNames.forEach(ormName => {
        const dataset = {
            label: ormName,
            data: [],
        };

        // Iterate through each benchmark for the current target
        jsonData.forEach(item => {
            if (item.orm_name === ormName) {
                item.benchmarks.forEach(b => {
                    if (b.name === benchmarkName) {
                        Object.keys(b.numberOfRecords).forEach(record => {
                            if (!data.labels.includes(record)) {
                                data.labels.push(record);
                            }
                            const avgTime = b.numberOfRecords[record].avgTime;
                            dataset.data.push(avgTime);
                        });
                    }
                });
            }
        });

        data.datasets.push(dataset);
    });

    return data;
}


export function calculateAverageTimesForBenchmarks(jsonData, targetNames, benchmarkNames) {
    const benchmarkAverages = {};

    // Iterate over each ORM_NAME and benchmark
    jsonData.forEach(item => {
        if (targetNames.has(item.orm_name)) {
            item.benchmarks.forEach(benchmark => {
                if (benchmarkNames.includes(benchmark.name)) {
                    Object.keys(benchmark.numberOfRecords).forEach(record => {
                        if (!benchmarkAverages[record]) {
                            benchmarkAverages[record] = {};
                        }
                        if (!benchmarkAverages[record][item.orm_name]) {
                            benchmarkAverages[record][item.orm_name] = [];
                        }
                        benchmarkAverages[record][item.orm_name].push(benchmark.numberOfRecords[record].avgTime);
                    });
                }
            });
        }
    });

    // Calculate the average of avgTime values for each record
    const result = {
        labels: Object.keys(benchmarkAverages),
        datasets: []
    };

    for (const ormName in benchmarkAverages["1"]) {
        const ormData = {
            label: ormName,
            data: result.labels.map(record => {
                const avgTimeSum = benchmarkAverages[record][ormName].reduce((sum, value) => sum + value, 0);
                return avgTimeSum / benchmarkAverages[record][ormName].length;
            })
        };
        result.datasets.push(ormData);
    }

    return result;
}


export function getQueriesForBenchmarkAndTargets(jsonData, benchmarkName, ormNames) {
    const queries = [];

    // Iterate over each ORM_NAME
    ormNames.forEach(ormName => {
        // Find the benchmark object by name and ORM_NAME
        const benchmark = jsonData.find(item => item.orm_name === ormName && item.benchmarks.some(b => b.name === benchmarkName));

        if (benchmark) {
            const benchmarkData = benchmark.benchmarks.find(b => b.name === benchmarkName);
            const queriesData = benchmarkData.numberOfRecords["1000"];
            queries.push({ormName, numberOfQueries: queriesData.numberOfQueries, queries: queriesData.queries});
        }
    });

    return queries;
}