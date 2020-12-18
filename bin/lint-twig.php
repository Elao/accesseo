#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Bridge\Twig\Command\LintCommand;
use Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$twig = new Environment(new FilesystemLoader());
$twig->addExtension(new WebProfilerExtension(new HtmlDumper()));

(new Application('twig/lint'))
    ->add(new LintCommand($twig))
    ->getApplication()
    ->setDefaultCommand('lint:twig', true)
    ->run();
