<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Record;

abstract class AbstractPlaceRecord extends AbstractRecord
{
    /**
     * @var array<string>
     */
    private $locales;
    /**
     * @ignore
     */
    public function __construct(?array $record, array $locales = ['en'])
    {
        $this->locales = $locales;
        parent::__construct($record);
    }
    /**
     * @ignore
     *
     * @return mixed
     */
    public function __get(string $attr)
    {
        if ($attr === 'name') {
            return $this->name();
        }
        return parent::__get($attr);
    }
    /**
     * @ignore
     */
    public function __isset(string $attr) : bool
    {
        if ($attr === 'name') {
            return $this->firstSetNameLocale() !== null;
        }
        return parent::__isset($attr);
    }
    private function name() : ?string
    {
        $locale = $this->firstSetNameLocale();
        // @phpstan-ignore-next-line
        return $locale === null ? null : $this->names[$locale];
    }
    private function firstSetNameLocale() : ?string
    {
        foreach ($this->locales as $locale) {
            if (isset($this->names[$locale])) {
                return $locale;
            }
        }
        return null;
    }
}
