import 'dotenv/config';
import { Configuration, OpenAIApi } from 'openai';
import fs from 'fs';
import csv from 'csv-parser';
import axios from 'axios';
// Check if the CSV file name is provided as a command-line argument

console.log(process.argv.length);
if (process.argv.length < 2) {
  console.error('Please provide the CSV file name and the Model (openai, alephalpha) as a command-line argument.');
  process.exit(1);
}

const configuration = new Configuration({
  organization: process.env.OPENAI_ORG,
  apiKey: process.env.OPENAI_API_KEY,
});
const openai = new OpenAIApi(configuration);
const openaiModel = 'text-davinci-003';
const alephalphaModel = 'luminous-supreme-control';
const timestamp = new Date().getTime();
const filePath = `./scraped-data/${process.argv[2]}`;
const filePathResult = `./results/${timestamp}_${process.argv[2]}`;
const csvContent = [];
const excludedColumns = [
  'url',
  'description',
  'Trinktemperatur',
  'Säure',
  'Restzucker',
  'Alkoholgehalt',
  'Flaschengröße',
];
let tonalität = 'verspielt'; // freundlich, professionell oder verspielt
// const tonalitäten = ['verspielt'];
const tonalitäten = ['verspielt', 'freundlich', 'professionell', 'dramatisch'];
const instructions = `Du bist ein Experte für Wein und sollst eine Beschreibung für Wein erstellen. Formuliere den Text ${tonalität}. Die Beschreibung wird in einem Onlineshop genutzt. Die Beschreibung soll nicht länger als 200 Wörter sein. Es sollen nur eine Auswahl der passenden Speisen vorkommen. Nehme die Aromen und Düfte mit in die Beschreibung auf. Hier sind die Informationen zu dem Wein:`;
const instructionsOpenAiChat = `Du bist ein Experte für Wein und sollst eine Beschreibung für Wein erstellen. Formuliere den Text ${tonalität}. Die Beschreibung wird in einem Onlineshop genutzt. Die Beschreibung soll nicht länger als 200 Wörter sein. Es sollen nur eine Auswahl der passenden Speisen vorkommen. Nehme die Aromen und Düfte mit in die Beschreibung auf.`;
// async function main() {
//   const completion = await openai.createChatCompletion({
//     model: 'gpt-4',
//     messages: [
//       { role: 'system', content: 'You are a helpful assistant' },
//       { role: 'user', content: 'Hello world' },
//     ],
//   });

//   console.log(completion.data.choices[0].message.content);
// }

// main();
// read the CSV file from filePath
fs.createReadStream(filePath)
  .pipe(csv({ separator: ',' }))
  .on('data', (data) => csvContent.push(data))
  .on('end', async () => {
    console.log(`Input File "${filePath}" read. Starting to generate prompts.`);
    // return;
    for (const row in csvContent) {
      if (Object.hasOwnProperty.call(csvContent, row)) {
        let currentPromp = composePromptFromObject(csvContent[row]);

        // let response;
        // switch (process.argv[3]) {
        //   case 'openai':
        //     response = await callOpenAi(currentPromp);
        //     csvContent[row]['Model'] = `OpenAI: ${openaiModel}`;
        //     break;
        //   case 'alephalpha':
        //     response = await callAlephAlpha(currentPromp);
        //     csvContent[row]['Model'] = `AlephAlpha: ${alephalphaModel}`;
        //     break;
        // }
        try {
          for (const ton of tonalitäten) {
            tonalität = ton;
            csvContent[row][`OpenAI-${openaiModel}(${tonalität})`] = await callOpenAi(currentPromp);
            csvContent[row][`AlephAlpha-${openaiModel}(${tonalität})`] = await callAlephAlpha(currentPromp);
            csvContent[row][`OpenAI-GPT4-(${tonalität})`] = await callOpenAiChat(currentPromp);
            console.log(`prompts generated for ${tonalität}`);
          }
        } catch (error) {
          console.log(error);
        }

        csvContent[row]['Instructions'] = instructions;
        csvContent[row]['Prompt'] = currentPromp;
        // csvContent[row]['Generierte Beschreibung'] = response;

        console.log(`Prompt ${row} from ${csvContent.length} recived`);
      }
    }
    writeCSVFile(csvContent);
  });

function cleanCSV(csvContent) {
  const updatedData = csvContent.map((obj) => {
    const newObj = {};
    for (const key in obj) {
      if (!excludedColumns.includes(key)) {
        newObj[key] = obj[key];
      }
    }
    return newObj;
  });
  return updatedData;
}

/**
 * @param {string} prompt - the prompt to send to the openai api
 * @returns {string} - the response from the openai api
 */

async function callOpenAi(prompt) {
  const response = await openai.createCompletion({
    model: openaiModel,
    prompt: `${instructions}
      ${prompt}`,
    max_tokens: 500,
    temperature: 0.5,
  });
  // console.log(response.data.config.data);
  console.log('openAI prompt generated');
  return response.data.choices[0].text;
}

/**
 * This function sends a chat prompt to the OpenAI API and returns the generated response.
 *
 * @async
 * @function callOpenAiChat
 * @param {string} prompt - The prompt to send to the OpenAI API.
 * @returns {string} The generated response from the OpenAI API.
 */
async function callOpenAiChat(prompt) {
  const response = await openai.createChatCompletion({
    model: 'gpt-4',
    messages: [
      { role: 'system', content: instructionsOpenAiChat },
      { role: 'user', content: prompt },
    ],
    max_tokens: 500,
    temperature: 0.5,
  });

  // console.log(response.data.choices[0].message.content);
  console.log('openAI chat prompt generated');

  return response.data.choices[0].message.content;
}

/**
 * @param {string} prompt - the prompt to send to the alephalpha api
 * @returns {string} - the generated description from the alephalpha api
 */
async function callAlephAlpha(prompt) {
  const promptTemplate = `### Instruction:
  ${instructions}

  ### Input:
  ${prompt}

  ### Response:`;
  const body = JSON.stringify({
    model: alephalphaModel,
    prompt: promptTemplate,
    maximum_tokens: 500,
    temperature: 0.5,
  });
  const config = {
    method: 'post',
    maxBodyLength: Infinity,
    url: 'https://api.aleph-alpha.com/complete',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: `Bearer ${process.env.ALEPH_ALPHA_API_KEY}`,
    },
    data: body,
  };
  const response = await axios(config);
  console.log('alephalpha prompt generated');
  return response.data.completions[0].completion;
}
function composePromptFromObject(providedObject) {
  /*
   * bring the object into the right format. aka:
   * const prompt = `
   *  Art: Rotwein,
   *  Rebsorte: Cabernet Sauvignon,
   *  Land: Frankreich,
   *  ...`
   */

  const obj = { ...providedObject };
  excludedColumns.forEach((key) => {
    if (obj.hasOwnProperty(key)) {
      delete obj[key];
    }
  });

  let prompt = '';

  for (const key in obj) {
    prompt += `${key}: ${obj[key]},\n`;
  }

  return prompt;
}

function escapeDoubleQuotes(str) {
  return str.replace(/"/g, '""');
}

function writeCSVFile(data) {
  const csvHeaders = Object.keys(data[0]).join(',') + '\n';
  const csvRows = data
    .map((obj) => {
      return Object.values(obj)
        .map((value) => {
          if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
            return `"${escapeDoubleQuotes(value)}"`;
          }
          return value;
        })
        .join(',');
    })
    .join('\n');
  const csvContent = csvHeaders + csvRows;
  fs.writeFileSync(filePathResult, csvContent);
  console.log('File written');
}
