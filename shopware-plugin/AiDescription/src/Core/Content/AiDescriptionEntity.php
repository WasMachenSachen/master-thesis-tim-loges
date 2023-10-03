<?php declare (strict_types=1);

namespace AiDescription\Core\Content;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class AiDescriptionEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $evaluation;

    /**
     * @var string
     */
    protected $used_prompt;

    /**
     * @var string
     */
    protected $used_configuration;

    /**
     * @var string
     */
    protected $used_attributes;

    /**
     * @var string
     */
    protected $used_tonality;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->descrcontentiption = $content;
    }

    public function getEvaluation(): string
    {
        return $this->evaluation;
    }

    public function setEvaluation(string $evaluation): void
    {
        $this->evaluation = $evaluation;
    }

    public function getUsedPrompt(): string
    {
        return $this->used_prompt;
    }

    public function setUsedPrompt(string $used_prompt): void
    {
        $this->used_prompt = $used_prompt;
    }

    public function getUsedConfiguration(): string
    {
        return $this->used_configuration;
    }

    public function setUsedConfiguration(string $used_configuration): void
    {
        $this->used_configuration = $used_configuration;
    }

    public function getUsedAttributes(): string
    {
        return $this->used_attributes;
    }

    public function setUsedAttributes(string $used_attributes): void
    {
        $this->used_attributes = $used_attributes;
    }

    public function getUsedTonality(): string
    {
        return $this->used_tonality;
    }

    public function setUsedTonality(string $used_tonality): void
    {
        $this->used_tonality = $used_tonality;
    }


    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }


}
