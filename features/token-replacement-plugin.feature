Feature: token replacement
  In order to replace the occurrences of a string in a file
  I have to declare the strings I wish to replace

Scenario: replacing a token in empty file
  Given an empty file
  When I run the token replacement plugin
  Then the file should still be empty

Scenario: replacing single token in a file
  Given a file with the following content
  """
  # Example configuration
  my.directive = <%my.directive%>
  """
  When I bind the value "On" to the token "<%my.directive%>"
  And I run the token replacement plugin
  Then the file should contain
  """
  # Example configuration
  my.directive = On
  """
  And the file should not contain the original token "<%my.directive%>"
