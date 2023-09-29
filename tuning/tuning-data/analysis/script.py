
import re

total_words = 0
total_chars = 0
total_sentences = 0
lines = 0

# Loop over all lines in the file
with open('trainingData_1695475454304.jsonl', 'r') as file:
    for line in file:
        obj = json.loads(line)

        # Check if the role at index 2 of the messages list is assistant
        if obj['messages'][2]['role'] == 'assistant':
            # Access the content property of the object at index 2 of the messages list
            content = obj['messages'][2]['content']

            # Count the number of words and characters in the content
            words = len(content.split())
            chars = len(content)

            # Count the number of sentences by counting the number of: .  ! ?
            # For a basic count is that enough
            sentences = len(re.findall(r'[.!?]', content))

            total_words += words
            total_chars += chars
            total_sentences += sentences
            lines += 1

# Calculate the averages
average_words = total_words / lines
average_chars = total_chars / lines
average_sentences = total_sentences / lines

# average_words, average_chars, average_sentences
print(average_words, average_chars, average_sentences, lines)
