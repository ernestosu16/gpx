<?php

namespace App\Config\Data;

abstract class _Data_
{
    private ?string $parent;
    private string $code;
    private string $name;
    private string $description;
    private string $discriminator;

    abstract static function parent(): ?string;

    abstract static function code(): string;

    abstract static function name(): string;

    abstract static function description(): string;

    abstract static function discriminator(): string;

    private function __construct()
    {
        $this->parent = static::parent();
        $this->code = static::code();
        $this->name = static::name();
        $this->description = static::description();
        $this->discriminator = static::discriminator();
    }

    public function __toString(): string
    {
        return $this->getCodeComplete();
    }

    public static function newInstance(): static
    {
        return new static();
    }

    public function getParent(): self|null
    {
        $class = $this->parent;
        return $class ? new $class() : null;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    /**
     * @param string $discriminator
     * @return _Data_
     */
    public function setDiscriminator(string $discriminator): _Data_
    {
        $this->discriminator = $discriminator;
        return $this;
    }

    /**
     * @param string $prefix
     * @return string
     */
    public function getCodeComplete(string $prefix = ''): string
    {
        if ($this->getParent())
            $prefix .= $this->getParent()->getCodeComplete($prefix);

        return ($prefix) ? sprintf('%s_%s', $prefix, $this->getCode()) : $this->getCode();
    }
}
