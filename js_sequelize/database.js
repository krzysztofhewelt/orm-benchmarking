const {Sequelize} = require("sequelize");

const lastBenchmarkQueries = [];

const getQueries = (queries) => {
    lastBenchmarkQueries[0] = queries.replace('Executing (default): ', ''); // in my benchmark tests, Sequelize always generates one query.
};

module.exports.database = new Sequelize('orm_benchmarking', 'postgres', 'superpassword', {
    schema: "public",
    dialect: "postgres",
    host: "localhost",
    port: 5432,
    logging: getQueries
});

module.exports.lastBenchmarkQueries = lastBenchmarkQueries;