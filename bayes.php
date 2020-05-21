<?php

include './src/isac/project/WordsVocabularyList.php';
include './src/isac/project/NaiveBayes.php';

use Isac\Project\WordsVocabularyList;
use Isac\Project\NaiveBayes;

$wordsGenerator = new WordsVocabularyList();
$bayesAnalyzer = new NaiveBayes($wordsGenerator);

$bayesAnalyzer->training($wordsGenerator::POSITIVE_FILE);
$bayesAnalyzer->training($wordsGenerator::NEGATIVE_FILE);
$bayesAnalyzer->createClassification("source.txt");
$bayesAnalyzer->printResult();