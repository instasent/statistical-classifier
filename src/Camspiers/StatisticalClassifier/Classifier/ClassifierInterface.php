<?php

namespace Camspiers\StatisticalClassifier\Classifier;

interface ClassifierInterface
{
    // public function getSource();
    // public function prepare();
    public function classify($document);
    // public function train($category, $document);
}
