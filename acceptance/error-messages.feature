@ui @error-handling @error-reporting
Feature: Amaka Error Messages to the User
  As a developer
  I use Amaka in the wrong way
  So I expect to be told what to do, and what I did wrong

  Background:
    Given amaka executable is in "bin/amaka"
    And the current working directory is "%system.temp%"

    Scenario: Writing a task that directly depends on itself results in an error
      Given the amaka script "DirectCycle.amk" contains
      """
          <?php return [$amaka->task('TASK_A')->dependsOn('TASK_A')];
      """
      When I run amaka with arguments "-f DirectCycle.amk TASK_A"
      Then the output on the screen should contain "Cycle detected"

    Scenario: Writing a task that indirectly depends on itself results in an error
      Given the amaka script "IndirectCycle.amk" contains
      """
          <?php return [
              $amaka->task('TASK_A')->dependsOn('TASK_B')
              ,
              $amaka->task('TASK_B')->dependsOn('TASK_C')
              ,
              $amaka->task('TASK_C')->dependsOn('TASK_A')
          ];
      """
      When I run amaka with arguments "-f IndirectCycle.amk TASK_A"
      Then the output on the screen should contain "Cycle detected"

    Scenario: Writing a task that depends on another task which wasn't declared
      Given the amaka script "DependsOnUndeclaredTask.amk" contains
      """
          <?php return [
              $amaka->task('TASK_A')->dependsOn('TASK_Z')
          ];
      """
      When I run amaka with arguments "-f DependsOnUndeclaredTask.amk TASK_A"
      Then the output on the screen should contain "The named invocable 'TASK_Z' could not be retrieved from the script definition"

    Scenario: Writing a task which uses an undefined operation
      Given the amaka script "UndefinedOperation.amk" contains
      """
          <?php return [
              $amaka->task('TASK_A', function() use ($amaka) {
                  $amaka->bogusOperation();
              })
          ];
      """
      When I run amaka with arguments "-f UndefinedOperation.amk TASK_A"
      Then the output on the screen should contain "Unknown method 'bogusOperation'"


    Scenario: Writing a task with a PHP parse error in the code fragment
      # We're asserting that the PHP error is left untouched by Amaka.
      Given the amaka script "PHPParseError.amk" contains
      """
          <?php return [
              $amaka->task('TASK_A', function() { hello world });
          ];
      """
      When I run amaka with arguments "-f PHPParseError.amk TASK_A"
      Then the output on the screen should contain "Parse error"
