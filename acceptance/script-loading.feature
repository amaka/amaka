Feature: Amaka script loading mechanism
  In order to run a task Amaka first needs to load a script
  As a developer
  I want to unserstand how Amaka behaves when loading scripts

  Background:
    Given amaka executable is in "bin/amaka"
    And the current working directory is "%system.temp%"

    Scenario: -f option provided, file available, default script available
      Given the amaka script "File.amk" is available
      And the amaka script "Amkfile" is available
      When I run amaka with arguments "-f File.amk"
      Then the output on the screen should match "@(Loaded '([^']*)File.amk')@s"

    Scenario: -f option provided, file available, default script not available
      Given the amaka script "File.amk" is available
      And the amaka script "Amkfile" is not available
      When I run amaka with arguments "-f File.amk"
      Then the output on the screen should match "@(Loaded '([^']*)File.amk')@s"

    Scenario: -f option provided, file not available, default script available
      Given the amaka script "BogusFile.amk" is not available
      And the amaka script "Amkfile" is available
      When I run amaka with arguments "-f BogusFile.amk"
      Then the output on the screen should match "@Amaka script @s"

    Scenario: -f option provided, file not available, default script not available
      Given the amaka script "BogusFile.amk" is not available
      And the amaka script "Amkfile" is not available
      When I run amaka with arguments "-f BogusFile.amk"
      Then the output on the screen should match "@Amaka script '([^']*)BogusFile.amk' not found@s"

    Scenario: -f option not provided, default script available
      Given the amaka script "Amkfile" is available
      And the amaka script "BogusFile.amk" is not available
      When I run amaka with arguments "-f BogusFile.amk"
      Then the output on the screen should match "@Amaka script '([^']*)BogusFile.amk' not found@s"

    Scenario: -f option not provided, default script not available
      Given the amaka script "Amkfile" is not available
      When I run amaka with arguments ""
      Then the output on the screen should match "@(No amaka scripts to load found)@s"
      And the output on the screen should match "@(Generate a default amaka script using)@s"
