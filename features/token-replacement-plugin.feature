@plugins @token-replacement
Feature: token replacement

  Scenario: replacing a token in empty file
    Given an empty file
    When I run the token replacement plugin
    Then the file should still be empty

  Scenario: replacing only one token in a file
    Given a file with the following content
    """
    # Example configuration
    my.directive = <%my.directive%>
    """
    And the value "On" is bound to the token "<%my.directive%>"
    When I run the token replacement plugin
    Then the file should contain
    """
    # Example configuration
    my.directive = On
    """
    And the file should not contain the original token "<%my.directive%>"

  Scenario: replacing single token in a file
    Given a file with the following content
    """
    # Example configuration
    my.directive = <%my.directive%>
    """
    And the value "On" is bound to the token "<%my.directive%>"
    When I run the token replacement plugin
    Then the file should contain
    """
    # Example configuration
    my.directive = On
    """
    And the file should not contain the original token "<%my.directive%>"


  Scenario: replacing tokens from a template into a new file
    Given a file with the following content
    """
    # Example configuration
    my.directive = <%my.directive%>
    """
    And the value "On" is bound to the token "<%my.directive%>"
    When the token replacement plugin is run
    Then the target file should be created
    And the destination file should contain the replaced tokens
    """
    # Example configuration
    my.directive = On
    """

  Scenario: replacing single token in a file
    Given a file with the following content
    """
    # Example configuration
    my.directive = <%my.directive%>
    """
    And the value "On" is bound to the token "<%my.directive%>"
    When I run the token replacement plugin
    Then the file should not contain the original token "<%my.directive%>"
    And the file should contain
    """
    # Example configuration
    my.directive = On
    """

  Scenario: replacing multiple tokens in a file
    Given a file with the following content
    """
    # Example configuration
    one.directive = <%one.directive%>
    other.directive = <%other.directive%>
    """
    And the value "On" is bound to the token "<%one.directive%>"
    And the value "Off" is bound to the token "<%other.directive%>"
    When I run the token replacement plugin
    And the file should contain
    """
    # Example configuration
    one.directive = On
    other.directive = Off
    """

  Scenario: applying replaceInto to a SplFileInfo object
    Given a file with the following content
    """
    {greeting} World!
    """
    And the value "Hello" is bound to the token "{greeting}"
    When passing the SplFileInfo object to token replacement plugin
    Then the file should contain
    """
    Hello World!
    """

  Scenario: applying replaceInto to the Finder plugin
    Given the value "Hello" is bound to the token "{greeting}"
    And a file called "amaka.hello.txt" with the following content
    """
    {greeting} World!
    """
    When passing the Finder "amaka.*.txt" to the replacement plugin
    Then the file "amaka.hello.txt" should contain
    """
    Hello World!
    """

  Scenario: unbound tokens are preserved after substitution
    Given a file with the following content
    """
    {hello} World!
    """
    When I run the token replacement plugin
    Then the file should contain
    """
    {hello} World!
    """
