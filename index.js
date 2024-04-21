const express = require("express")
const path = require("path");
const {extractChartData} = require("./charts.js");
const jsonData = require("./results.json");

const app = express()
const port = 8000;

app.use(express.static(path.join(__dirname)));

// Define route to serve simple users data
app.get("/simpleUsersData", (req, res) => {
    const simpleUsersData = extractChartData(jsonData, "SelectSimpleUsers");
    res.json(simpleUsersData);
});

// Define route to serve complex students data
app.get("/complexStudentsData", (req, res) => {
    const complexStudentsData = extractChartData(jsonData, "SelectComplexStudentsWithInformationAndCourses");
    res.json(complexStudentsData);
});

// Define route to render index.html
app.get("/", (req, res) => {
    res.sendFile(path.join(__dirname, "report.html"));
});


app.listen(port, () => {
    console.log(`Server listening at http://localhost:${port}`);
});

process.on("SIGINT", function () {
    console.log("Server stopped");
    process.exit(1);
});