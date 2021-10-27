<?php
	require 'func.php';
	
	//json読み込み
	$fname = 'idyer.json';
	$json = file_get_contents($fname);
	$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
	$json = json_decode($json,true);
	
	//ソート
	$arrSort = array();
	foreach($json["words"] as $singleEntry ){
		$arrSort[] = $singleEntry["entry"]["form"]; //キーはentryId
	}
	uasort($arrSort , "HKSCmp");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=yes" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400&display=swap');
</style>
<link rel="stylesheet" type="text/css" href="book.css" >
<title>緯日辞典 本文</title>
</head>
<body>
<article>
<?php
	///////////////////////////////テスト用////////////////////
	$isTest = true;
	
	if ($isTest){
		$i = 0; 
	}
	///////////////////////////////テスト用ここまで////////////////////
	$separators = array(":","+");
	//ここから表示部
	
	echo '<div class="edge" id="e"></div>';
	echo '<div class="edge" id="a"></div>';
	
	$firstTry = true;
	foreach($arrSort as $entryId => $singleArrSort){
		$singleEntry = $json["words"][$entryId];
		$firstLetter = mb_strtolower(mb_substr(deleteNonIdyerinCharacters($singleEntry["entry"]["form"]),0,1));
		
		if ($firstTry === false){
			$previousFirstLetter = mb_strtolower(mb_substr(deleteNonIdyerinCharacters($json["words"][$previousEntryId]["entry"]["form"]),0,1));
			
			if ( $previousFirstLetter !== $firstLetter){
				echo '<h1 class="', $firstLetter, '">', mb_strtoupper($firstLetter), '</h1>';
			}
		}else{
			echo '<h1 class="edge" id=', $firstLetter, '">', mb_strtoupper($firstLetter), '</h1>';
			$firstTry = false;
		}
		
		
		echo '<ul class="wordEntry">';
		echo '<li class="wordForm">', $singleEntry["entry"]["form"], '</li>';
		
		$previousTitle = '';
		foreach ($singleEntry["translations"] as $index => $singleTranslation){
			if ($index === 0){
				echo '<span class="wordTitle">' , $singleTranslation["title"] , '</span>';
			}else{
				if ($previousTitle !== $singleTranslation["title"]) {
					echo '<span class="wordTitle">' , $singleTranslation["title"] , '</span>';
				}
			}
			$previousTitle = $singleTranslation["title"];
			echo '<li class="wordTrans">';
			foreach ($singleTranslation["forms"] as $singleTranslationForm){
				echo $singleTranslationForm;
				if ($singleTranslationForm !== end($singleTranslation["forms"])){
					//最後のとき以外に「、」を追加
					echo '、';
				}
			}
		}
		
		foreach ($singleEntry["contents"] as $singleContent){
			echo '<li class="wordContents">';
			echo '<span class="wordContentTitle">' , $singleContent["title"] , '</span>';
			echo hyphenate($singleContent["text"], "<wbr>", $separators);
			echo '</li>';
		}
		$relationTitles = array();
		foreach ($singleEntry["relations"] as $singleRelation){
			if (array_search($singleRelation["title"],$relationTitles) === false){
				echo '<li class="wordRelation"><span class="wordRelation">' , $singleRelation["title"] , '</span>';
				$relationTitles[] = $singleRelation["title"];
			}else{
				echo ",";
			}
			echo hyphenate($singleRelation["entry"]["form"], "<wbr>", $separators) ;
		}
		echo '</li>';
		echo '</ul>';
		
		$previousEntryId = $entryId;
		///////////////////////////////テスト用////////////////////
		if ($isTest){
			$i++;
			if($i === 500) {
				break;
			}
		}
		///////////////////////////////テスト用ここまで////////////////////
	}

?>
</article>
</body>
</html>