<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PhabelVendor\Symfony\Component\String\Slugger;

use PhabelVendor\Symfony\Component\String\AbstractUnicodeString;
use PhabelVendor\Symfony\Component\String\UnicodeString;
use PhabelVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
if (!\interface_exists(LocaleAwareInterface::class)) {
    throw new \LogicException('You cannot use the "Symfony\\Component\\String\\Slugger\\AsciiSlugger" as the "symfony/translation-contracts" package is not installed. Try running "composer require symfony/translation-contracts".');
}
/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class AsciiSlugger implements SluggerInterface, LocaleAwareInterface
{
    private const LOCALE_TO_TRANSLITERATOR_ID = array('am' => 'Amharic-Latin', 'ar' => 'Arabic-Latin', 'az' => 'Azerbaijani-Latin', 'be' => 'Belarusian-Latin', 'bg' => 'Bulgarian-Latin', 'bn' => 'Bengali-Latin', 'de' => 'de-ASCII', 'el' => 'Greek-Latin', 'fa' => 'Persian-Latin', 'he' => 'Hebrew-Latin', 'hy' => 'Armenian-Latin', 'ka' => 'Georgian-Latin', 'kk' => 'Kazakh-Latin', 'ky' => 'Kirghiz-Latin', 'ko' => 'Korean-Latin', 'mk' => 'Macedonian-Latin', 'mn' => 'Mongolian-Latin', 'or' => 'Oriya-Latin', 'ps' => 'Pashto-Latin', 'ru' => 'Russian-Latin', 'sr' => 'Serbian-Latin', 'sr_Cyrl' => 'Serbian-Latin', 'th' => 'Thai-Latin', 'tk' => 'Turkmen-Latin', 'uk' => 'Ukrainian-Latin', 'uz' => 'Uzbek-Latin', 'zh' => 'Han-Latin');
    /**
     * @var (string | null) $defaultLocale
     */
    private $defaultLocale;
    /**
     * @var (\Closure | array) $symbolsMap
     */
    private $symbolsMap = ['en' => ['@' => 'at', '&' => 'and']];
    /**
     * Cache of transliterators per locale.
     *
     * @var \Transliterator[]
     */
    private $transliterators = [];
    /**
     * @param (array | \Closure | null) $symbolsMap
     */
    public function __construct(string $defaultLocale = NULL, $symbolsMap = NULL)
    {
        if (!(\is_array($symbolsMap) || $symbolsMap instanceof \Closure || \is_null($symbolsMap))) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($symbolsMap) must be of type ?Closure|array, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($symbolsMap) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        $this->defaultLocale = $defaultLocale;
        $this->symbolsMap = $symbolsMap ?? $this->symbolsMap;
    }
    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale)
    {
        $this->defaultLocale = $locale;
    }
    /**
     * {@inheritdoc}
     */
    public function getLocale() : string
    {
        return $this->defaultLocale;
    }
    /**
     * {@inheritdoc}
     */
    public function slug(string $string, string $separator = '-', string $locale = NULL) : AbstractUnicodeString
    {
        $locale = $locale ?? $this->defaultLocale;
        $transliterator = [];
        if ($locale && ('de' === $locale || \str_starts_with($locale, 'de_'))) {
            // Use the shortcut for German in UnicodeString::ascii() if possible (faster and no requirement on intl)
            $transliterator = ['de-ASCII'];
        } elseif (\function_exists('transliterator_transliterate') && $locale) {
            $transliterator = (array) $this->createTransliterator($locale);
        }
        if ($this->symbolsMap instanceof \Closure) {
            // If the symbols map is passed as a closure, there is no need to fallback to the parent locale
            // as the closure can just provide substitutions for all locales of interest.
            $symbolsMap = $this->symbolsMap;
            \Phabel\Target\Php73\Polyfill::array_unshift($transliterator, static function ($s) use($symbolsMap, $locale) {
                return $symbolsMap($s, $locale);
            });
        }
        $unicodeString = (new UnicodeString($string))->ascii($transliterator);
        if (\is_array($this->symbolsMap)) {
            $map = null;
            if (isset($this->symbolsMap[$locale])) {
                $map = $this->symbolsMap[$locale];
            } else {
                $parent = self::getParentLocale($locale);
                if ($parent && isset($this->symbolsMap[$parent])) {
                    $map = $this->symbolsMap[$parent];
                }
            }
            if ($map) {
                foreach ($map as $char => $replace) {
                    $unicodeString = $unicodeString->replace($char, ' ' . $replace . ' ');
                }
            }
        }
        return $unicodeString->replaceMatches('/[^A-Za-z0-9]++/', $separator)->trim($separator);
    }
    /**
     *
     */
    private function createTransliterator(string $locale) : ?\Transliterator
    {
        if (\array_key_exists($locale, $this->transliterators)) {
            return $this->transliterators[$locale];
        }
        // Exact locale supported, cache and return
        if ($id = self::LOCALE_TO_TRANSLITERATOR_ID[$locale] ?? null) {
            return $this->transliterators[$locale] = \Transliterator::create($id . '/BGN') ?? \Transliterator::create($id);
        }
        // Locale not supported and no parent, fallback to any-latin
        if (!($parent = self::getParentLocale($locale))) {
            return $this->transliterators[$locale] = null;
        }
        // Try to use the parent locale (ie. try "de" for "de_AT") and cache both locales
        if ($id = self::LOCALE_TO_TRANSLITERATOR_ID[$parent] ?? null) {
            $transliterator = \Transliterator::create($id . '/BGN') ?? \Transliterator::create($id);
        }
        return $this->transliterators[$locale] = $this->transliterators[$parent] = $transliterator ?? null;
    }
    /**
     *
     */
    private static function getParentLocale(?string $locale) : ?string
    {
        if (!$locale) {
            return null;
        }
        if (\false === ($str = \strrchr($locale, '_'))) {
            // no parent locale
            return null;
        }
        return \Phabel\Target\Php80\Polyfill::substr($locale, 0, -\strlen($str));
    }
}