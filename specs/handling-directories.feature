@plugins @directories
Feature: Directory Manipulation Plugin
  In order to create, move, copy, or delete directories
  Developers should be able to use the 'directories' plugin
  simple API to do their job.

  Background:
    Given the directory "amaka-tests" exists in the system temp directory
    And the directory "amaka-tests" is the plugin working directory

  Scenario: Creating a new directory
    Given the directory "new-dir" doesn't exist
    When the developer calls the "create" method with "new-dir"
    Then the directory "new-dir" exists

  Scenario: removing a empty directory
    Given the developer calls the "create" method with "empty-dir"
    And the directory "empty-dir" exists
    When the developer calls the "remove" method with "empty-dir"
    Then the directory "empty-dir" doesn't exist

  Scenario: removing a directory
    Given the developer calls the "create" method with "example"
    And the developer calls the "create" method with "example/a"
    And the developer calls the "create" method with "example/b"
    And the developer calls the "create" method with "example/b/c"
    When the developer calls the "remove" method with "example"
    Then the directory "example" doesn't exist

  Scenario: copying a directory
    Given the developer calls the "create" method with "example"

  Scenario: moving or renaming a empty directory
    Given the developer calls the "create" method with "source"
    And the directory "source" exists
    And the directory "dest" doesn't exist
    When the developer calls the "move" method with "source" and "dest"
    Then the directory "dest" exists
    And the directory "source" doesn't exist

  Scenario: moving or renaming a directory
    Given the developer calls the "create" method with "source"
      And the developer calls the "create" method with "source/a"
      And the developer calls the "create" method with "source/b"
      And the developer calls the "create" method with "source/b/c"
    When the developer calls the "move" method with "source" and "dest"
    Then the directory "dest" exists
     And the directory "dest/a" exists
     And the directory "dest/b" exists
     And the directory "dest/b/c" exists
     And the directory "source" doesn't exist

  Scenario: moving into existing directory
    Given the developer calls the "create" method with "dest"
    And the developer calls the "create" method with "dest/a"
    And the developer calls the "create" method with "source"
    And the developer calls the "create" method with "source/a"
    And the developer calls the "create" method with "source/c"
    And the developer calls the "create" method with "source/a/b"
    When the developer calls the "move" method with "source" and "dest"
    Then the directory "dest/a" exists
    And the directory "dest/a/b" exists
    And the directory "dest/c" exists
