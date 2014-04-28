@amaka-core @error-reporting
Feature: Amaka Error Reporting
  As a Developer I want to inform the Amaka end-user of some error
  that has occurred during execution.  In order to provide the
  end-user with friendly and consistent error messages.

  Scenario: Generating and printing errors
    Given that "Example error." is encountered
    And this error has a message that says
    """
    Something went unexpectedly wrong.
    """
    When the error is printed
    Then the output on the screen should be
    """
    Error   : Example error.
    Message : Something went unexpectedly wrong.
    """

  Scenario: Generating and printing errors with resolutions
    Given that "Example error." is encountered
    When this error has a resolution saying "Perhaps you missed a letter?"
    And this error has a resolution saying "Have you considered RTFM?"
    And the error is printed
    Then the output on the screen should be
    """
    Error   : Example error.

    Troubleshooting:
    - Perhaps you missed a letter?
    - Have you considered RTFM?
    """

  Scenario: Generating and printing errors with longer resolutions messages
    Given that "Example error." is encountered
    When this error has a resolution saying "This problem needs a longer description" that says
    """
    Ideally this would be a long message.
    """
    And this error has a resolution saying "Perhaps you missed a letter?"
    And the error is printed
    Then the output on the screen should be
    """
    Error   : Example error.

    Troubleshooting:
    - This problem needs a longer description
    Ideally this would be a long message.
    - Perhaps you missed a letter?
    """

  Scenario: Generating and printing fatal errors
    Given a fatal error "A more serious error." is encountered
    And this error happens inside of file "/path/to/file/name.php" at line "123"
    When the error is printed
    Then the output on the screen should be
    """
    Failure : A more serious error.
    Location: /path/to/file/name.php:123
    """
