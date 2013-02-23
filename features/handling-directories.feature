Feature: handling directories
  when writing build scripts it's often necessary to move
  directories from one place to the other.

  Scenario: a working directory must be defined before using the plugin
    Given an instance of the plugin in "null"
    Then the working directory should be "null"
    And calling the "create" should throw
    And calling the "remove" should throw

  Scenario: creating a directory
    Given an instance of the plugin in "amaka-tests"
    When the "create" method is called with "empty-dir"
    Then the directory "amaka-tests/empty-dir" should exist
