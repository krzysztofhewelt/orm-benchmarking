const {Teacher} = require('./Teacher');
const {User} = require('./User');
const {Student} = require("./Student");
const {Course} = require("./Course");
const {Task} = require("./Task");


// User 1-1 Teacher
User.hasOne(Teacher, {foreignKey: 'user_ID'});
Teacher.belongsTo(User, {foreignKey: 'user_ID'});

// User 1-* Student
User.hasMany(Student, {foreignKey: 'user_ID'});
Student.belongsTo(User, {foreignKey: 'user_ID'});

// User *-* Course
User.belongsToMany(Course, { through: 'course_enrollments', foreignKey: 'user_ID', timestamps: false });
Course.belongsToMany(User, { through: 'course_enrollments', foreignKey: 'course_ID', timestamps: false });

// Course 1-* Task
Course.hasMany(Task, {foreignKey: 'course_ID'});
Task.belongsTo(Course, {foreignKey: 'course_ID'});

module.exports = {
    User,
    Teacher,
    Student,
    Course,
    Task
};