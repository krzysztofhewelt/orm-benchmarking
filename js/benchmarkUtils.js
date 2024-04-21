const { promisify } = require('util');
const exec = promisify(require('child_process').exec);

const backupDatabase = async() => {
    await exec('php ../../databaseBackup.php');
}

const restoreDatabase = async() => {
    await exec('php ../../databaseRestore.php');
}

const sendSaveResults = async (ormName, ormVersion, benchmarks) => {
    await fetch("http://localhost/orm_benchmarking/index.php?save-results", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            orm_name: ormName,
            orm_version: ormVersion,
            benchmarks: benchmarks
        })
    }).then((response) => {
        if (!response.ok)
            throw "An error occurred during sending request: " + response.statusText;
    })
};

const getMethodArgumentForMethod = (type, quantity, data = []) => {
    if(type === 'select' || type === 'update' || type === 'delete')
        return quantity;

    if(type === 'insert')
        return data.slice(0, quantity);

    return '';
};

module.exports.backupDatabase = backupDatabase;
module.exports.restoreDatabase = restoreDatabase;
module.exports.sendSaveResults = sendSaveResults;
module.exports.getMethodArgumentForMethod = getMethodArgumentForMethod;
