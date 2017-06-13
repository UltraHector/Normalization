<?PHP
require_once('app/service/FindAllCK.php');
require_once('app/service/FunctionalID.php');
require_once('app/service/NFTest.php');
require_once('app/service/NormalizeTo2NF.php');
require_once('app/service/NormalizeTo3NF.php');
require_once('app/service/NormalizeToBCNF.php');
require_once('app/service/From1NFTo3NF.php');


header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$attributes = $data["attributes"];
$functionalDependencies = $data["functionalDependencies"];
$funcionalDependencySet = array();
for($index = 0; $index < count($functionalDependencies); $index ++){
	$funcionalDependency = new FD($functionalDependencies[$index]["leftAttributes"], $functionalDependencies[$index]["rightAttributes"]);
	array_push($funcionalDependencySet, $funcionalDependency);
}


switch($_GET["normalizeOption"]){
	case "findCandidateKeys":
		echo json_encode(findAllCK($funcionalDependencySet, $attributes));
		break;
	case "checkNormalForm":
		//echo $attributes;
		$responseData = array();
		$responseData["isBCNF"] = isBCNF($funcionalDependencySet, $attributes);
		$responseData["is3NF"] = is3NF($funcionalDependencySet, $attributes);
		$responseData["is2NF"] = is2NF($funcionalDependencySet, $attributes);
		echo json_encode($responseData);
		break;
	case "findMinimalCover":
		echo json_encode(findMiniCover($funcionalDependencySet));
		break;
	case "normalize2NF":
		echo json_encode(To2NF($funcionalDependencySet, $attributes));
		break;
	case "normalize3NF":
		echo json_encode(To3NF($funcionalDependencySet, $attributes));
		break;
	case "normalizeBCNF":
		echo json_encode(ToBCNF($funcionalDependencySet, $attributes));
		break;
        case "1NFTo3NF":
		echo json_encode(from1NFto3NF($funcionalDependencySet, $attributes));
		break;
	case "loadExample":
		echo json_encode(loadFDExamples());
		break;
}

function loadFDExamples(){
	$dir = 'assets/fdExamples';
	$exampleFiles = scandir($dir);

	$responseData = array();
	$responseData['fdExamples'] = array();
	
	foreach ($exampleFiles as $exampleFile) {
		if(strlen($exampleFile) < 3){
			continue;
		}
		$fdExample = array();
		$fdExample['title'] = $exampleFile;
		$fdExample['attributes'] = array();
		$fdExample['fd'] = array();
		
		$handle = fopen($dir."/".$exampleFile, "r");
		if ($handle) {
			/*
			 * load attributes
			 */
			$line = fgets($handle);
			$attributes = explode(',', $line);
			foreach ($attributes as $attribute) {
				array_push($fdExample['attributes'], trim($attribute, " \t\n\r\0\x0B"));
			}
			/*
			 * load FDs
			 */
			while (($line = fgets($handle)) !== false) {
				$formatFd = array();
				$formatFd['left'] = array();
				$formatFd['right'] = array();
						
				$rawFd = explode('->', $line);
				if(count($rawFd) != 2){
					continue;
				}
				$rawFdLeft = explode(',', $rawFd[0]);
				$rawFdRight = explode(',', $rawFd[1]);

				foreach ($rawFdLeft as $rawFdAttri) {
					array_push($formatFd['left'], trim($rawFdAttri, " \t\n\r\0\x0B"));
				}
				foreach ($rawFdRight as $rawFdAttri) {
					array_push($formatFd['right'], trim($rawFdAttri, " \t\n\r\0\x0B"));
				}
				
				array_push($fdExample['fd'], $formatFd);
			}
			
			array_push($responseData['fdExamples'], $fdExample);
			
			fclose($handle);
		} else {
			// error opening the file.
		}
	}
	
	return $responseData;
} 

