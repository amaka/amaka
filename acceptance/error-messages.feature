@ui @error-reporting
Feature: Error Messages
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
