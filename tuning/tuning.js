import "dotenv/config";
import OpenAI from "openai";
import fs from "fs";

const openai = new OpenAI({
	apiKey: process.env.OPENAI_API_KEY,
});
const TRAINING_FILE = "";
const TRAINING_FILE_ID = "";

async function uploadToOpenAI() {
	const result = await openai.files.create({
		file: fs.createReadStream(TRAINING_FILE),
		purpose: "fine-tune",
	});
	console.log(result);
	/* Example Answer
  {
  object: 'file',
  id: 'file-XXX',
  purpose: 'fine-tune',
  filename: 'XXX.jsonl',
  bytes: 365031,
  created_at: XXX,
  status: 'uploaded',
  status_details: null
  }
  */
}
// await uploadToOpenAI();
async function initTraining() {
	const fineTune = await openai.fineTuning.jobs.create({
		training_file: TRAINING_FILE_ID,
		model: "gpt-3.5-turbo",
		// suffix: "500",
	});
	console.log(fineTune);
	/* Example Answer
  {
  object: 'fine_tuning.job',
  id: 'ftjob-XXX',
  model: 'gpt-3.5-turbo-0613',
  created_at: XXX,
  finished_at: null,
  fine_tuned_model: null,
  organization_id: 'org-XXX',
  result_files: [],
  status: 'validating_files',
  validation_file: null,
  training_file: 'file-XXX',
  hyperparameters: { n_epochs: 'auto' },
  trained_tokens: null,
  error: null
  }
  */
}
// initTraining();
async function getTrainingStatus() {
	let page = await openai.fineTuning.jobs.list({ limit: 10 });
	console.log(page);
}
