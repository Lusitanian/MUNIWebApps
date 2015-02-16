<?php
require_once __DIR__.'/../vendor/autoload.php';
use MUNI\Templating;
use MUNI\Logger;
$app = new Silex\Application();

$app['filedirectory'] = __DIR__.'/../files/';

$app['db'] = function () {
    $pdo = new PDO('sqlite:'.__DIR__.'/../papers.db');
    $sql = "CREATE TABLE IF NOT EXISTS papers (id INTEGER PRIMARY KEY, file TEXT, delegate TEXT, hs TEXT, committee TEXT, position TEXT)";
    $pdo->exec($sql);

    return $pdo;
};

$app['templating'] = function () {
    return new Templating(__DIR__.'/../tpl');
};

$app['log'] = function () {
    return new Logger(__DIR__.'/../log.txt');
};

$app->get('/submit-paper', function () use ($app) {
    // on get, show submit paper tpl
    return $app['templating']->render('submitpaper.tpl');
});

$app->post('/submit-paper', function () use ($app) {
    /** @var $request Request */
    $request = $app['request'];

    $delegateName = $request->request->get('delegatename');
    $highschool = $request->request->get('highschool');
    $committee = $request->request->get('committee');
    $position = $request->request->get('position');
    $paper = $request->files->get('paper');

    $validCommittees = ['ECOFIN', 'DISEC', 'SOCHUM', 'SPECPOL', 'WCW', 'IPD', 'ICC', 'UNHRC', 'Other'];
    $validExtensions = ['pdf', 'doc', 'docx', 'rtf'];

    if (empty($delegateName) || empty($highschool) || empty($committee) || empty($position) || empty($paper)) {
        return $app['templating']->render('submitpaper.tpl', ['formError' => 'You must fill out the entire form.']);
    }

    if (!in_array($committee, $validCommittees)) {
        return $app['templating']->render('submitpaper.tpl', ['formError' => 'You must select a valid committee.']);
    }

    if (!$paper->isValid() || $paper->getSize() > 5243000 || !in_array(strtolower($paper->getClientOriginalExtension()), $validExtensions)) {
        return $app['templating']->render('submitpaper.tpl', ['formError' => 'You must submit a word document or PDF file under 5MB in size.']);
    }

    // validations passed!
    // move file.
    $generatedName = sha1(uniqid(mt_rand(), true)).'.'.$paper->getClientOriginalExtension();
    $paper->move($app['filedirectory'], $generatedName);

    $app['log']->log("Adding position paper with filename '$generatedName' delegate '$delegateName' hs '$highschool' committee '$committee' position '$position'");
    $sql = "INSERT INTO papers (file, delegate, hs, committee, position) VALUES (:file, :delegate, :hs, :committee, :position)";
    $insertStatement = $app['db']->prepare($sql);
    $insertStatement->bindParam(':file', $generatedName);
    $insertStatement->bindParam(':delegate', $delegateName);
    $insertStatement->bindParam(':hs', $highschool);
    $insertStatement->bindParam(':committee', $committee);
    $insertStatement->bindParam(':position', $position);

    $insertStatement->execute();

    return $app['templating']->render('thankyou.tpl', ['delegate' => $delegateName]);
});

$app->run();
