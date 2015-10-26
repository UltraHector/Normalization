<?PHP
require('service/NormalizeService.php');

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
		$responseData["is3NF"] = is3NF($funcionalDependencySet, $attributes, true);
		$responseData["is2NF"] = is2NF($funcionalDependencySet, $attributes, true);
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
}
