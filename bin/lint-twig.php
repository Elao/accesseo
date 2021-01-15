#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Bridge\Twig\Command\LintCommand;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$twig = new Environment(new FilesystemLoader());
$twig->addExtension(new WebProfilerExtension(new HtmlDumper()));
$twig->addExtension(new RoutingExtension(new UrlGenerator(new RouteCollection(), new RequestContext())));

(new Application('twig/lint'))
    ->add(new LintCommand($twig))
    ->getApplication()
    ->setDefaultCommand('lint:twig', true)
    ->run();
