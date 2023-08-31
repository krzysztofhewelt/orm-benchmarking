const {Teacher} = require('./Teacher');
const {User} = require('./User');
const {Student} = require("./Student");
const {Course} = require("./Course");
const {Task} = require("./Task");


// User 1-1 Teacher
User.hasOne(Teacher, {foreignKey: 'user_id'});
Teacher.belongsTo(User, {foreignKey: 'user_id'});

// User 1-* Student
User.hasMany(Student, {foreignKey: 'user_id'});
Student.belongsTo(User, {foreignKey: 'user_id'});

// User *-* Course
User.belongsToMany(Course, { through: 'course_enrollments', foreignKey: 'user_id', timestamps: false });
Course.belongsToMany(User, { through: 'course_enrollments', foreignKey: 'course_id', timestamps: false });

// Course 1-* Task
Course.hasMany(Task, {foreignKey: 'course_id'});
Task.belongsTo(Course, {foreignKey: 'course_id'});

module.exports = {
    User,
    Teacher,
    Student,
    Course,
    Task
};