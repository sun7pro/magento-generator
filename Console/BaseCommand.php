<?php

namespace Sun7Pro\Generator\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    protected function echo($echoTag, OutputInterface $output, $message, $replaces = [])
    {
        return $output->writeln(vsprintf("<$echoTag>$message</$echoTag>", $replaces));
    }

    protected function writeComment(OutputInterface $output, $message, $replaces = [])
    {
        return $this->echo('comment', $output, $message, $replaces);
    }

    protected function writeInfo(OutputInterface $output, $message, $replaces = [])
    {
        return $this->echo('info', $output, $message, $replaces);
    }

    protected function writeError(OutputInterface $output, $message, $replaces = [])
    {
        return $this->echo('error', $output, $message, $replaces);
    }
}
