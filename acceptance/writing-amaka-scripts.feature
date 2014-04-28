Feature: Writing Amaka Scripts
  As a Developer
  I want to use the Amaka DSL to do some simple automation

  Background:
    Given amaka executable is in "bin/amaka"
    And the current working directory is "%system.temp%"

    Scenario: Printing 'hello, world' with a task
      Given the amaka script "Amkfile" contains
      """
      <?php return [
          $amaka->task("hello", function() {
              echo "hello, world\n";
          })
      ];
      """
      When I run amaka with arguments "hello"
      Then the output on the screen should contain "hello, world"

    Scenario: Printing 'hello, world' using two tasks linked with 'dependsOn()'
      Given the amaka script "Amkfile" contains
      """
      <?php return [
          $amaka->task("hello-world", function() {
              echo "world\n";
          })->dependsOn("hello")
          ,
          $amaka->task("hello", function() {
              echo "hello, ";
          })
      ];
      """
      When I run amaka with arguments "hello-world"
      Then the output on the screen should contain "hello, world"

    Scenario: Using the $amaka variable to access the script scope operations
      Given the amaka script "Amkfile" contains
      """
      <?php return [
          $amaka->task(":default", function() use ($amaka) {
              echo get_class($amaka);
          })
      ];
      """
      When I run amaka without arguments
      Then the output on the screen should contain "ScriptScope"

    Scenario: Using the Finder operation to find files
      Given the amaka script "Amkfile" contains
      """
      <?php return [
          $amaka->task(":default", function() use ($amaka, $__fileName) {
              $fileSet = $amaka->finder()
                               ->files()
                               ->ignoreUnreadableDirs()
                               ->name('Amkfile')
                               ->in(__DIR__);

              foreach ($fileSet as $file) {
                  echo 'fileName: ' . $file->getFileName();
              }
          })
      ];
      """
      When I run amaka without arguments
      Then the output on the screen should match "@fileName:(.*)Amkfile@"

    Scenario: Using the $__fileName variable to get the name of the script file
      Given the amaka script "Amkfile" contains
      """
      <?php return [
          $amaka->task("var-example", function() use ($__fileName) {
              echo 'fileName: ' . $__fileName;
          })
      ];
      """
      When I run amaka with arguments "var-example"
      Then the output on the screen should match "@fileName:(.*)Amkfile@"
