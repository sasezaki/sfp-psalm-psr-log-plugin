Feature: Logger

  Background:
    Given I have the following config
      """
      <?xml version="1.0"?>
      <psalm totallyTyped="true">
        <projectFiles>
          <directory name="."/>
          <ignoreFiles> <directory name="../../vendor"/> </ignoreFiles>
        </projectFiles>
        <plugins>
          <pluginClass class="Sfp\Psalm\PsrLogPlugin\Plugin"/>
        </plugins>
      </psalm>
      """
    And I have the following code preamble
      """
      <?php
      use Psr\Log\LoggerInterface;
      use Psr\Log\AbstractLogger;
      use Psr\Log\LoggerTrait;

      /**
       * @psalm-suppress InvalidReturnType
       * @return LoggerInterface
       */
      function impl_interface() {}

      /**
       * @psalm-suppress InvalidReturnType
       * @return AbstractLogger
       */
      function concrete_abstract() {}

      """

  Scenario: `exception` key is actually an Exception Object AS per LoggerInterface
    Given I have the following code
      """
      impl_interface()->emergency("message", ['exception' => 'foo']);
      impl_interface()->alert("message", ['exception' => 'foo']);
      impl_interface()->critical("message", ['exception' => 'foo']);
      impl_interface()->error("message", ['exception' => 'foo']);
      impl_interface()->warning("message", ['exception' => 'foo']);
      impl_interface()->notice("message", ['exception' => 'foo']);
      impl_interface()->info("message", ['exception' => 'foo']);
      impl_interface()->debug("message", ['exception' => 'foo']);
      """
    When I run Psalm
    Then I see these errors
      | Type            | Message                                                                                                                      |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::emergency expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::alert expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::critical expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::error expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::warning expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::notice expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::info expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\LoggerInterface::debug expects array{exception?: Exception}, array{exception: string(foo)} provided |
    And I see no other errors

  Scenario: `exception` key is actually an Exception Object AS per AbstractLogger
    Given I have the following code
      """
      concrete_abstract()->emergency("message", ['exception' => 'foo']);
      concrete_abstract()->alert("message", ['exception' => 'foo']);
      concrete_abstract()->critical("message", ['exception' => 'foo']);
      concrete_abstract()->error("message", ['exception' => 'foo']);
      concrete_abstract()->warning("message", ['exception' => 'foo']);
      concrete_abstract()->notice("message", ['exception' => 'foo']);
      concrete_abstract()->info("message", ['exception' => 'foo']);
      concrete_abstract()->debug("message", ['exception' => 'foo']);
      """
    When I run Psalm
    Then I see these errors
      | Type            | Message                                                                                                                      |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::emergency expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::alert expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::critical expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::error expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::warning expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::notice expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::info expects array{exception?: Exception}, array{exception: string(foo)} provided |
      | InvalidArgument | Argument 2 of Psr\Log\AbstractLogger::debug expects array{exception?: Exception}, array{exception: string(foo)} provided |
    And I see no other errors
