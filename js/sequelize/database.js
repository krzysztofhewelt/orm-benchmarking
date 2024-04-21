const {Sequelize} = require("sequelize");
const dbCredentials = require('../../dbCredentials.json');

let lastBenchmarkQueries = [];
let driver = null;
if(dbCredentials.driver === 'mysql')
    driver = 'mariadb'

const getQueries = (queries) => {
    const cleanedQuery = queries.replace(/^Executing \(.*?\): /, ''); // remove 'Executing' part and ID from the query
    lastBenchmarkQueries.push(cleanedQuery);
};

const clearQueriesHistory = () => {
    lastBenchmarkQueries = [];
};

const getQueriesHistory = () => {
    return lastBenchmarkQueries.slice(0, 10);
};

const countQueriesHistory = () => {
    return lastBenchmarkQueries.length;
};

module.exports.database = new Sequelize(dbCredentials.database, dbCredentials.username, dbCredentials.password, {
    dialect: driver,
    host: dbCredentials.host,
    port: dbCredentials.port,
    logging: getQueries
});

module.exports.getBenchmarkQueries = getQueriesHistory;
module.exports.countBenchmarkQueries = countQueriesHistory;
module.exports.clearBenchmarkQueries = clearQueriesHistory;
