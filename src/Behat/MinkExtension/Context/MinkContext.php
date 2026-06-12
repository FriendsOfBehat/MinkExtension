<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Context;

use Behat\Behat\Context\TranslatableContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Mink context for Behat BDD tool.
 * Provides Mink integration and base step definitions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkContext extends RawMinkContext implements TranslatableContext
{
    #[\Behat\Step\Given('/^(?:|I )am on (?:|the )homepage$/')]
    #[\Behat\Step\When('/^(?:|I )go to (?:|the )homepage$/')]
    public function iAmOnHomepage(): void
    {
        $this->visitPath('/');
    }

    #[\Behat\Step\Given('/^(?:|I )am on "(?P<page>[^"]+)"$/')]
    #[\Behat\Step\When('/^(?:|I )go to "(?P<page>[^"]+)"$/')]
    public function visit(string $page): void
    {
        $this->visitPath($page);
    }

    #[\Behat\Step\When('/^(?:|I )reload the page$/')]
    public function reload(): void
    {
        $this->getSession()->reload();
    }

    #[\Behat\Step\When('/^(?:|I )move backward one page$/')]
    public function back(): void
    {
        $this->getSession()->back();
    }

    #[\Behat\Step\When('/^(?:|I )move forward one page$/')]
    public function forward(): void
    {
        $this->getSession()->forward();
    }

    #[\Behat\Step\When('/^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/')]
    public function pressButton(string $button): void
    {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }

    #[\Behat\Step\When('/^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/')]
    public function clickLink(string $link): void
    {
        $link = $this->fixStepArgument($link);
        $this->getSession()->getPage()->clickLink($link);
    }

    #[\Behat\Step\When('/^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/')]
    #[\Behat\Step\When('/^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with:$/')]
    #[\Behat\Step\When('/^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/')]
    public function fillField(string $field, string $value): void
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    #[\Behat\Step\When('/^(?:|I )fill in the following:$/')]
    public function fillFields(TableNode $fields): void
    {
        foreach ($fields->getRowsHash() as $field => $value) {
            $this->fillField((string) $field, is_array($value) ? implode(',', $value) : $value);
        }
    }

    #[\Behat\Step\When('/^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/')]
    public function selectOption(string $select, string $option): void
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    #[\Behat\Step\When('/^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/')]
    public function additionallySelectOption(string $select, string $option): void
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
    }

    #[\Behat\Step\When('/^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/')]
    public function checkOption(string $option): void
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->checkField($option);
    }

    #[\Behat\Step\When('/^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/')]
    public function uncheckOption(string $option): void
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->uncheckField($option);
    }

    #[\Behat\Step\When('/^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/')]
    public function attachFileToField(string $field, string $path): void
    {
        $field = $this->fixStepArgument($field);

        $filesPath = $this->getMinkParameter('files_path');
        if (is_string($filesPath) && '' !== $filesPath) {
            $realFilesPath = realpath($filesPath);
            if (false !== $realFilesPath) {
                $fullPath = rtrim($realFilesPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
                if (is_file($fullPath)) {
                    $path = $fullPath;
                }
            }
        }

        $this->getSession()->getPage()->attachFileToField($field, $path);
    }

    #[\Behat\Step\Then('/^(?:|I )should be on "(?P<page>[^"]+)"$/')]
    public function assertPageAddress(string $page): void
    {
        $this->assertSession()->addressEquals($this->locatePath($page));
    }

    #[\Behat\Step\Then('/^(?:|I )should be on (?:|the )homepage$/')]
    public function assertHomepage(): void
    {
        $this->assertSession()->addressEquals($this->locatePath('/'));
    }

    #[\Behat\Step\Then('/^the (?i)url(?-i) should match (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertUrlRegExp(string $pattern): void
    {
        $this->assertSession()->addressMatches($this->fixStepArgument($pattern));
    }

    #[\Behat\Step\Then('/^the response status code should be (?P<code>\d+)$/')]
    public function assertResponseStatus(string $code): void
    {
        $this->assertSession()->statusCodeEquals((int) $code);
    }

    #[\Behat\Step\Then('/^the response status code should not be (?P<code>\d+)$/')]
    public function assertResponseStatusIsNot(string $code): void
    {
        $this->assertSession()->statusCodeNotEquals((int) $code);
    }

    #[\Behat\Step\Then('/^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertPageContainsText(string $text): void
    {
        $this->assertSession()->pageTextContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertPageNotContainsText(string $text): void
    {
        $this->assertSession()->pageTextNotContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertPageMatchesText(string $pattern): void
    {
        $this->assertSession()->pageTextMatches($this->fixStepArgument($pattern));
    }

    #[\Behat\Step\Then('/^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertPageNotMatchesText(string $pattern): void
    {
        $this->assertSession()->pageTextNotMatches($this->fixStepArgument($pattern));
    }

    #[\Behat\Step\Then('/^the response should contain "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertResponseContains(string $text): void
    {
        $this->assertSession()->responseContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertResponseNotContains(string $text): void
    {
        $this->assertSession()->responseNotContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/')]
    public function assertElementContainsText(string $element, string $text): void
    {
        $this->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/')]
    public function assertElementNotContainsText(string $element, string $text): void
    {
        $this->assertSession()->elementTextNotContains('css', $element, $this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertElementContains(string $element, string $value): void
    {
        $this->assertSession()->elementContains('css', $element, $this->fixStepArgument($value));
    }

    #[\Behat\Step\Then('/^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertElementNotContains(string $element, string $value): void
    {
        $this->assertSession()->elementNotContains('css', $element, $this->fixStepArgument($value));
    }

    #[\Behat\Step\Then('/^(?:|I )should see an? "(?P<element>[^"]*)" element$/')]
    public function assertElementOnPage(string $element): void
    {
        $this->assertSession()->elementExists('css', $element);
    }

    #[\Behat\Step\Then('/^(?:|I )should not see an? "(?P<element>[^"]*)" element$/')]
    public function assertElementNotOnPage(string $element): void
    {
        $this->assertSession()->elementNotExists('css', $element);
    }

    #[\Behat\Step\Then('/^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertFieldContains(string $field, string $value): void
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueEquals($field, $value);
    }

    #[\Behat\Step\Then('/^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertFieldNotContains(string $field, string $value): void
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueNotEquals($field, $value);
    }

    #[\Behat\Step\Then('/^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/')]
    public function assertNumElements(string $num, string $element): void
    {
        $this->assertSession()->elementsCount('css', $element, intval($num));
    }

    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/')]
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is checked$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked$/')]
    public function assertCheckboxChecked(string $checkbox): void
    {
        $this->assertSession()->checkboxChecked($this->fixStepArgument($checkbox));
    }

    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should (?:be unchecked|not be checked)$/')]
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is (?:unchecked|not checked)$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked)$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked)$/')]
    public function assertCheckboxNotChecked(string $checkbox): void
    {
        $this->assertSession()->checkboxNotChecked($this->fixStepArgument($checkbox));
    }

    #[\Behat\Step\Then('/^print current URL$/')]
    public function printCurrentUrl(): void
    {
        echo $this->getSession()->getCurrentUrl();
    }

    #[\Behat\Step\Then('/^print last response$/')]
    public function printLastResponse(): void
    {
        echo $this->getSession()->getCurrentUrl()."\n\n".
            $this->getSession()->getPage()->getContent()
        ;
    }

    #[\Behat\Step\Then('/^show last response$/')]
    public function showLastResponse(): void
    {
        $showCmd = $this->getMinkParameter('show_cmd');
        if (null === $showCmd) {
            throw new \RuntimeException('Set "show_cmd" parameter in behat.yml to be able to open page in browser (ex.: "show_cmd: firefox %s")');
        }

        $showTmpDir = $this->getMinkParameter('show_tmp_dir');
        $filename = rtrim(is_string($showTmpDir) ? $showTmpDir : sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.uniqid().'.html';
        file_put_contents($filename, $this->getSession()->getPage()->getContent());
        system(sprintf(is_string($showCmd) ? $showCmd : '', escapeshellarg($filename)));
    }

    /**
     * @return string[]
     */
    public static function getTranslationResources(): array
    {
        return self::getMinkTranslationResources();
    }

    /**
     * @return string[]
     */
    public static function getMinkTranslationResources(): array
    {
        return glob(__DIR__.'/../../../../i18n/*.xliff') ?: [];
    }

    protected function fixStepArgument(string $argument): string
    {
        return str_replace('\\"', '"', $argument);
    }
}
