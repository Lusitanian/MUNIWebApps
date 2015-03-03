<?php
require_once __DIR__.'/../vendor/autoload.php';
use MUNI\Templating;
use MUNI\Logger;
use Symfony\Component\HttpFoundation\Response;

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

$app->get('/view-paper/{id}', function($id) use($app) {
	$id = intval($id);
	if($id <= 0) { return 'nah'; }
	$paperFile = $app['db']->query('SELECT file FROM papers WHERE id=' . $id)->fetch()['file'];
	$extension = strtolower(substr($paperFile, strrpos($paperFile, '.') + 1));
	$mimeType = '';
	switch($extension) {
		case 'docx':
			$mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
			break;
		case 'doc':
			$mimeType = 'application/msword';
			break;
		case 'pdf':
			$mimeType = 'application/pdf';
			break;
		case 'rtf':
			$mimeType = 'application/rtf';
			break;
	}

	$fileData = file_get_contents($app['filedirectory'] . DIRECTORY_SEPARATOR . $paperFile);
	$response = new Response(); 
	$response->headers->set('Content-disposition', 'attachment; fileName=' . $paperFile);
	$response->headers->set('Content-type', $mimeType);
	$response->setContent($fileData);
	return $response;
});

$app->get('/list-papers', function() use($app) {
	$temp = $app['db']->query('SELECT id, file, delegate, hs, committee, position FROM papers ORDER BY committee, position ASC')->fetchAll(PDO::FETCH_ASSOC);
	$indexed = [];
	foreach($temp as $val) {
		if(!isset($indexed[$val['committee']])) {
			$indexed[$val['committee']] = [];
		}
		$indexed[$val['committee']][] = $val;
	}
	if(isset($indexed['WCW'])) {
		$indexed['CSW'] = $indexed['WCW'];
		unset($indexed['WCW']);
	}
	
	$preferredOrder = ['DISEC', 'ECOFIN', 'SOCHUM', 'SPECPOL', 'UNHRC', 'CSW', 'ICC', 'IPD', 'Other'];
	uksort($indexed, function($a, $b) use($preferredOrder) {
		$keyA = array_search($a, $preferredOrder, true);
		$keyB = array_search($b, $preferredOrder, true);

		if($keyA === false || $keyB === false) {
			return 0;
		}

		if($keyA > $keyB) {
			return 1;
		} elseif($keyB > $keyA) {
			return -1;
		}

		return 0;
	});
	return $app['templating']->render('listpapers.tpl', ['papers' => $indexed]);
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
