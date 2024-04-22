export function extractChartData(jsonData, benchmarkName) {
    const data = {
        labels: [],
        datasets: []
    };

    // Find the benchmark object by name
    const benchmark = jsonData.find(item => item.benchmarks.some(b => b.name === benchmarkName));

    if (!benchmark) {
        console.error(`Benchmark '${benchmarkName}' not found.`);
        return null;
    }

    // Extract unique ORM names from the entire JSON data
    const ormNames = new Set(jsonData.filter(item => !item.orm_name.includes("no-orm")).map(item => item.orm_name));

    // Iterate through each ORM_NAME
    ormNames.forEach(ormName => {
        const dataset = {
            label: ormName,
            data: [],
        };

        // Iterate through each benchmark for the current ORM_NAME
        jsonData.forEach(item => {
            if (item.orm_name === ormName) {
                item.benchmarks.forEach(b => {
                    if (b.name === benchmarkName) {
                        Object.keys(b.numberOfRecords).forEach(record => {
                            if (!data.labels.includes(record)) {
                                data.labels.push(record);
                            }
                            dataset.data.push(b.numberOfRecords[record].time);
                        });
                    }
                });
            }
        });

        data.datasets.push(dataset);
    });

    return data;
}

// Example usage:
// const jsonData = require("./results.json")/* Your JSON data */;
// const simpleUsersData = extractChartData(jsonData, "SelectSimpleUsers");
// const complexStudentsData = extractChartData(jsonData, "SelectComplexStudentsWithInformationAndCourses");
//
// console.log("Simple Users Data:", simpleUsersData.datasets);
// console.log("Complex Students Data:", complexStudentsData.datasets);
//
// module.exports.simpleUsersData = simpleUsersData;
// module.exports.complexStudentsData = complexStudentsData;
// module.exports.extractChartData = extractChartData;
