const {Op, sequelize} = require('sequelize');
const {User, Student, Course, Task, Teacher} = require("./Entities");
const {database, getBenchmarkQueries, countBenchmarkQueries, clearBenchmarkQueries } = require("./database");
const {getMethodArgumentForMethod, restoreDatabase, sendSaveResults} = require("../benchmarkUtils");

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

const updateCoursesEndDate = async (quantity) => {
    return await Course.update({ available_to: '2024-10-01' }, { where: {}, limit: quantity });
}

/**
 * ======================
 *     DELETE QUERIES
 * ======================
 */

const detachUsersFromCourses = async (quantityUsers) => {
    const users = await User.findAll({ limit: quantityUsers });

    for (const user of users) {
        await user.removeCourses(); // Detach wszystkich kursów przypisanych do użytkownika
    }
}

const deleteCourses = async (quantity) => {
    return await Course.destroy({
        where: {},
        limit: quantity
    });
}


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
        { benchmark: selectSimpleUsers, type: 'select', name: 'Simple Users' },
        { benchmark: selectComplexStudentsWithInformationAndCourses, type: 'select', name: 'Complex Students with Information and Courses' },
        { benchmark: selectComplexUsersTasks, type: 'select', name: 'Complex Users Tasks' },
        // { benchmark: insertUsers, type: 'insert', table: 'users', name: 'Inserts user with their information' },
        // { benchmark: insertCourses, type: 'insert', table: 'courses', name: 'Inserts courses' },
        // { benchmark: updateCoursesEndDate, type: 'update', name: 'Update courses table (prolong end date)' },
        // { benchmark: detachUsersFromCourses, type: 'delete', name: 'Removes n users from all their courses' },
        // { benchmark: deleteCourses, type: 'delete', name: 'Delete n courses' },
    ];

    const courses = require('../../courses.json');
    const users = require('../../users.json');

    for (const {benchmark, name, type, table = ''} of benchmarksToRun) {
        console.log(`Benchmarking ${benchmark.name}`);
        const results = {};
        for (let i = 0; i < NUMBER_OF_RECORDS.length; i++) {
            const tempTimes = [];
            const recordsToFetch = NUMBER_OF_RECORDS[i];

            let dataToAdd = [];
            if(table === 'users')
                dataToAdd = users;
            else if (table === 'courses')
                dataToAdd = courses;

            const methodArgument = getMethodArgumentForMethod(type, recordsToFetch, dataToAdd);

            for (let j = 0; j < NUMBER_OF_REPEATS; j++) {
                clearBenchmarkQueries();

                const start = performance.now();
                await benchmark(methodArgument);
                const stop = performance.now();
                tempTimes.push(stop - start);

                if(type !== 'select')
                    await restoreDatabase();
            }

            const minTime = +Math.min(...tempTimes).toFixed(2);
            const maxTime = +Math.max(...tempTimes).toFixed(2);
            const avgTime = +(tempTimes.reduce((sum, el) => sum + el, 0) / NUMBER_OF_REPEATS).toFixed(2);

            results[recordsToFetch] = {"time": avgTime, "min": minTime, "max": maxTime, "numberOfQueries": countBenchmarkQueries(), "queries": getBenchmarkQueries()};
            console.log(` - ${NUMBER_OF_RECORDS[i]}: Avg=${avgTime}, Min=${minTime}, Max=${maxTime}`);
        }
        benchmarkResults.push({name, "numberOfRecords": results});
    }

    await database.close();

    return benchmarkResults;
};

// Run benchmarks and save results
console.log("Performing benchmark tests. Please wait...");

runBenchmarks().then(benchmarkResults => {
    sendSaveResults("Sequelize", sequelize.version, benchmarkResults).then(() => {
        console.log("Results have been saved successfully.");
        process.exit();
    }).catch((error) => {
        console.log(error);
    });
});
