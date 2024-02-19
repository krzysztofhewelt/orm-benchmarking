const {Op} = require('sequelize');
const {User, Student, Course, Task, Teacher} = require("./Entities");
const {database, lastBenchmarkQueries} = require("./database");

// benchmark results var and constant
const NUMBER_OF_REPEATS = 100;
let benchmarks = [];

// test-cases functions
const test1 = async () => {
    return await User.findAll({
        include: [Course],
        where: {
            id: {
                [Op.eq]: 315
            }
        }
    });
};

// run function
const run = async (benchmark, times = NUMBER_OF_REPEATS) => {
    let tempTimes = [];

    for (let i = 0; i < times; i++) {
        let start = performance.now();
        await benchmark();
        let stop = performance.now();
        tempTimes.push(stop - start);
    }

    const avgTime = +(tempTimes.reduce((sum, el) => sum + el, 0) / times).toFixed(2);
    addBenchmark(benchmark.name, avgTime, lastBenchmarkQueries);

    console.log("AVG time of benchmark " + benchmark.name + ": " + avgTime + " ms.");
};

const addBenchmark = (benchmark, avgTime, queries) => {
    benchmarks.push({
        name: benchmark,
        time: avgTime,
        queries: queries
    });
};

// send request to php
const sendSaveResults = async () => {
    await fetch("http://localhost/orm_benchmarking/index.php?save-results", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            orm_name: "Sequelize",
            orm_version: "6.32.1",
            benchmarks: benchmarks
        })
    }).then((response) => {
        if (!response.ok)
            throw "An error occurred during sending request: " + response.statusText;
    })
};

// run benchmarks
console.log("Performing benchmark tests. Please wait...");

Promise.all([
    run(test1)
]).then(() => {
    sendSaveResults()
        .then(() => {
            console.log("Results has been saved successfully.");

            database.close().then(() => { process.exit(); });
        })
        .catch((error) => {
            console.log(error);
        });
});