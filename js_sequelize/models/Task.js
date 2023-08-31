const {database} = require("../database");
const {DataTypes} = require("sequelize");


module.exports.Task = database.define('Task', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    name: {
        type: DataTypes.STRING(100),
        allowNull: false
    },
    description: {
        type: DataTypes.TEXT,
        allowNull: false
    },
    available_from: {
        type: DataTypes.DATE,
        allowNull: false
    },
    available_to: {
        type: DataTypes.DATE,
        allowNull: true
    },
    max_points: {
        type: DataTypes.FLOAT,
        allowNull: false
    },
    course_ID: {
        type: DataTypes.INTEGER,
        primaryKey: true,
    },
}, {
    tableName: 'tasks',
    timestamps: false
});