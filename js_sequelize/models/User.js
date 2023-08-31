const {database} = require("../database")
const {  DataTypes} = require('sequelize');

module.exports.User = database.define('User', {
    id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    name: {
        type: DataTypes.STRING(45),
        allowNull: false
    },
    surname: {
        type: DataTypes.STRING(45),
        allowNull: false
    },
    email: {
        type: DataTypes.STRING,
        allowNull: false
    },
    password: {
        type: DataTypes.STRING,
        allowNull: false
    },
    account_role: {
        type: DataTypes.STRING(20),
        allowNull: false
    },
    active: {
        type: DataTypes.BOOLEAN,
        allowNull: false
    },
}, {
    timestamps: false,
    tableName: 'users',
})

