import axios from "axios";
import { JSDOM } from "jsdom";
import { Parser as json2csv } from "@json2csv/plainjs";
import fs from "fs";
import { urls } from "./urls.js";

async function scrapeUrl(url) {
	try {
		const response = await axios.get(url);
		const dom = new JSDOM(response.data);
		const document = dom.window.document;

		// Extract the description
		const description = document.querySelector(".description-teaser--text").textContent.trim();
		// Extract the name
		const name = document.querySelector(".product--title").textContent.trim();
		// Extract the properties table
		const properties = {};
		Array.from(document.querySelectorAll(".product--properties-table .product--properties-row")).forEach((element) => {
			const label = element.querySelector(".product--properties-label").textContent.trim().replace(/:/g, "");
			const value = element.querySelector(".product--properties-value").textContent.trim();
			properties[label] = value;
		});

		return { url, name, description, ...properties };
	} catch (error) {
		console.error(`Error scraping URL: ${url}`);
		return null;
	}
}

// Array to store the scraped data
const data = [];

async function scrapeUrls(urls) {
	console.time();
	for (const url of urls) {
		const scrapedData = await scrapeUrl(url);
		// timeout to not get blocked
		await new Promise((resolve) => setTimeout(resolve, 500));

		if (scrapedData) {
			console.log(`scraped url: ${url}`);
			data.push(scrapedData);
		}
	}
	console.timeEnd();
}

scrapeUrls(urls)
	.then(() => {
		// exclude results where "Produktkategorie" does not include "wein"
		const filteredData = data.filter(
			(item) => item.Produktkategorie && item.Produktkategorie.toLowerCase().includes("wein")
		);

		// Convert the data to CSV format
		// watch out: if the first url doesnt have all properties, like "Speisen" its not included in the csv
		const fields = [
			"url",
			"description",
			...Object.keys(data[0]).filter((key) => key !== "url" && key !== "description"),
		];
		const json2csvParser = new json2csv({ fields });
		const csv = json2csvParser.parse(filteredData);

		// Output the CSV to a file
		const date = new Date();
		const timestamp = `${date.getDate()}-${
			date.getMonth() + 1
		}-${date.getFullYear()}_${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`;
		const fileName = `scraped_data-${timestamp}.csv`;
		fs.writeFileSync(`./scraped-data/${fileName}`, csv);
		console.log(`Data successfully exported to${fileName}`);
	})
	.catch((error) => console.error(error));
