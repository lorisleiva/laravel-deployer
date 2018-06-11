<?php

namespace Lorisleiva\LaravelDeployer\Test\Unit;

use Lorisleiva\LaravelDeployer\Commands\BaseCommand;
use Lorisleiva\LaravelDeployer\Test\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ParseParametersTest extends TestCase
{
    /** @test */
    function it_should_allow_using_the_ansi_flag()
    {
        $params = $this->simulateAndParseParameters('dummyTask --ansi');

        $this->assertContains('--ansi', $params);
        $this->assertNotContains('--no-ansi', $params);
    }

    /** @test */
    function if_should_allow_using_the_no_ainsi_flag()
    {
        $params = $this->simulateAndParseParameters('dummyTask --no-ansi');

        $this->assertContains('--no-ansi', $params);
        $this->assertNotContains('--ansi', $params);
    }

    /** @test */
    function it_should_conserve_existing_arguments()
    {
        $params = $this->simulateAndParseParameters('dummyTask 4 prod');

        $this->assertContains('dummyTask 4 prod', $params);
    }

    /** @test */
    function it_should_not_conserve_empty_optional_arguments()
    {
        $params = $this->simulateAndParseParameters('dummyTask');

        // If optional argument was kept, it would add an extra space.
        $this->assertNotContains('  ', $params);
        $this->assertContains('dummyTask 3', $params);
    }

    /** @test */
    function it_should_conserve_boolean_options_and_value_options()
    {
        $params = $this->simulateAndParseParameters(
            "dummyTask 
                -pl3 
                --no-hooks 
                --log='./custom.log'
                --roles=app
                --hosts=dev.example.com:example.com
                -f './custom/file.php'
                --tag=v1.2.1
                --revision=c3110a562339a20eaa4c99e
                --branch=develop
            "
        );

        $this->assertContains("--parallel", $params);
        $this->assertContains("--limit=3", $params);
        $this->assertContains("--no-hooks", $params);
        $this->assertContains("--log='./custom.log'", $params);
        $this->assertContains("--roles=app", $params);
        $this->assertContains("--hosts='dev.example.com:example.com'", $params);
        $this->assertContains("--file='./custom/file.php'", $params);
        $this->assertContains("--tag='v1.2.1'", $params);
        $this->assertContains("--revision=c3110a562339a20eaa4c99e", $params);
        $this->assertContains("--branch=develop", $params);
    }

    /** @test */
    function it_should_conserve_array_options()
    {
        $params = $this->simulateAndParseParameters(
            "dummyTask -o ssh_multiplexing=true -o branch=develop"
        );

        $this->assertContains("--option='ssh_multiplexing=true'", $params);
        $this->assertContains("--option='branch=develop'", $params);
    }

    /** @test */
    function it_should_conserve_verbose_levels()
    {
        $params = $this->simulateAndParseParameters('dummyTask');
        $paramsV = $this->simulateAndParseParameters('dummyTask', '-v');
        $paramsVV = $this->simulateAndParseParameters('dummyTask', '-vv');
        $paramsVVV = $this->simulateAndParseParameters('dummyTask', '-vvv');

        $this->assertNotContains("-v", $params);

        $this->assertContains("-v", $paramsV);
        $this->assertNotContains("-vv", $paramsV);
        $this->assertNotContains("--verbose", $paramsV);

        $this->assertContains("-vv", $paramsVV);
        $this->assertNotContains("-vvv", $paramsVV);
        
        $this->assertContains("-vvv", $paramsVVV);
    }

    protected function simulateAndParseParameters($commandAsString, $verbosity = null)
    {
        $command = new DummyCommand;
        $command->setLaravel(app());
        $command->run(
            new StringInput($commandAsString),
            new ConsoleOutput($this->parseVerbosity($verbosity))
        );

        return $command->getParametersAsString();
    }

    protected function parseVerbosity($verbosity)
    {
        switch ($verbosity) {
            case '-q':
                return OutputInterface::VERBOSITY_QUIET;
            case '-v':
                return OutputInterface::VERBOSITY_VERBOSE;
            case '-vv':
                return OutputInterface::VERBOSITY_VERY_VERBOSE;
            case '-vvv':
                return OutputInterface::VERBOSITY_DEBUG;
            default:
                return OutputInterface::VERBOSITY_NORMAL;
        }
    }
}

class DummyCommand extends BaseCommand
{
    protected $signature = 'deploy:dummy {task} {repeat=3} {stage?} {--ansi} {--no-ansi}';

    public function handle()
    {
        //
    }
}
