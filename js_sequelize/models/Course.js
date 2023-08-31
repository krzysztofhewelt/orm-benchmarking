const {database} = require("../database");
const {DataTypes} = require("sequelize");

module.exports.Course = database.define('Course', {
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
        allowNull: true
    },
    available_from: {
        type: DataTypes.DATE,
        allowNull: false
    },
    available_to: {
        type: DataTypes.DATE,
        allowNull: true
    }
}, {
    tableName: 'courses',
    timestamps: false
});