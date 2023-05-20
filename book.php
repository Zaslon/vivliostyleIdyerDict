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
<link rel="stylesheet" type="text/css" href="book.css" >
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/yakuhanjp@3.4.1/dist/css/yakuhanjp-noto.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/yakuhanjp@3.4.1/dist/css/yakuhanmp-noto.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&family=Noto+Serif&family=Noto+Serif+JP&display=swap" rel="stylesheet">
<title>緯日辞典_本文</title>
</head>
<body>
<article>
<?php
	///////////////////////////////テスト用////////////////////
	$isTest = false;
	
	if ($isTest){
		$testI = 0; 
	}
	///////////////////////////////テスト用ここまで////////////////////
	$separators = array(":","+");
	//ここから表示部
		
	$firstTry = true;
	foreach($arrSort as $entryId => $singleArrSort){
		$singleEntry = $json["words"][$entryId];
		$firstLetter = mb_strtolower(mb_substr(deleteNonIdyerinCharacters($singleEntry["entry"]["form"]),0,1));
		
		if ($firstTry === false){
			$previousFirstLetter = mb_strtolower(mb_substr(deleteNonIdyerinCharacters($json["words"][$previousEntryId]["entry"]["form"]),0,1));
			
			if ( $previousFirstLetter !== $firstLetter){
				echo '<h1 class="edge" id="', $firstLetter, '">', mb_strtoupper($firstLetter), '</h1>';
			}
		}else{
			echo '<h1 class="edge" id="', $firstLetter, '">', mb_strtoupper($firstLetter), '</h1>';
			$firstTry = false;
		}
		
		
		echo '<ul class="wordEntry">';
		echo '<li class="wordForm">', $singleEntry["entry"]["form"], '</li>';
		
		$previousTitle = '';
		$isNumber = false;
		foreach ($singleEntry["translations"] as $index => $singleTranslation){
			if ($index === 0){
				echo '<span class="wordTitle">' , $singleTranslation["title"] , '</span>';
				if (count($singleEntry["translations"]) !== 1 && $singleEntry["translations"][1]["title"] === $singleTranslation["title"]){
					$isNumber = true;
				}
			}else{
				if ($previousTitle !== $singleTranslation["title"]) {
					echo '<span class="wordTitle">' , $singleTranslation["title"] , '</span>';
					$isNumber = false;
				}else{
					$isNumber = true;
				}
			}
			$previousTitle = $singleTranslation["title"];
			if ($isNumber){
				echo '<li class="wordTransWithNumber">';
			}else{
				echo '<li class="wordTrans">';
			}
			foreach ($singleTranslation["forms"] as $singleTranslationForm){
				echo $singleTranslationForm;
				if ($singleTranslationForm !== end($singleTranslation["forms"])){
					//最後のとき以外に「、」を追加
					echo '、';
				}
			}
			echo '</li>';
		}
		
		foreach ($singleEntry["contents"] as $singleContent){
			echo '<li class="wordContents">';
			echo '<span class="wordContentTitle">' , $singleContent["title"] , '</span>';

			if ($singleContent["title"] !== "語源"){
				echo hyphenate($singleContent["text"], "<wbr>", $separators);
			}else{
				$singleContent["text"] = preg_split ('/(i\..:)|(i:)|([:\/>+|])/u', $singleContent["text"], -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
				foreach ($singleContent["text"] as $index => $singleContentText){
					if(preg_match('/(i\..:)|(i:)/u', $singleContentText) === 1){
						echo '<span class="etymology">';
						echo $singleContentText;
						echo '</span>';
					}elseif($singleContentText === ":"){
						echo '<span class="noidz etymology">:</span>';
					}else{
					echo '<span class="etymology">';
					echo hyphenate($singleContentText, "<wbr>", $separators);
					echo '</span>';
					}
				}
			}
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
			$testI++;
			if($testI === 500) {
				break;
			}
		}
		///////////////////////////////テスト用ここまで////////////////////
	}

?>
</article>
</body>
</html>