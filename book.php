<?php
	require 'func.php';

	define ("HEADER", '<!DOCTYPE html>
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
	<article>');

	define ("FOOTER", '
	</article>
	</body>
	</html>');
	
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

	ob_start();	
	echo HEADER;

	///////////////////////////////テスト用////////////////////
	$isTest = false;
	$testWordCount = 1000;
	$testI = 0; 
	///////////////////////////////テスト用ここまで////////////////////
	$separators = array(":","+");
	//ここから表示部
		
	$isFirstTry = true;
	foreach($arrSort as $entryId => $singleArrSort){
		$singleEntry = $json["words"][$entryId];
		$firstLetter = mb_strtolower(mb_substr(deleteNonIdyerinCharacters($singleEntry["entry"]["form"]),0,1));
		
		if (!$isFirstTry){
			$previousFirstLetter = mb_strtolower(mb_substr(deleteNonIdyerinCharacters($json["words"][$previousEntryId]["entry"]["form"]),0,1));
			
			if ( $previousFirstLetter !== $firstLetter){
				echo FOOTER;
				$out = ob_get_clean();
				file_put_contents( $previousFirstLetter.'.html', $out );
				
				ob_start();
				echo HEADER;
				echo '<h1 class="edge" id="', $firstLetter, '">', mb_strtoupper($firstLetter), '</h1>';
			}
		}else{
			echo '<h1 class="edge" id="', $firstLetter, '">', mb_strtoupper($firstLetter), '</h1>';
			$isFirstTry = false;
		}
		
		
		echo '<ul class="wordEntry">';
		echo '<li class="wordForm">', $singleEntry["entry"]["form"], '</li>';
		
		if ($isTest){
			echo '<li class=pronounciation>test', $singleEntry["entry"]["form"], '</li>';
		}else{
			echo '<li class="pronounciation">', akrantiain($singleEntry["entry"]["form"]), '</li>';
		}

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
				$isIdz = startsWith($singleTranslationForm,"=");
				if ($isIdz){
					echo '<span class="idz">';
				}
				echo $singleTranslationForm;
				if ($singleTranslationForm !== end($singleTranslation["forms"])){
					//最後のとき以外に「、」を追加
					echo '、';
				}
				if ($isIdz){
					echo '</span>';
				}
			}
			echo '</li>';
		}
		
		foreach ($singleEntry["contents"] as $singleContent){
			echo '<li class="wordContents">';
			echo '<span class="wordContentTitle">' , $singleContent["title"] , '</span>';

			if ($singleContent["title"] !== "語源"){
				$singleContent["text"] = preg_replace('/(英|独|仏|露|ドイツ|フランス|ロシア)語の([A-z]+)/u', '$1語の<span class="noIdz">$2</span>', $singleContent["text"]);
				echo $singleContent["text"];
			}else{
				$singleContent["text"] = preg_split ('/(i\..:)|(i:)|([:\/>+|])/u', $singleContent["text"], -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
				foreach ($singleContent["text"] as $index => $singleContentText){
					if($singleContentText === ":"){
						echo '<span class="noIdzBothSide etymology">:</span>';
					}elseif($singleContentText === "/"){
						echo '<span class="noIdzLeftSide etyomology">/</span>';
					}elseif(preg_match('/(i\..:)|(i:)/u', $singleContentText) === 1){
						echo '<span class="noIdz etymology">';
						echo $singleContentText;
						echo '</span>';
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
		echo '</ul>';
		
		$previousEntryId = $entryId;
		///////////////////////////////テスト用////////////////////
		if ($isTest){
			$testI++;
			if($testI === $testWordCount) {
				break;
			}
		}
		///////////////////////////////テスト用ここまで////////////////////
	}

	echo FOOTER;

	$out = ob_get_clean();
	file_put_contents( $firstLetter.'.html', $out );	

	echo "Success";
	exit(0);

?>