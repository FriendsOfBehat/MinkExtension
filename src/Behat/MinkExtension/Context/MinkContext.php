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
    public function iAmOnHomepage()
    {
        $this->visitPath('/');
    }

    #[\Behat\Step\Given('/^(?:|I )am on "(?P<page>[^"]+)"$/')]
    #[\Behat\Step\When('/^(?:|I )go to "(?P<page>[^"]+)"$/')]
    public function visit($page)
    {
        $this->visitPath($page);
    }

    #[\Behat\Step\When('/^(?:|I )reload the page$/')]
    public function reload()
    {
        $this->getSession()->reload();
    }

    #[\Behat\Step\When('/^(?:|I )move backward one page$/')]
    public function back()
    {
        $this->getSession()->back();
    }

    #[\Behat\Step\When('/^(?:|I )move forward one page$/')]
    public function forward()
    {
        $this->getSession()->forward();
    }

    #[\Behat\Step\When('/^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/')]
    public function pressButton($button)
    {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }

    #[\Behat\Step\When('/^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/')]
    public function clickLink($link)
    {
        $link = $this->fixStepArgument($link);
        $this->getSession()->getPage()->clickLink($link);
    }

    #[\Behat\Step\When('/^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/')]
    #[\Behat\Step\When('/^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with:$/')]
    #[\Behat\Step\When('/^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/')]
    public function fillField($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    #[\Behat\Step\When('/^(?:|I )fill in the following:$/')]
    public function fillFields(TableNode $fields)
    {
        foreach ($fields->getRowsHash() as $field => $value) {
            $this->fillField($field, $value);
        }
    }

    #[\Behat\Step\When('/^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/')]
    public function selectOption($select, $option)
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    #[\Behat\Step\When('/^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/')]
    public function additionallySelectOption($select, $option)
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
    }

    #[\Behat\Step\When('/^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/')]
    public function checkOption($option)
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->checkField($option);
    }

    #[\Behat\Step\When('/^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/')]
    public function uncheckOption($option)
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->uncheckField($option);
    }

    #[\Behat\Step\When('/^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/')]
    public function attachFileToField($field, $path)
    {
        $field = $this->fixStepArgument($field);

        if ($this->getMinkParameter('files_path')) {
            $fullPath = rtrim(realpath($this->getMinkParameter('files_path')), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
            if (is_file($fullPath)) {
                $path = $fullPath;
            }
        }

        $this->getSession()->getPage()->attachFileToField($field, $path);
    }

    #[\Behat\Step\Then('/^(?:|I )should be on "(?P<page>[^"]+)"$/')]
    public function assertPageAddress($page)
    {
        $this->assertSession()->addressEquals($this->locatePath($page));
    }

    #[\Behat\Step\Then('/^(?:|I )should be on (?:|the )homepage$/')]
    public function assertHomepage()
    {
        $this->assertSession()->addressEquals($this->locatePath('/'));
    }

    #[\Behat\Step\Then('/^the (?i)url(?-i) should match (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertUrlRegExp($pattern)
    {
        $this->assertSession()->addressMatches($this->fixStepArgument($pattern));
    }

    #[\Behat\Step\Then('/^the response status code should be (?P<code>\d+)$/')]
    public function assertResponseStatus($code)
    {
        $this->assertSession()->statusCodeEquals($code);
    }

    #[\Behat\Step\Then('/^the response status code should not be (?P<code>\d+)$/')]
    public function assertResponseStatusIsNot($code)
    {
        $this->assertSession()->statusCodeNotEquals($code);
    }

    #[\Behat\Step\Then('/^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertPageContainsText($text)
    {
        $this->assertSession()->pageTextContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertPageNotContainsText($text)
    {
        $this->assertSession()->pageTextNotContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertPageMatchesText($pattern)
    {
        $this->assertSession()->pageTextMatches($this->fixStepArgument($pattern));
    }

    #[\Behat\Step\Then('/^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertPageNotMatchesText($pattern)
    {
        $this->assertSession()->pageTextNotMatches($this->fixStepArgument($pattern));
    }

    #[\Behat\Step\Then('/^the response should contain "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertResponseContains($text)
    {
        $this->assertSession()->responseContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertResponseNotContains($text)
    {
        $this->assertSession()->responseNotContains($this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/')]
    public function assertElementContainsText($element, $text)
    {
        $this->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/')]
    public function assertElementNotContainsText($element, $text)
    {
        $this->assertSession()->elementTextNotContains('css', $element, $this->fixStepArgument($text));
    }

    #[\Behat\Step\Then('/^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertElementContains($element, $value)
    {
        $this->assertSession()->elementContains('css', $element, $this->fixStepArgument($value));
    }

    #[\Behat\Step\Then('/^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertElementNotContains($element, $value)
    {
        $this->assertSession()->elementNotContains('css', $element, $this->fixStepArgument($value));
    }

    #[\Behat\Step\Then('/^(?:|I )should see an? "(?P<element>[^"]*)" element$/')]
    public function assertElementOnPage($element)
    {
        $this->assertSession()->elementExists('css', $element);
    }

    #[\Behat\Step\Then('/^(?:|I )should not see an? "(?P<element>[^"]*)" element$/')]
    public function assertElementNotOnPage($element)
    {
        $this->assertSession()->elementNotExists('css', $element);
    }

    #[\Behat\Step\Then('/^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertFieldContains($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueEquals($field, $value);
    }

    #[\Behat\Step\Then('/^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertFieldNotContains($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueNotEquals($field, $value);
    }

    #[\Behat\Step\Then('/^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/')]
    public function assertNumElements($num, $element)
    {
        $this->assertSession()->elementsCount('css', $element, intval($num));
    }

    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/')]
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is checked$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked$/')]
    public function assertCheckboxChecked($checkbox)
    {
        $this->assertSession()->checkboxChecked($this->fixStepArgument($checkbox));
    }

    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should (?:be unchecked|not be checked)$/')]
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is (?:unchecked|not checked)$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked)$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked)$/')]
    public function assertCheckboxNotChecked($checkbox)
    {
        $this->assertSession()->checkboxNotChecked($this->fixStepArgument($checkbox));
    }

    #[\Behat\Step\Then('/^print current URL$/')]
    public function printCurrentUrl()
    {
        echo $this->getSession()->getCurrentUrl();
    }

    #[\Behat\Step\Then('/^print last response$/')]
    public function printLastResponse()
    {
        echo (
            $this->getSession()->getCurrentUrl()."\n\n".
            $this->getSession()->getPage()->getContent()
        );
    }

    #[\Behat\Step\Then('/^show last response$/')]
    public function showLastResponse()
    {
        if (null === $this->getMinkParameter('show_cmd')) {
            throw new \RuntimeException('Set "show_cmd" parameter in behat.yml to be able to open page in browser (ex.: "show_cmd: firefox %s")');
        }

        $filename = rtrim($this->getMinkParameter('show_tmp_dir'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.uniqid().'.html';
        file_put_contents($filename, $this->getSession()->getPage()->getContent());
        system(sprintf($this->getMinkParameter('show_cmd'), escapeshellarg($filename)));
    }

    /**
     * @return array
     */
    public static function getTranslationResources()
    {
        return self::getMinkTranslationResources();
    }

    /**
     * @return array
     */
    public static function getMinkTranslationResources()
    {
        return glob(__DIR__.'/../../../../i18n/*.xliff');
    }

    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }
}
