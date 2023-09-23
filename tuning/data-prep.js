import axios from "axios";
import { JSDOM } from "jsdom";
import fs from "fs";
import { urls } from "./urls.js";
const timestamp = new Date().getTime();

const selectedUrls = urls.slice(0, 500);

const instructions =
	"Du bist ein Experte für Wein und sollst eine Beschreibung für einen Wein erstellen. Die Beschreibung wird in einem Onlineshop genutzt. Die Beschreibung soll 200 Wörter lang sein. Es soll eine Auswahl der passenden Speisen vorkommen. Nehme die Aromen und Düfte mit in die Beschreibung auf.";
const messagePartOne =
	"Hier sind die Informationen über den Wein. Die Informationen sind durch ein ; (Semikolon) getrennt:";
const messagePartTwo =
	"Die folgenden Informationen sind auch über den selben Wein, sollen aber nicht explizit in der Beschreibung genannt werden. Nutze sie um den Wein besser zu verstehen. Die Informationen sind durch ; (Semikolon) getrennt:";

const trainingData = [];
// Function to scrape a single URL
async function scrapeUrl(url) {
	try {
		const response = await axios.get(url);
		const dom = new JSDOM(response.data);
		const document = dom.window.document;

		// Extract the description
		const description = document.querySelector(".description-teaser--text").textContent.trim();
		// Extract the properties table
		const properties = {};
		// Extract the name
		properties.name = document.querySelector(".product--title").textContent.trim();
		Array.from(document.querySelectorAll(".product--properties-table .product--properties-row")).forEach((element) => {
			const label = element.querySelector(".product--properties-label").textContent.trim().replace(/:/g, "");
			let value = element.querySelector(".product--properties-value").textContent.trim();
			//remove strange whitespaces between values. maybe its due to a new line on the webpage...
			value = value.replace(/\s+/g, " ");
			properties[label] = value;
		});
		//move unwanted properties
		const unwantedProperties = {};
		unwantedProperties["Flaschengröße"] = properties["Flaschengröße"];
		unwantedProperties["Alkoholgehalt"] = properties["Alkoholgehalt"];
		unwantedProperties["Restzucker"] = properties["Restzucker"];
		unwantedProperties["Säure"] = properties["Säure"];
		unwantedProperties["Trinktemperatur"] = properties["Trinktemperatur"];
		unwantedProperties["Hinweis auf Allergene"] = properties["Hinweis auf Allergene"];
		unwantedProperties["Hersteller"] = properties["Hersteller"];

		delete properties["Flaschengröße"];
		delete properties["Alkoholgehalt"];
		delete properties["Restzucker"];
		delete properties["Säure"];
		delete properties["Trinktemperatur"];
		delete properties["Hinweis auf Allergene"];
		delete properties["Hersteller"];

		const propertyString = Object.entries(properties)
			.map(([key, value]) => `${key}: ${value}`)
			.join("; ");

		const unwantedPropertiesString = Object.entries(unwantedProperties)
			.map(([key, value]) => `${key}: ${value}`)
			.join("; ");
		let message = {
			messages: [
				{ role: "system", content: instructions },
				{
					role: "user",
					content: `${messagePartOne} ${propertyString} \n\n${messagePartTwo} ${unwantedPropertiesString}`,
				},
				{ role: "assistant", content: description },
			],
		};
		console.log(message);
		trainingData.push(message);

		return true;
		// return { url, description, properties, unwantedProperties };
	} catch (error) {
		console.log(error);
		console.error(`Error scraping URL: ${url}`);
		return null;
	}
}
// console.log(result);
// writeToFile();
async function scrapeUrls(urls) {
	console.time();
	writeToFile(selectedUrls);
	for (const url of urls) {
		const scrapedData = await scrapeUrl(url);
		// timeout to not get blocked
		await new Promise((resolve) => setTimeout(resolve, 500));

		if (scrapedData) {
			console.log(`scraped url: ${url}`);
		}
	}
	writeToJsonLine();
	console.timeEnd();
}
const result = await scrapeUrls(selectedUrls);

function writeToFile(array) {
	const jsonData = JSON.stringify(array, null, 2);

	fs.writeFile(`usedurls_${timestamp}.json`, jsonData, (err) => {
		if (err) {
			console.error("Error writing JSON file:", err);
		} else {
			console.log("JSON file written successfully.");
		}
	});
}

function writeToJsonLine() {
	//https://jsonlines.org/
	const data = trainingData.map((x) => JSON.stringify(x)).join("\n");
	fs.writeFile(`trainingData_${timestamp}.jsonl`, data, (err) => {
		if (err) {
			console.error("Error writing JSON file:", err);
		} else {
			console.log("JSONL file written successfully.");
		}
	});
}

async function uploadToOpenAI() {
	const openai = new OpenAI();
	await openai.files.create({ file: fs.createReadStream("mydata.jsonl"), purpose: "fine-tune" });
}
