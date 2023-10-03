<?php declare(strict_types=1);

namespace AiDescription\Service;

/**
 * Class ComposePrompt
 *
 * This class is responsible for composing prompts and messages for generating and rephrasing descriptions.
 */
class ComposePrompt
{
    /**
     * Compose instructions for generation a wine description.
     *
     * @param string $tonality The tonality of the description
     * @param int $maxLength The maximum length of the description in words
     * @return string The composed instructions
     */
    public function composeInstructionsForGeneration(string $tonality, int $maxLength = 200): string
    {
        $instructions = <<<PROMPT
    Du bist ein Experte für Wein und sollst eine Beschreibung für einen Wein erstellen.
    Du formulierst die Texte $tonality. Die Beschreibung wird in einem Onlineshop genutzt.
    Die Beschreibung soll $maxLength  Wörter lang sein. Es soll eine Auswahl der passenden Speisen vorkommen.
    Nehme die Aromen und Düfte mit in die Beschreibung auf.
    PROMPT;

        return $instructions;
    }

    /**
     * This method composes the  the message with the given attributes.
     *
     * @param array $attributes The attributes of the wine
     * @return string The composed message
     */
    public function composeMessageForGeneration(array $attributes): string
    {
        $includedAttributesString = "";
        $excludedAttributesString = "";

        foreach ($attributes as $property) {
            // we use the checkbox state (checked or not) from the frontend, to determine if the attribute should be included or excluded
            $property->checked === true ?
                $includedAttributesString .= "{$property->name}: {$property->options[0]->name}. \n" :
                $excludedAttributesString .= "{$property->name}: {$property->options[0]->name}. \n";
        }
        $initialMessage = <<<PROMPT
    Hier sind die Informationen über den Wein:
    $includedAttributesString

    Die folgenden Informationen sind auch über den selben Wein, sollen aber nicht explizit in der Beschreibung genannt werden.
    Nutze sie um den Wein besser zu verstehen:
    $excludedAttributesString
    PROMPT;

        return $initialMessage;
    }

    /**
     * This method composes a prompt for rephrasing a wine description in a specific tonality.
     *
     * @param string $tonality The tonality in which the description should be rephrased
     * @return string The composed prompt for rephrasing
     */
    public function composePromptForRephrasing(string $tonality): string
    {
        $instructions = <<<PROMPT
    Lese den Text der dir zur Verfügung gestellt wird.
    Formuliere den Satz innerhalb der span Elemente mit dem data-change Attribute neu, in einem $tonality Ton.
    Der Rest des Textes soll gleich bleiben.
    Behalte die HTML Formatierung bei. Entferne in der Ausgabe das data-change Attribute.
    PROMPT;

        return $instructions;
    }
}
