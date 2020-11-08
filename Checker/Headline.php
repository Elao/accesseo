<?php

declare(strict_types=1);

namespace Elao\Bundle\SEOTool\Checker;

class Headline
{
    /** @var int */
    public $level;

    /** @var string|null */
    public $content;

    /** @var array */
    public $children;

    /** @var Headline|null */
    public $parent;

    public function __construct(int $level, ?string $content)
    {
        $this->level = $level;
        $this->content = $content;
        $this->parent = null;
        $this->children = [];
    }

    public function addChild(Headline $headline): void
    {
        $this->children[] = $headline;
        $headline->setParent($this);
    }

    public function setParent(Headline $parent): void
    {
        $this->parent = $parent;
    }

    public function hasChildren(): bool
    {
        return \count($this->children) > 0;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getHn(): string
    {
        return sprintf('h%d', $this->level);
    }

    public function isParent(): bool
    {
        return $this->parent !== null;
    }

    public function getParent(): ?Headline
    {
        return $this->parent;
    }

    public function getParentForLevel(int $level): ?Headline
    {
        if ($this->level < $level) {
            return $this;
        }

        if ($this->parent === null) {
            return null;
        }

        return $this->parent->getParentForLevel($level);
    }
}
