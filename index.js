const express = require("express")

const app = express()
const port = 8000;

app.listen(port, () => {
    console.log(`Server listening at http://localhost:${port}`);
});

process.on("SIGINT", function () {
    console.log("Server stopped");
    process.exit(1);
});