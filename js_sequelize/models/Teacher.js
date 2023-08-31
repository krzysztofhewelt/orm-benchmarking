const {database} = require("../database");
const {DataTypes} = require('sequelize');

module.exports.Teacher = database.define('Teacher', {
    user_id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
    },
    scien_degree: {
        type: DataTypes.STRING(50),
        allowNull: false
    },
    business_email: {
        type: DataTypes.STRING,
        allowNull: false
    },
    contact_number: {
        type: DataTypes.STRING(20),
        allowNull: true
    },
    room: {
        type: DataTypes.STRING(20),
        allowNull: true
    },
    consultation_hours: {
        type: DataTypes.STRING,
        allowNull: true
    }
}, {
    tableName: 'teacher_info',
    timestamps: false
});