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

  Scenario: removing a empty directory
    Given the developer calls the "create" method with "empty-dir"
    And the directory "empty-dir" is created
    When the developer calls the "remove" method with "empty-dir"
    Then the directory "empty-dir" is removed

  Scenario: removing a non-empty directory
    Given the developer calls the "create" method with "example"
    And the developer calls the "create" method with "example/a"
    And the developer calls the "create" method with "example/b"
    And the developer calls the "create" method with "example/b/c"
    When the developer calls the "remove" method with "example"
    Then the directory "example" is removed

  Scenario: moving (or renaming) a empty directory
    Given the developer calls the "create" method with "source"
    And the directory "dest" doesn't exist
    When the developer calls the "move" method with "source" and "dest"
    Then the directory "dest" is created
    And the directory "source" is removed
