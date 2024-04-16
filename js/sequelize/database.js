const {Sequelize} = require("sequelize");

const lastBenchmarkQueries = [];
const dbCredentials = require('../../dbCredentials.json');
let driver = null;
if(dbCredentials.driver === 'mysql')
    driver = 'mariadb'

const getQueries = (queries) => {
    // console.log(queries);
    // lastBenchmarkQueries[0] = queries.replace('Executing (default): ', ''); // in my benchmark tests, sequelize always generates one query.
};

module.exports.database = new Sequelize(dbCredentials.database, dbCredentials.username, dbCredentials.password, {
    dialect: driver,
    host: dbCredentials.host,
    port: dbCredentials.port,
    logging: getQueries
});

module.exports.lastBenchmarkQueries = lastBenchmarkQueries;