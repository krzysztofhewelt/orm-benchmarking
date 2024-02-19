import template from '../template.json' assert { type: "json" };

const benchmarks = Array(template)[0];

const getAllBenchmarks = () => {


  return [...new Set(benchmarks.map((el, index) => el.benchmarks))];
}

console.log(getAllBenchmarks())

// wczytać testy wszystkie z array

// spośród tych testów wyświetl wyniki
//