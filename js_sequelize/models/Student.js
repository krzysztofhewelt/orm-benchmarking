const {database} = require("../database");
const {DataTypes} = require("sequelize");

module.exports.Student = database.define('Student', {
    user_ID: {
        type: DataTypes.INTEGER,
        primaryKey: true,
    },
    field_of_study: {
        type: DataTypes.STRING(50),
        allowNull: false
    },
    semester: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    year_of_study: {
        type: DataTypes.STRING(10),
        allowNull: false
    },
    mode_of_study: {
        type: DataTypes.STRING(20),
        allowNull: false
    }
}, {
    tableName: 'student_info',
    timestamps: false
});