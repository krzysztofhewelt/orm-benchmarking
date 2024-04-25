const {fetchConn, closeConn} = require('./database');
const {sendSaveResults, getMethodArgumentForMethod, restoreDatabase, calculateMean, calculateStandardDeviation} = require("../benchmarkUtils");

// Benchmark functions
const selectSimpleUsers = async (dbConnection, quantity) => {
    return await dbConnection.query("SELECT * FROM users LIMIT ?", [quantity]);
};

const selectComplexStudentsWithInformationAndCourses = async (dbConnection, quantity) => {
    return await dbConnection.query("SELECT * FROM (SELECT * FROM users WHERE account_role = 'student' ORDER BY surname LIMIT ?) as us INNER JOIN student_info ON us.id = student_info.user_id INNER JOIN orm_benchmarking.course_enrollments ce on us.id = ce.user_id INNER JOIN orm_benchmarking.courses c on ce.course_id = c.id", [quantity]);
};

const selectComplexUsersTasks = async (dbConnection, quantity) => {
    return await dbConnection.query("SELECT * FROM tasks INNER JOIN orm_benchmarking.courses c on tasks.course_id = c.id INNER JOIN orm_benchmarking.course_enrollments ce on c.id = ce.course_id INNER JOIN (SELECT * FROM users LIMIT ?) u on ce.user_id = u.id", [quantity]);
};

const insertUsers = async (dbConnection, users) => {
    const promises = users.map(userData => {
        return dbConnection.query("INSERT INTO users (name, surname, email, password, account_role, active) VALUES (?, ?, ?, ?, ?, ?)", [userData.name, userData.surname, userData.email, userData.password, userData.account_role, userData.active]);
    });
    return await Promise.all(promises);
};

const insertCourses = async (dbConnection, courses) => {
    const promises = courses.map(courseData => {
        return dbConnection.query("INSERT INTO courses (name, description, available_from, available_to) VALUES (?, ?, ?, ?)", [courseData.name, courseData.description, courseData.available_from, courseData.available_to]);
    });
    return await Promise.all(promises);
};

const updateCoursesEndDate = async (dbConnection, quantity) => {
    return await dbConnection.query("UPDATE courses SET available_to = '2024-10-01' LIMIT ?", [quantity]);
};

const detachUsersFromCourses = async (dbConnection, quantityUsers) => {
    const users = await dbConnection.query("SELECT * FROM users LIMIT ?", [quantityUsers]);

    const promises = users.map(user => {
        return dbConnection.query("DELETE FROM course_enrollments WHERE user_id = ?", [user.id]);
    });
    return await Promise.all(promises);
};

const deleteCourses = async (dbConnection, quantity) => {
    return await dbConnection.query("DELETE FROM courses LIMIT ?", [quantity]);
};


// Benchmark parameters
const NUMBER_OF_REPEATS = 100;
const NUMBER_OF_RECORDS = [1, 50, 100, 500, 1000];


/**
 * Run benchmarks for given functions.
 * @returns {Promise<void>} - A promise indicating the completion of benchmarking.
 */
const runBenchmarks = async () => {
    const benchmarkResults = [];
    const benchmarksToRun = [
        { benchmark: selectSimpleUsers, type: 'select', name: 'Select n first users' },
        { benchmark: selectComplexStudentsWithInformationAndCourses, type: 'select', name: 'Select first n students and their courses, order by surname' },
        { benchmark: selectComplexUsersTasks, type: 'select', name: 'Select tasks to do for n first students' },

        { benchmark: insertUsers, type: 'insert', table: 'users', name: 'Insert n users with additional information using transaction' },
        { benchmark: insertCourses, type: 'insert', table: 'courses', name: 'Insert n courses' },

        { benchmark: updateCoursesEndDate, type: 'update', name: 'Prolong available to date for n courses' },

        { benchmark: detachUsersFromCourses, type: 'delete', name: 'Remove n first users from their courses' },
        { benchmark: deleteCourses, type: 'delete', name: 'Delete n courses' },
    ];

    const courses = require('../../courses.json');
    const users = require('../../users.json');

    let dbConnection;
    try {
        dbConnection = await fetchConn();

        for (const {benchmark, name, type, table = ''} of benchmarksToRun) {
            console.log(`Benchmarking ${benchmark.name}`);
            const results = {};
            for (let i = 0; i < NUMBER_OF_RECORDS.length; i++) {
                const tempTimes = [];
                const recordsToFetch = NUMBER_OF_RECORDS[i];

                let dataToAdd = [];
                if (table === 'users')
                    dataToAdd = users;
                else if (table === 'courses')
                    dataToAdd = courses;

                const methodArgument = getMethodArgumentForMethod(type, recordsToFetch, dataToAdd);

                for (let j = 0; j < NUMBER_OF_REPEATS; j++) {
                    const start = performance.now();
                    await benchmark(dbConnection, methodArgument);
                    const stop = performance.now();
                    tempTimes.push(stop - start);

                    if(type !== 'select')
                        await restoreDatabase();
                }

                const avgTime = calculateMean(tempTimes);
                const stdTime = calculateStandardDeviation(tempTimes);

                results[recordsToFetch] = {"avgTime": avgTime, "stdTime": stdTime, "numberOfQueries": 0, "queries": []};
                console.log(` - ${NUMBER_OF_RECORDS[i]}: avg=${avgTime}, std=${stdTime}`);
            }
            benchmarkResults.push({name, "numberOfRecords": results});
        }
    } catch (err) {
        console.log(err);
    } finally {
        if (dbConnection) await closeConn(dbConnection);
    }

    return benchmarkResults;
};

// Run benchmarks and save results
console.log("Performing benchmark tests. Please wait...");

runBenchmarks().then(benchmarkResults => {
    const nodeJsVersion = "NodeJS " + process.versions.node.split('.')[0];
    sendSaveResults("JavaScript NO-ORM", nodeJsVersion, benchmarkResults).then(() => {
        console.log("Results have been saved successfully.");
        process.exit();
    }).catch((error) => {
        console.log(error);
    });
});
