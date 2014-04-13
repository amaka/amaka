Feature: Amaka error handling and Reporting
  In order to understand what Amaka is doing internally
  As a end-user
  I want to be told what the error was and what to do next

  Background:
    Given amaka executable is in "bin/amaka"
    And the current working directory is "%system.temp%"

    Scenario: Run amaka, default Amkfile in working directory
      Given the amaka script "Amkfile" contains
      """
      <?php
          return [];
      """
      When I run amaka with arguments ""
      And the output on the screen should match "@(No task to run)@"
      Then the program exit status should be non-zero

    Scenario: Run amaka without arguments, no Amkfile in the working directory
      Given I run amaka with arguments ""
      Then the output on the screen should match "@Amaka script '([^']*)Amkfile' not found*@s"
      And the program exit status should be non-zero

    Scenario: Run amaka, path to bogus Amkfile specified
      Given I run amaka with arguments "-f bogus"
      Then the output on the screen should match "@Amaka script '([^']*)' not found*@s"
      And the program exit status should be non-zero
