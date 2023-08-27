import fs from "fs";
import csv from "csv-parser";
import path from "path";
if (process.argv.length < 3) {
	console.error("Please provide the CSV file name as an argument");
	process.exit(1);
}
const filePath = `${process.argv[2]}`;

const strings = {
	Herkunftsland: "Dieser Wein ist aus",
	Anbauregion: "Er kommt aus der Region",
	Hersteller: "Er wurde hergestellt von",
	Weingut: "Er kommt aus dem Weingut",
	Produktkategorie: "Er ist ein",
	Qualitätsstufe: "Er ist von der Qualitätsstufe",
	Cuvee: "Er ist eine Cuvée aus",
	Geschmacksrichtung: "Er ist",
	Farbnuance: "Die Farbnuance ist",
	Flaschengröße: "Er ist abgefüllt in einer Flasche mit",
	Alkoholgehalt: "Er hat einen Alkoholgehalt von",
	Restzucker: "Er hat einen Restzuckergehalt von",
	Säure: "Er hat einen Säuregehalt von",
	Trinktemperatur: "Er hat die empfohlene Trinktemperatur von",
	Speisen: "Er passt gut",
	Auszeichnungen: "Er hat folgende Auszeichnungen:",
};

// read the data from the csv file
// for every row: create a new object with 'url', 'description and all the other properties combined into one string
// the string is combined based on the prepared strings above and the value of the corresponding property

const csvContent = [];
const trainingData = [];

fs.createReadStream(filePath)
	.pipe(csv({ separator: ";" }))
	.on("data", (data) => csvContent.push(data))
	.on("end", async () => {
		console.log(`Input File "${filePath}" read. Starting to generate prompts.`);
		for (const row in csvContent) {
			if (Object.hasOwnProperty.call(csvContent, row)) {
				let currentPromp = composePromptFromObject(csvContent[row]);
				console.log(currentPromp);
				const stopSequence = "####";
				trainingData.push({ propmt: currentPromp, completion: ` ${csvContent[row].description}${stopSequence}` });
			}
		}
		// writeCSVFile(csvContent);
	});

function composePromptFromObject(providedObject) {
	const obj = { ...providedObject };

	let prompt = "";

	for (const key in obj) {
		if (key === "url" || key === "description") continue;
		if (obj[key] === "") continue;
		if (strings[key] === undefined) continue;
		if (key === "Speisen" && !obj[key].includes("zu")) {
			obj[key] = `zu ${obj[key]}`;
		}
		prompt += `${strings[key]} ${obj[key]}. `;
	}

	return prompt;
}
