// const {Op, Sequelize} = require('sequelize');
// const {User, Student, Course, Task, Teacher} = require("./Entities");
// const {database, lastBenchmarkQueries} = require("./database");
//
// // benchmark results var and constant
// const NUMBER_OF_REPEATS = 100;
// const NUMBER_OF_RECORDS = [1, 50, 100, 500, 1000];
// let benchmarks = [];
//
// /**
//  * ======================
//  *     SELECT QUERIES
//  * ======================
//  */
// const selectSimpleUsers = async (quantity) => {
//     return await User.findAll({
//         limit: quantity
//     });
// };
//
// const selectComplexStudentsWithInformationAndCourses = async (quantity) => {
//     return await User.findAll({
//         where: {
//             account_role: {
//                 [Op.eq]: 'student'
//             }
//         },
//         include: [Student, Course],
//         order: [
//             ['surname']
//         ],
//         limit: quantity
//     });
// };
//
// const selectComplexUsersTasks = async (quantity) => {
//     return await User.findAll({
//         limit: quantity,
//         include: [
//             {
//                 model: Course,
//                 include: [
//                     {
//                         model: Task
//                     }
//                 ]
//             }
//         ]
//     });
// }
//
// // run function
// const run = async (benchmark, times = NUMBER_OF_REPEATS, numberOfRecords = NUMBER_OF_RECORDS, typeOfBenchmark = '', table = '') => {
//     console.log(`avg time of ${benchmark.name}:`);
//
//     for (let i = 0; i < numberOfRecords.length; i++) {
//         let tempTimes = [];
//         let recordsToFetch = numberOfRecords[i];
//
//         for (let j = 0; j < times; j++) {
//             let start = performance.now();
//             await benchmark(recordsToFetch);
//             let stop = performance.now();
//             tempTimes.push(stop - start);
//         }
//
//         console.log(lastBenchmarkQueries);
//
//         const minTime = Math.min(...tempTimes).toFixed(2);
//         const maxTime = Math.max(...tempTimes).toFixed(2);
//         const avgTime = +(tempTimes.reduce((sum, el) => sum + el, 0) / times).toFixed(2);
//
//         console.log(` - ${recordsToFetch}: ${avgTime}; min=${minTime}, max=${maxTime}`)
//     }
// };
//
// const addBenchmark = (benchmark, avgTime, queries) => {
//     benchmarks.push({
//         name: benchmark,
//         time: avgTime,
//         queries: queries
//     });
// };
//
// // send request to php
// const sendSaveResults = async () => {
//     await fetch("http://localhost/orm_benchmarking/index.php?save-results", {
//         method: "POST",
//         headers: {
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({
//             orm_name: "Sequelize",
//             orm_version: Sequelize.version,
//             benchmarks: benchmarks
//         })
//     }).then((response) => {
//         if (!response.ok)
//             throw "An error occurred during sending request: " + response.statusText;
//     })
// };
//
// // run benchmarks
// console.log("Performing benchmark tests. Please wait...");
//
// Promise.all([
//     run(selectSimpleUsers),
//     run(selectComplexStudentsWithInformationAndCourses)
//     run(selectComplexUsersTasks)
// ]).then(() => {
//     sendSaveResults()
//         .then(() => {
//             console.log("Results has been saved successfully.");
//
//             database.close().then(() => {
//                 process.exit();
//             });
//         })
//         .catch((error) => {
//             console.log(error);
//         });
// });

const {Op, Sequelize, sequelize} = require('sequelize');
const {User, Student, Course, Task, Teacher} = require("./Entities");
const {database, lastBenchmarkQueries} = require("./database");

// Benchmark parameters
const NUMBER_OF_REPEATS = 100;
const NUMBER_OF_RECORDS = [1, 50, 100, 500, 1000];
let benchmarks = [];

// Benchmark functions

/**
 * Select simple users.
 * @param {number} quantity - The number of users to fetch.
 * @returns {Promise<User[]>} - A promise resolving to an array of User objects.
 */
const selectSimpleUsers = async (quantity) => {
    return await User.findAll({
        limit: quantity
    });
};

/**
 * Select complex students with information and courses.
 * @param {number} quantity - The number of students to fetch.
 * @returns {Promise<User[]>} - A promise resolving to an array of User objects representing students.
 */
const selectComplexStudentsWithInformationAndCourses = async (quantity) => {
    return await User.findAll({
        where: {
            account_role: {
                [Op.eq]: 'student'
            }
        },
        include: [Student, Course],
        order: [
            ['surname']
        ],
        limit: quantity
    });
};

/**
 * Select complex users with tasks.
 * @param {number} quantity - The number of users to fetch.
 * @returns {Promise<User[]>} - A promise resolving to an array of User objects with their associated courses and tasks.
 */
const selectComplexUsersTasks = async (quantity) => {
    return await User.findAll({
        limit: quantity,
        include: [
            {
                model: Course,
                include: [
                    {
                        model: Task
                    }
                ]
            }
        ]
    });
};

/**
 * ======================
 *     INSERT QUERIES
 * ======================
 */
const insertUsers = async (users) => {
    for (const userData of users) {
        const transaction = await database.transaction();
        try {
            const user = await User.create({
                name: userData.name,
                surname: userData.surname,
                email: userData.email,
                password: userData.password,
                account_role: userData.account_role,
                active: userData.active
            }, {transaction});

            if (userData.student) {
                const studentData = {...userData.student, user_id: user.id};
                await Student.create(studentData, {transaction});
            }

            if (userData.teacher) {
                const teacherData = {...userData.teacher, user_id: user.id};
                await Teacher.create(teacherData, {transaction})
            }

            await transaction.commit();
        } catch (error) {
            await transaction.rollback();
            console.error('Error inserting users:', error);
        }
    }
};

const insertCourses = async (courses) => {
    return await Course.bulkCreate(courses);
}

/**
 * ======================
 *     UPDATE QUERIES
 * ======================
 */

// TODO: CHECK THIS
const updateCoursesEndDate = async (quantity) => {
    return await Course.update({ available_to: '2024-10-01' }, { limit: quantity });
}

/**
 * ======================
 *     DELETE QUERIES
 * ======================
 */

// TODO: FIX THIS
const detachUsersFromCourses = async (quantityUsers) => {
    const users = await User.findAll({ limit: quantityUsers });

    for (const user of users) {
        await user.removeCourses(); // Detach wszystkich kursów przypisanych do użytkownika
    }
}

// TODO: CHECK THIS
const deleteCourses = async (quantity) => {
    return await Course.destroy({
        order: ['id', 'desc'],
        limit: quantity
    });
}

// TODO: use this method in runBenchmark
const getMethodArgumentForMethod = (data, type, table, quantity) => {
    if(type === 'select' || type === 'update' || type === 'delete')
        return quantity;

    if(type === 'insert') {
        if(table === 'users')
            return data.slice(0, quantity);

        if(table === 'courses')
            return data.slice(0, quantity);
    }

    return '';
}


const {promisify} = require('util');
const exec = promisify(require('child_process').exec);

async function backupDatabase() {
    await exec('php ../../databaseBackup.php');
}

async function restoreDatabase() {
    await exec('php ../../databaseRestore.php');
}


/**
 * Run benchmarks for given functions.
 * @returns {Promise<void>} - A promise indicating the completion of benchmarking.
 */
const runBenchmarks = async () => {
    const benchmarkResults = [];
    const benchmarksToRun = [
        // { benchmark: selectSimpleUsers, name: 'Simple Users' },
        // { benchmark: selectComplexStudentsWithInformationAndCourses, name: 'Complex Students with Information and Courses' },
        // { benchmark: selectComplexUsersTasks, name: 'Complex Users Tasks' },
        {benchmark: insertUsers, name: 'Inserts user with their information'}
    ];

    const courses = require('../../courses.json');
    const users = require('../../users.json');

    for (const {benchmark, name} of benchmarksToRun) {
        console.log(`Benchmarking ${name}`);
        const results = {};
        for (let i = 0; i < NUMBER_OF_RECORDS.length; i++) {
            const tempTimes = [];
            // const recordsToFetch = NUMBER_OF_RECORDS[i];
            const recordsToFetch = users.slice(0, 1);

            for (let j = 0; j < NUMBER_OF_REPEATS; j++) {
                const start = performance.now();
                await benchmark(recordsToFetch);
                const stop = performance.now();
                tempTimes.push(stop - start);

                await executeCommand();
            }

            const minTime = Math.min(...tempTimes).toFixed(2);
            const maxTime = Math.max(...tempTimes).toFixed(2);
            const avgTime = +(tempTimes.reduce((sum, el) => sum + el, 0) / NUMBER_OF_REPEATS).toFixed(2);

            results[recordsToFetch] = {avgTime, minTime, maxTime};
            console.log(`  - ${NUMBER_OF_RECORDS[i]}: Avg=${avgTime}, Min=${minTime}, Max=${maxTime}`);
        }
        benchmarkResults.push({name, results});
    }
    return benchmarkResults;
};

// Save benchmark results

/**
 * Send benchmark results to the server.
 * @returns {Promise<void>} - A promise indicating the completion of sending benchmark results.
 */
const sendSaveResults = async () => {
    await fetch("http://localhost/orm_benchmarking/index.php?save-results", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            orm_name: "Sequelize",
            orm_version: Sequelize.version,
            benchmarks: benchmarks
        })
    }).then((response) => {
        if (!response.ok)
            throw "An error occurred during sending request: " + response.statusText;
    })
};

// Run benchmarks and save results

console.log("Performing benchmark tests. Please wait...");

runBenchmarks().then(benchmarkResults => {
    console.log("Benchmark results:");
    console.log(JSON.stringify(benchmarkResults, null, 2));
    benchmarks = benchmarkResults;

    sendSaveResults().then(() => {
        console.log("Results have been saved successfully.");

        database.close().then(() => {
            process.exit();
        });
    }).catch((error) => {
        console.log(error);
    });
});