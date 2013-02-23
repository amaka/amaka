@plugins @directories
Feature: handling directories
  In order to create, move, copy, or delete directories
  Developers should be able to use the 'directories' plugin
  simple API to do their job.

  Background:
    Given the directory "amaka-tests" exists
    And the test script is run in the system temporary directory
    And the directory "amaka-tests" is the plugin working directory

  Scenario: Creating a new directory
    Given the directory "new-dir" doesn't exist
    When the developer calls the "create" method with "new-dir"
    Then the directory "new-dir" is created
