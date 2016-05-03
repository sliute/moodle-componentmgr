<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Command;

use ComponentManager\Console\Argument;
use ComponentManager\Exception\ComponentProjectException;
use ComponentManager\PlatformUtil;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Run a script within a component.
 */
class RunScriptCommand extends AbstractCommand {
    use ComponentAwareCommandTrait;

    /**
     * Help text.
     *
     * @var string
     */
    const HELP = <<<HELP
Executes the specified script for a component.
HELP;

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this
            ->setName('run-script')
            ->setDescription('Executes a script for a component')
            ->setHelp(static::HELP)
            ->setDefinition(new InputDefinition([
                new InputArgument(Argument::ARGUMENT_SCRIPT,
                                  InputArgument::REQUIRED,
                                  Argument::ARGUMENT_SCRIPT_HELP),
            ]));
    }

    /**
     * @override \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $script = $this->getComponentProjectFile()->getScript(
                $input->getArgument(Argument::ARGUMENT_SCRIPT));

        $process = new Process($script, PlatformUtil::workingDirectory());
        $process->run(function($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            throw new ComponentProjectException(
                    $process->getErrorOutput(),
                    ComponentProjectException::CODE_SCRIPT_FAILED);
        }
    }
}