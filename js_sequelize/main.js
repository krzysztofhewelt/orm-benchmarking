const { Sequelize, DataTypes, Op} = require('sequelize');

// Connect to database
const sequelize = new Sequelize('orm_benchmarking', 'postgres', 'admin123', {
    host: 'localhost',
    port: '5432',
    schema: 'public',
    dialect: "postgres" /* one of 'mysql' | 'mariadb' | 'postgres' | 'mssql' */
});

// Create Model
const User = sequelize.define('User', {
    name: {
        type: DataTypes.STRING
    }
    // Model attributes are defined here
    // firstName: {
    //     type: DataTypes.STRING,
    // },
    // lastName: {
    //     type: DataTypes.STRING
    // }
}, {
    // Other model options go here
    timestamps: false,
    tableName: "users"
});

// Create instance
// const jane = User.build({ firstName: "Jane", lastName: "Doe" });
// jane.save(); // save to database
//
// // Shortcut for creating instance and saving to database at once
// jane = User.create({ firstName: "Jane", lastName: "Doe" });

// Find all users
const users = User.findAll({
    benchmark: true,
    logging: console.log,
    where: {
        id: {
            [Op.gt]: 6
        }
    }
}).then((response) => {
    console.log(response);
    //console.log(response); // true
    //console.log("All users:", JSON.stringify(users, null, 2));
})


