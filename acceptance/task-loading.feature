Feature: Amaka task loading
  Amaka need to be able to find a task inside of a script so it can run it
  As a developer
  I want to unserstand how Amaka behaves when running a task from a script

  Background:
    Given amaka executable is in "bin/amaka"
    And the current working directory is "%system.temp%"
    And the amaka script "EmptyScript.amk" contains
    """
    <?php return [];
    """
    And the amaka script "ScriptWithDefault.amk" contains
    """
    <?php return [
        $amaka->task(':example-task', function() {
            echo 'EXAMPLE';
        }),
        $amaka->task(':default', function() {
            echo 'DEFAULT';
        })];
    """

    And the amaka script "ScriptNoDefault.amk" contains
    """
    <?php return [
        $amaka->task(':example-task', function() {
            echo 'EXAMPLE';
        })];
    """

    Scenario: task is provided, script is empty
     When I run amaka with arguments "-f EmptyScript.amk :a-task"
     Then the output on the screen should contain "No task to run"

    Scenario: task is not provided, script is empty
     When I run amaka with arguments "-f EmptyScript.amk"
     Then the output on the screen should contain "No task to run"

    Scenario: task is provided, task can be found, there is no default task
     When I run amaka with arguments "-f ScriptNoDefault.amk :example-task"
     Then the output on the screen should contain "(Task ':example-task')"
     Then the output on the screen should contain "EXAMPLE"

    Scenario: task is provided, task can be found, there is default task
     When I run amaka with arguments "-f ScriptWithDefault.amk :example-task"
     Then the output on the screen should contain "(Task ':example-task')"
     And the output on the screen should contain "EXAMPLE"
     And the output on the screen should not contain "DEFAULT"

    Scenario: task is provided, task can not be found, there is default task
     When I run amaka with arguments "-f ScriptWithDefault.amk :foo-task"
     Then the output on the screen should contain "was not found"

    Scenario: task is provided, task can not be found, there is no default task
     When I run amaka with arguments "-f ScriptNoDefault.amk :foo-task"
     Then the output on the screen should contain "was not found"

    Scenario: task is not provided, there is no default task
     When I run amaka with arguments "-f ScriptNoDefault.amk"
     Then the output on the screen should contain "No task to run"

    Scenario: task is not provided, there is default task
     When I run amaka with arguments "-f ScriptWithDefault.amk"
     Then the output on the screen should contain "DEFAULT"
