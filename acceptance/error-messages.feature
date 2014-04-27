Feature: Error Message
  As a developer
  I use Amaka in the wrong way
  So I expect to be told what to do, and what I did wrong

  Background:
    Given amaka executable is in "bin/amaka"
    And the current working directory is "%system.temp%"

    Scenario: I write an amaka script with a task that depends on itself and try to run it
      Given the amaka script "Cycle.amk" contains
      """
          <?php return [$amaka->task(':a')->dependsOn(':a')];
      """
      When I run amaka with arguments "-f Cycle.amk :a"
      Then the output on the screen should contain "Cycle detected"
