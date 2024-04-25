import jsonData from '../results.json' with {type: "json"};
import testEnv from '../testEnv.json' with {type: "json"};
import {
    showAllTargets, showAverageTimesForCrudOperations,
    showLastBenchmarkDate,
    showNumberOfRecords,
    showResultsFromAllBenchmarks,
    showTestEnvSpecs
} from "./reportUtils.js";

const lastBenchmarkDateDiv = document.getElementById("lastBenchmarkDate");
const benchmarkTargetsOrmDiv = document.getElementById("ormBenchmarksList");
const benchmarkTargetsNoOrmDiv = document.getElementById("noOrmBenchmarksList");
const testEnvironmentDiv = document.getElementById("testEnv");
const numberOfRecordsDiv = document.getElementById("numberOfRecords");

const benchmarkResultsDiv = document.getElementById("results");
const benchmarkCrudAveragesDiv = document.getElementById("benchmarkCrudAverages");


const prepareReport = () => {
    showLastBenchmarkDate(jsonData, lastBenchmarkDateDiv);
    showAllTargets(jsonData, false, benchmarkTargetsOrmDiv);
    showAllTargets(jsonData, true, benchmarkTargetsNoOrmDiv);
    showTestEnvSpecs(testEnv[0], testEnvironmentDiv);
    showNumberOfRecords(jsonData, numberOfRecordsDiv);

    showResultsFromAllBenchmarks(jsonData, benchmarkResultsDiv);
    showAverageTimesForCrudOperations(jsonData, benchmarkCrudAveragesDiv);
};

document.addEventListener("DOMContentLoaded", prepareReport);