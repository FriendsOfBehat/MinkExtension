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
    /**
     * Opens homepage.
     * Example: Given I am on "/"
     * Example: When I go to "/"
     * Example: And I go to "/".
     */
    #[\Behat\Step\Given('/^(?:|I )am on (?:|the )homepage$/')]
    #[\Behat\Step\When('/^(?:|I )go to (?:|the )homepage$/')]
    public function iAmOnHomepage(): void
    {
        $this->visitPath('/');
    }

    /**
     * Opens specified page.
     * Example: Given I am on "http://batman.com"
     * Example: And I am on "/articles/isBatmanBruceWayne"
     * Example: When I go to "/articles/isBatmanBruceWayne".
     */
    #[\Behat\Step\Given('/^(?:|I )am on "(?P<page>[^"]+)"$/')]
    #[\Behat\Step\When('/^(?:|I )go to "(?P<page>[^"]+)"$/')]
    public function visit(string $page): void
    {
        $this->visitPath($page);
    }

    /**
     * Reloads current page.
     * Example: When I reload the page
     * Example: And I reload the page.
     */
    #[\Behat\Step\When('/^(?:|I )reload the page$/')]
    public function reload(): void
    {
        $this->getSession()->reload();
    }

    /**
     * Moves backward one page in history.
     * Example: When I move backward one page.
     */
    #[\Behat\Step\When('/^(?:|I )move backward one page$/')]
    public function back(): void
    {
        $this->getSession()->back();
    }

    /**
     * Moves forward one page in history.
     * Example: And I move forward one page.
     */
    #[\Behat\Step\When('/^(?:|I )move forward one page$/')]
    public function forward(): void
    {
        $this->getSession()->forward();
    }

    /**
     * Presses button with specified id|name|title|alt|value.
     * Example: When I press "Log In"
     * Example: And I press "Log In".
     */
    #[\Behat\Step\When('/^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/')]
    public function pressButton(string $button): void
    {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * Clicks link with specified id|title|alt|text.
     * Example: When I follow "Log In"
     * Example: And I follow "Log In".
     */
    #[\Behat\Step\When('/^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/')]
    public function clickLink(string $link): void
    {
        $link = $this->fixStepArgument($link);
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     * Example: When I fill in "username" with: "bwayne"
     * Example: And I fill in "bwayne" for "username".
     */
    #[\Behat\Step\When('/^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/')]
    #[\Behat\Step\When('/^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with:$/')]
    #[\Behat\Step\When('/^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/')]
    public function fillField(string $field, string $value): void
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * Fills in form fields with provided table.
     * Example: When I fill in the following:
     *              | username | bruceWayne   |
     *              | password | iLoveBats123 |.
     */
    #[\Behat\Step\When('/^(?:|I )fill in the following:$/')]
    public function fillFields(TableNode $fields): void
    {
        foreach ($fields->getRowsHash() as $field => $value) {
            $this->fillField((string) $field, is_array($value) ? implode(',', $value) : $value);
        }
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     * Example: When I select "Bats" from "user_fears"
     * Example: And I select "Bats" from "user_fears".
     */
    #[\Behat\Step\When('/^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/')]
    public function selectOption(string $select, string $option): void
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * Selects additional option in select field with specified id|name|label|value.
     * Example: When I additionally select "Deceased" from "parents_alive_status"
     * Example: And I additionally select "Deceased" from "parents_alive_status".
     */
    #[\Behat\Step\When('/^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/')]
    public function additionallySelectOption(string $select, string $option): void
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     * Example: When I check "Pearl Necklace"
     * Example: And I check "Pearl Necklace".
     */
    #[\Behat\Step\When('/^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/')]
    public function checkOption(string $option): void
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->checkField($option);
    }

    /**
     * Unchecks checkbox with specified id|name|label|value.
     * Example: When I uncheck "Broadway Plays"
     * Example: And I uncheck "Broadway Plays".
     */
    #[\Behat\Step\When('/^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/')]
    public function uncheckOption(string $option): void
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->uncheckField($option);
    }

    /**
     * Attaches file to field with specified id|name|label|value.
     * Example: When I attach the file "bwayne_profile.png" to "profileImageUpload"
     * Example: And I attach the file "bwayne_profile.png" to "profileImageUpload".
     */
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

    /**
     * Checks that current page PATH is equal to specified.
     * Example: Then I should be on "/"
     * Example: And I should be on "/bats"
     * Example: And I should be on "http://google.com".
     */
    #[\Behat\Step\Then('/^(?:|I )should be on "(?P<page>[^"]+)"$/')]
    public function assertPageAddress(string $page): void
    {
        $this->assertSession()->addressEquals($this->locatePath($page));
    }

    /**
     * Checks that current page is the homepage.
     * Example: Then I should be on the homepage
     * Example: And I should be on the homepage.
     */
    #[\Behat\Step\Then('/^(?:|I )should be on (?:|the )homepage$/')]
    public function assertHomepage(): void
    {
        $this->assertSession()->addressEquals($this->locatePath('/'));
    }

    /**
     * Checks that current page PATH matches regular expression.
     * Example: Then the url should match "superman is dead"
     * Example: And the url should match "log in".
     */
    #[\Behat\Step\Then('/^the (?i)url(?-i) should match (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertUrlRegExp(string $pattern): void
    {
        $this->assertSession()->addressMatches($this->fixStepArgument($pattern));
    }

    /**
     * Checks that current page response status is equal to specified.
     * Example: Then the response status code should be 200
     * Example: And the response status code should be 400.
     */
    #[\Behat\Step\Then('/^the response status code should be (?P<code>\d+)$/')]
    public function assertResponseStatus(string $code): void
    {
        $this->assertSession()->statusCodeEquals((int) $code);
    }

    /**
     * Checks that current page response status is not equal to specified.
     * Example: Then the response status code should not be 501
     * Example: And the response status code should not be 404.
     */
    #[\Behat\Step\Then('/^the response status code should not be (?P<code>\d+)$/')]
    public function assertResponseStatusIsNot(string $code): void
    {
        $this->assertSession()->statusCodeNotEquals((int) $code);
    }

    /**
     * Checks that page contains specified text.
     * Example: Then I should see "Who is the Batman?"
     * Example: And I should see "Who is the Batman?".
     */
    #[\Behat\Step\Then('/^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertPageContainsText(string $text): void
    {
        $this->assertSession()->pageTextContains($this->fixStepArgument($text));
    }

    /**
     * Checks that page doesn't contain specified text.
     * Example: Then I should not see "Batman is Bruce Wayne"
     * Example: And I should not see "Batman is Bruce Wayne".
     */
    #[\Behat\Step\Then('/^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertPageNotContainsText(string $text): void
    {
        $this->assertSession()->pageTextNotContains($this->fixStepArgument($text));
    }

    /**
     * Checks that page contains text matching specified pattern.
     * Example: Then I should see text matching "Batman, the vigilante"
     * Example: And I should not see "Batman, the vigilante".
     */
    #[\Behat\Step\Then('/^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertPageMatchesText(string $pattern): void
    {
        $this->assertSession()->pageTextMatches($this->fixStepArgument($pattern));
    }

    /**
     * Checks that page doesn't contain text matching specified pattern.
     * Example: Then I should not see text matching "Bruce Wayne, the vigilante"
     * Example: And I should not see "Bruce Wayne, the vigilante".
     */
    #[\Behat\Step\Then('/^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*")$/')]
    public function assertPageNotMatchesText(string $pattern): void
    {
        $this->assertSession()->pageTextNotMatches($this->fixStepArgument($pattern));
    }

    /**
     * Checks that HTML response contains specified string.
     * Example: Then the response should contain "Batman is the hero Gotham deserves."
     * Example: And the response should contain "Batman is the hero Gotham deserves.".
     */
    #[\Behat\Step\Then('/^the response should contain "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertResponseContains(string $text): void
    {
        $this->assertSession()->responseContains($this->fixStepArgument($text));
    }

    /**
     * Checks that HTML response doesn't contain specified string.
     * Example: Then the response should not contain "Bruce Wayne is a billionaire, play-boy, vigilante."
     * Example: And the response should not contain "Bruce Wayne is a billionaire, play-boy, vigilante.".
     */
    #[\Behat\Step\Then('/^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/')]
    public function assertResponseNotContains(string $text): void
    {
        $this->assertSession()->responseNotContains($this->fixStepArgument($text));
    }

    /**
     * Checks that element with specified CSS contains specified text.
     * Example: Then I should see "Batman" in the "heroes_list" element
     * Example: And I should see "Batman" in the "heroes_list" element.
     */
    #[\Behat\Step\Then('/^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/')]
    public function assertElementContainsText(string $element, string $text): void
    {
        $this->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));
    }

    /**
     * Checks that element with specified CSS doesn't contain specified text.
     * Example: Then I should not see "Bruce Wayne" in the "heroes_alter_egos" element
     * Example: And I should not see "Bruce Wayne" in the "heroes_alter_egos" element.
     */
    #[\Behat\Step\Then('/^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/')]
    public function assertElementNotContainsText(string $element, string $text): void
    {
        $this->assertSession()->elementTextNotContains('css', $element, $this->fixStepArgument($text));
    }

    /**
     * Checks that element with specified CSS contains specified HTML.
     * Example: Then the "body" element should contain "style=\"color:black;\""
     * Example: And the "body" element should contain "style=\"color:black;\"".
     */
    #[\Behat\Step\Then('/^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertElementContains(string $element, string $value): void
    {
        $this->assertSession()->elementContains('css', $element, $this->fixStepArgument($value));
    }

    /**
     * Checks that element with specified CSS doesn't contain specified HTML.
     * Example: Then the "body" element should not contain "style=\"color:black;\""
     * Example: And the "body" element should not contain "style=\"color:black;\"".
     */
    #[\Behat\Step\Then('/^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertElementNotContains(string $element, string $value): void
    {
        $this->assertSession()->elementNotContains('css', $element, $this->fixStepArgument($value));
    }

    /**
     * Checks that element with specified CSS exists on page.
     * Example: Then I should see a "body" element
     * Example: And I should see a "body" element.
     */
    #[\Behat\Step\Then('/^(?:|I )should see an? "(?P<element>[^"]*)" element$/')]
    public function assertElementOnPage(string $element): void
    {
        $this->assertSession()->elementExists('css', $element);
    }

    /**
     * Checks that element with specified CSS doesn't exist on page.
     * Example: Then I should not see a "canvas" element
     * Example: And I should not see a "canvas" element.
     */
    #[\Behat\Step\Then('/^(?:|I )should not see an? "(?P<element>[^"]*)" element$/')]
    public function assertElementNotOnPage(string $element): void
    {
        $this->assertSession()->elementNotExists('css', $element);
    }

    /**
     * Checks that form field with specified id|name|label|value has specified value.
     * Example: Then the "username" field should contain "bwayne"
     * Example: And the "username" field should contain "bwayne".
     */
    #[\Behat\Step\Then('/^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertFieldContains(string $field, string $value): void
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueEquals($field, $value);
    }

    /**
     * Checks that form field with specified id|name|label|value doesn't have specified value.
     * Example: Then the "username" field should not contain "batman"
     * Example: And the "username" field should not contain "batman".
     */
    #[\Behat\Step\Then('/^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/')]
    public function assertFieldNotContains(string $field, string $value): void
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueNotEquals($field, $value);
    }

    /**
     * Checks that (?P<num>\d+) CSS elements exist on the page.
     * Example: Then I should see 5 "div" elements
     * Example: And I should see 5 "div" elements.
     */
    #[\Behat\Step\Then('/^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/')]
    public function assertNumElements(string $num, string $element): void
    {
        $this->assertSession()->elementsCount('css', $element, intval($num));
    }

    /**
     * Checks that checkbox with specified id|name|label|value is checked.
     * Example: Then the "remember_me" checkbox should be checked
     * Example: And the "remember_me" checkbox is checked.
     */
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/')]
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is checked$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked$/')]
    public function assertCheckboxChecked(string $checkbox): void
    {
        $this->assertSession()->checkboxChecked($this->fixStepArgument($checkbox));
    }

    /**
     * Checks that checkbox with specified id|name|label|value is unchecked.
     * Example: Then the "newsletter" checkbox should be unchecked
     * Example: Then the "newsletter" checkbox should not be checked
     * Example: And the "newsletter" checkbox is unchecked.
     */
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should (?:be unchecked|not be checked)$/')]
    #[\Behat\Step\Then('/^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is (?:unchecked|not checked)$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked)$/')]
    #[\Behat\Step\Then('/^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked)$/')]
    public function assertCheckboxNotChecked(string $checkbox): void
    {
        $this->assertSession()->checkboxNotChecked($this->fixStepArgument($checkbox));
    }

    /**
     * Prints current URL to console.
     * Example: Then print current URL
     * Example: And print current URL.
     */
    #[\Behat\Step\Then('/^print current URL$/')]
    public function printCurrentUrl(): void
    {
        echo $this->getSession()->getCurrentUrl();
    }

    /**
     * Prints last response to console.
     * Example: Then print last response
     * Example: And print last response.
     */
    #[\Behat\Step\Then('/^print last response$/')]
    public function printLastResponse(): void
    {
        echo $this->getSession()->getCurrentUrl()."\n\n".
            $this->getSession()->getPage()->getContent()
        ;
    }

    /**
     * Opens last response content in browser.
     * Example: Then show last response
     * Example: And show last response.
     */
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
