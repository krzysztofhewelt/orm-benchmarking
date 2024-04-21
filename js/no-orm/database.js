const mariadb = require('mariadb');
const dbCredentials = require('../../dbCredentials.json');

const pool = mariadb.createPool({
    host: dbCredentials.host,
    port: dbCredentials.port,
    user: dbCredentials.username,
    password: dbCredentials.password,
    database: dbCredentials.database,
    checkDuplicate: false,
    connectionLimit: 100,
});

const fetchConn = async() => {
    return pool.getConnection();
};

const closeConn = async(conn) => {
    return await conn.end();
};

module.exports.fetchConn = fetchConn;
module.exports.closeConn = closeConn;
