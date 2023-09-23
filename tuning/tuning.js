import "dotenv/config";
import OpenAI from "openai";
import fs from "fs";

const openai = new OpenAI({
	apiKey: process.env.OPENAI_API_KEY,
});
const TRAINING_FILE = "";

async function uploadToOpenAI() {
	const result = await openai.files.create({
		file: fs.createReadStream("trainingData_1695411166006.jsonl"),
		purpose: "fine-tune",
	});
	console.log(result);
	/* Example Answer
  {
  object: 'file',
  id: 'file-sVTUHPt8qRcVAth6LZAMdHjz',
  purpose: 'fine-tune',
  filename: 'trainingData_1695411166006.jsonl',
  bytes: 365031,
  created_at: 1695411715,
  status: 'uploaded',
  status_details: null
  }
  */
}
// await uploadToOpenAI();
async function initTraining() {
	const fineTune = await openai.fineTuning.jobs.create({
		training_file: TRAINING_FILE,
		model: "gpt-3.5-turbo",
		suffix: "500",
	});
	console.log(fineTune);
}
// initTraining();
async function getTrainingStatus() {
	let page = await openai.fineTuning.jobs.list({ limit: 10 });
	console.log(page);
}
